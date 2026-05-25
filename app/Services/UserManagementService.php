<?php

namespace App\Services;

use App\Models\Department;
use App\Models\User;
use App\Models\UserCredential;
use App\Notifications\SendUserCredentialsNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserManagementService
{
    /**
     * Create a new user with auto-generated credentials
     */
    public function createUserWithCredentials(array $data): User
    {
        // Validate required fields
        $this->validateUserData($data);

        // Check if user already exists
        if (User::where('email', $data['email'])->exists()) {
            throw ValidationException::withMessages([
                'email' => 'A user with this email already exists.',
            ]);
        }

        // Generate temporary password
        $tempPassword = Str::random(12);

        // Create user
        $user = User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($tempPassword),
            'status'     => 'active',
            'department_id' => $data['department_id'] ?? null,
        ]);

        // Assign role if provided
        if (!empty($data['role'])) {
            $user->assignRole($data['role']);
        }

        // Create user credential record
        $credential = UserCredential::create([
            'user_id'           => $user->id,
            'email'             => $data['email'],
            'password_hash'     => Hash::make($tempPassword),
            'temp_password'     => $tempPassword,
            'status'            => 'active',
            'password_expires_at' => now()->addDays(90),
        ]);

        // Send credentials via email
        $user->notify(new SendUserCredentialsNotification($credential));

        return $user->load('roles', 'department');
    }

    /**
     * Bulk import users from CSV file
     * Uses native PHP CSV parsing — no external package needed
     */
    public function bulkImportUsers(string $filePath): array
    {
        $results = [
            'imported' => 0,
            'failed'   => 0,
            'errors'   => [],
        ];

        if (!file_exists($filePath)) {
            throw ValidationException::withMessages([
                'file' => 'Uploaded file could not be found.',
            ]);
        }

        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            throw ValidationException::withMessages([
                'file' => 'Could not open the CSV file.',
            ]);
        }

        // Read header row
        $headers = fgetcsv($handle);

        if (!$headers) {
            fclose($handle);
            throw ValidationException::withMessages([
                'file' => 'CSV file is empty or has no headers.',
            ]);
        }

        // Normalize headers
        $headers = array_map('trim', array_map('strtolower', $headers));

        $rowIndex = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowIndex++;

            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Map headers to values
            $record = array_combine($headers, array_pad($row, count($headers), ''));

            try {
                $data = [
                    'name'          => trim($record['name'] ?? ''),
                    'email'         => trim($record['email'] ?? ''),
                    'department_id' => $this->getDepartmentId(trim($record['department'] ?? '')),
                    'role'          => trim($record['role'] ?? 'asset_manager'),
                    'email_notifications_enabled' => strtolower(trim($record['email_notifications'] ?? 'yes')) === 'yes',
                ];

                $this->createUserWithCredentials($data);
                $results['imported']++;

            } catch (ValidationException $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'row'     => $rowIndex,
                    'email'   => $record['email'] ?? 'unknown',
                    'message' => implode(', ', array_merge(...array_values($e->errors()))),
                ];
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'row'     => $rowIndex,
                    'email'   => $record['email'] ?? 'unknown',
                    'message' => $e->getMessage(),
                ];
            }
        }

        fclose($handle);

        return $results;
    }

    /**
     * Regenerate password for a specific user
     */
    public function regeneratePassword(User $user): string
    {
        $tempPassword = Str::random(12);

        // Update user password
        $user->update([
            'password' => Hash::make($tempPassword),
        ]);

        // Update or create credential record
        $credential = UserCredential::updateOrCreate(
            ['user_id' => $user->id],
            [
                'email'              => $user->email,
                'password_hash'      => Hash::make($tempPassword),
                'temp_password'      => $tempPassword,
                'status'             => 'active',
                'password_expires_at' => now()->addDays(90),
            ]
        );

        // Send new credentials via email
        $user->notify(new SendUserCredentialsNotification($credential));

        return $tempPassword;
    }

    /**
     * Update password for user (used during password reset flow)
     */
    public function updatePassword(User $user, string $newPassword): bool
    {
        // Update the user's actual password
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        // Update credential record — clear temp password
        UserCredential::updateOrCreate(
            ['user_id' => $user->id],
            [
                'email'              => $user->email,
                'password_hash'      => Hash::make($newPassword),
                'temp_password'      => null,
                'password_changed_at' => now(),
                'status'             => 'active',
            ]
        );

        return true;
    }

    /**
     * Generate a CSV template string for bulk import
     */
    public function generateCsvTemplate(): string
    {
        $headers = ['name', 'email', 'department', 'role', 'email_notifications'];

        $rows = [
            ['John Doe',    'john@example.com',  'IT Department', 'admin',         'yes'],
            ['Jane Smith',  'jane@example.com',  'Operations',    'asset_manager', 'yes'],
            ['Bob Johnson', 'bob@example.com',   'Maintenance',   'asset_manager', 'no'],
        ];

        $csv = implode(',', $headers) . "\n";

        foreach ($rows as $row) {
            $csv .= implode(',', $row) . "\n";
        }

        return $csv;
    }

    /**
     * Validate user data before creation
     */
    private function validateUserData(array $data): void
    {
        $errors = [];

        if (empty(trim($data['name'] ?? ''))) {
            $errors['name'] = 'Name is required.';
        }

        if (empty(trim($data['email'] ?? ''))) {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email address is not valid.';
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Resolve department name to its ID
     */
    private function getDepartmentId(?string $departmentName): ?int
    {
        if (empty($departmentName)) {
            return null;
        }

        $department = Department::where('name', 'like', $departmentName)->first();

        return $department?->id;
    }
}
