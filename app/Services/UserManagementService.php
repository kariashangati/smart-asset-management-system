<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SendUserCredentialsNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class UserManagementService
{
    /**
     * Create a new user with temporary password and send credentials
     */
    public function createUserWithCredentials(array $data, bool $adminCanResetPassword = true): User
    {
        $temporaryPassword = Str::password(12);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($temporaryPassword),
            'department_id' => $data['department_id'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
        ]);

        // Assign roles
        if (isset($data['roles']) && is_array($data['roles'])) {
            $user->assignRole($data['roles']);
        }

        // Send credentials notification
        Notification::send($user, new SendUserCredentialsNotification(
            $user,
            $temporaryPassword,
            $adminCanResetPassword
        ));

        // Store credential record
        $user->credentials()->create([
            'temporary_password' => Hash::make($temporaryPassword),
            'credentials_sent_at' => now(),
            'sent_to_email' => $user->email,
        ]);

        return $user;
    }

    /**
     * Reset user password as admin
     */
    public function resetUserPassword(User $user, bool $sendEmail = true): string
    {
        $temporaryPassword = Str::password(12);

        $user->update([
            'password' => Hash::make($temporaryPassword),
        ]);

        if ($sendEmail) {
            Notification::send($user, new SendUserCredentialsNotification(
                $user,
                $temporaryPassword,
                true
            ));
        }

        return $temporaryPassword;
    }

    /**
     * Bulk import users from array
     */
    public function bulkImportUsers(array $usersData): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
            'created_users' => [],
        ];

        foreach ($usersData as $index => $userData) {
            try {
                $this->validateUserData($userData);
                $user = $this->createUserWithCredentials($userData);
                $results['success']++;
                $results['created_users'][] = $user->id;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][$index] = $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Validate user data
     */
    private function validateUserData(array $data): void
    {
        if (empty($data['name'])) {
            throw new \Exception('Name is required');
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Valid email is required');
        }
        if (User::where('email', $data['email'])->exists()) {
            throw new \Exception('Email already exists');
        }
    }
}
