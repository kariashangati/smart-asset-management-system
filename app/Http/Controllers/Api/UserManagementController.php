<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Notifications\UserCredentialsNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class UserManagementController extends Controller
{
    /**
     * Create single user with generated password
     * POST /api/admin/users/create
     */
    public function createUser(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'required|string|in:admin,asset_manager,viewer',
            'send_credentials' => 'boolean',
            'force_password_reset' => 'boolean',
        ]);

        $password = Str::random(12);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($password),
            'department_id' => $request->department_id,
            'email_verified_at' => now(),
        ]);

        $user->assignRole($request->role);

        // Send credentials via email
        if ($request->send_credentials) {
            $user->notify(new UserCredentialsNotification(
                $user,
                $password,
                $request->force_password_reset ?? false
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => [
                'user' => $user,
                'password' => $request->send_credentials ? null : $password,
            ],
        ], 201);
    }

    /**
     * Bulk import users via CSV
     * POST /api/admin/users/import
     */
    public function bulkImportUsers(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120', // 5MB max
        ]);

        try {
            $importedUsers = [];
            $errors = [];
            $row = 1;

            Excel::load($request->file('file'), function ($reader) use (&$importedUsers, &$errors, &$row) {
                foreach ($reader->toArray() as $sheet) {
                    foreach ($sheet as $cell) {
                        $row++;

                        try {
                            $validator = \Illuminate\Support\Facades\Validator::make($cell, [
                                'name' => 'required|string|max:255',
                                'email' => 'required|email|unique:users,email',
                                'role' => 'required|string|in:admin,asset_manager,viewer',
                                'department_id' => 'nullable|exists:departments,id',
                            ]);

                            if ($validator->fails()) {
                                $errors[] = "Row {$row}: " . implode(', ', $validator->errors()->all());
                                continue;
                            }

                            $password = Str::random(12);
                            $user = User::create([
                                'name' => $cell['name'],
                                'email' => $cell['email'],
                                'password' => bcrypt($password),
                                'department_id' => $cell['department_id'] ?? null,
                                'email_verified_at' => now(),
                            ]);

                            $user->assignRole($cell['role']);

                            // Send credentials
                            $user->notify(new UserCredentialsNotification($user, $password, true));

                            $importedUsers[] = [
                                'name' => $user->name,
                                'email' => $user->email,
                                'role' => $cell['role'],
                            ];
                        } catch (\Exception $e) {
                            $errors[] = "Row {$row}: " . $e->getMessage();
                        }
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Users imported successfully',
                'imported_count' => count($importedUsers),
                'error_count' => count($errors),
                'imported_users' => $importedUsers,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing users: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Regenerate user password
     * POST /api/admin/users/{user}/regenerate-password
     */
    public function regeneratePassword(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'send_email' => 'boolean',
        ]);

        $password = Str::random(12);
        $user->update(['password' => bcrypt($password)]);

        if ($request->send_email) {
            $user->notify(new UserCredentialsNotification($user, $password, true));
        }

        return response()->json([
            'success' => true,
            'message' => 'Password regenerated successfully',
            'password' => $request->send_email ? null : $password,
        ]);
    }
}
