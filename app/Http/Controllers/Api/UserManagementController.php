<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserManagementController extends Controller
{
    public function __construct(private UserManagementService $userService)
    {
    }

    /**
     * Create a user with auto-generated credentials
     * POST /api/users/create-with-credentials
     */
    public function createUser(Request $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'nullable|string',
            'email_notifications_enabled' => 'boolean',
            'push_notifications_enabled' => 'boolean',
        ]);

        try {
            $user = $this->userService->createUserWithCredentials($validated);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully. Credentials sent via email.',
                'data' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Bulk import users from CSV
     * POST /api/users/bulk-import
     */
    public function bulkImportUsers(Request $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        try {
            $file = $request->file('file');
            $path = $file->store('imports', 'local');

            $results = $this->userService->bulkImportUsers(Storage::path($path));

            // Clean up uploaded file
            Storage::delete($path);

            return response()->json([
                'success' => true,
                'message' => "Imported {$results['imported']} users",
                'data' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get CSV import template
     * GET /api/users/bulk-import-template
     */
    public function getImportTemplate(): JsonResponse
    {
        $template = $this->userService->generateCsvTemplate();

        return response()->json([
            'success' => true,
            'data' => [
                'template' => $template,
                'headers' => ['name', 'email', 'department', 'role', 'email_notifications'],
                'example_row' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'department' => 'IT Department',
                    'role' => 'admin',
                    'email_notifications' => 'yes',
                ],
            ],
        ]);
    }

    /**
     * Regenerate password for user
     * POST /api/users/{user}/reset-password
     */
    public function regeneratePassword(User $user): JsonResponse
    {
        $this->authorize('update', $user);

        try {
            $this->userService->regeneratePassword($user);

            return response()->json([
                'success' => true,
                'message' => 'Password regenerated and sent via email.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
