<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserCredential;
use App\Models\Department;
use App\Notifications\SendUserCredentialsNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use League\Csv\Reader;

class UserManagementService
{
    /**
     * Create a new user with credentials
     */
    public function createUserWithCredentials(array $data): User
    {
        // Validate required fields
        $this->validateUserData($data);

        // Check if user already exists
        if (User::where('email', $data['email'])->exists()) {
            throw ValidationException::withMessages(['email' => 'Email already exists']);
        }

        // Create user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'department_id' => $data['department_id'] ?? null,
            'email_notifications_enabled' => $data['email_notifications_enabled'] ?? true,
            'push_notifications_enabled' => $data['push_notifications_enabled'] ?? false,
        ]);

        // Generate temporary password
        $tempPassword = Str::random(12);

        // Create user credential
        $credential = UserCredential::create([
            'user_id' => $user->id,
            'email' => $data['email'],
            'password_hash' => Hash::make($tempPassword),
            'temp_password' => $tempPassword,
            'status' => 'active',
            'password_expires_at' => now()->addDays(90),
        ]);

        // Send credentials via email
        $user->notify(new SendUserCredentialsNotification($credential));

        // Assign role if provided
        if (isset($data['role'])) {
            $user->assignRole($data['role']);
        }

        return $user;
    }

    /**
     * Bulk import users from CSV
     */
    public function bulkImportUsers(string $filePath): array
    {
        $results = [
            'imported' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        try {
            $csv = Reader::createFromPath($filePath, 'r');
            $csv->setHeaderOffset(0);

            $records = $csv->getRecords();

            foreach ($records as $index => $record) {
                try {
                    // Extract row data
                    $data = [
                        'name' => $record['name'] ?? null,
                        'email' => $record['email'] ?? null,
                        'department_id' => $this->getDepartmentId($record['department'] ?? null),
                        'role' => $record['role'] ?? 'user',
                        'email_notifications_enabled' => ($record['email_notifications'] ?? 'yes') === 'yes',
                    ];

                    // Validate record
                    $this->validateUserData($data);

                    // Create user
                    $this->createUserWithCredentials($data);
                    $results['imported']++;
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'row' => $index + 2, // +2 for header and 0-indexing
                        'message' => $e->getMessage(),
                    ];
                }
            }
        } catch (\Exception $e) {
            throw ValidationException::withMessages(['file' => 'Invalid CSV file: ' . $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Regenerate password for user
     */
    public function regeneratePassword(User $user): string
    {
        $tempPassword = Str::random(12);

        $credential = $user->credential;
        if (!$credential) {
            $credential = UserCredential::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'status' => 'active',
            ]);
        }

        $credential->update([
            'password_hash' => Hash::make($tempPassword),
            'temp_password' => $tempPassword,
            'password_expires_at' => now()->addDays(90),
        ]);

        // Send credentials via email
        $user->notify(new SendUserCredentialsNotification($credential));

        return $tempPassword;
    }

    /**
     * Update password for user
     */
    public function updatePassword(User $user, string $newPassword): bool
    {
        $credential = $user->credential;
        if (!$credential) {
            $credential = UserCredential::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'status' => 'active',
            ]);
        }

        $credential->update([
            'password_hash' => Hash::make($newPassword),
            'temp_password' => null,
            'password_changed_at' => now(),
        ]);

        return true;
    }

    /**
     * Validate user data
     */
    private function validateUserData(array $data): void
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required';
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Get department ID by name
     */
    private function getDepartmentId(?string $departmentName): ?int
    {
        if (!$departmentName) {
            return null;
        }

        $department = Department::where('name', $departmentName)->first();
        return $department?->id;
    }

    /**
     * Generate CSV template for bulk import
     */
    public function generateCsvTemplate(): string
    {
        $headers = ['name', 'email', 'department', 'role', 'email_notifications'];
        $sample = [
            ['John Doe', 'john@example.com', 'IT Department', 'admin', 'yes'],
            ['Jane Smith', 'jane@example.com', 'Operations', 'user', 'yes'],
            ['Bob Johnson', 'bob@example.com', 'Maintenance', 'manager', 'no'],
        ];

        $csv = implode(',', $headers) . "\n";
        foreach ($sample as $row) {
            $csv .= implode(',', $row) . "\n";
        }

        return $csv;
    }
}
