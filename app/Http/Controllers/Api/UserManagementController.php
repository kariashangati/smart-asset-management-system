<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserManagementService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    protected UserManagementService $userService;

    public function __construct(UserManagementService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Create user with credentials
     * POST /api/users/create-with-credentials
     */
    public function createWithCredentials(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'department_id' => 'nullable|exists:departments,id',
            'phone_number' => 'nullable|string',
            'roles' => 'nullable|array',
            'admin_can_reset_password' => 'nullable|boolean',
        ]);

        $user = $this->userService->createUserWithCredentials(
            $request->validated(),
            $request->admin_can_reset_password ?? true
        );

        return response()->json([
            'success' => true,
            'message' => 'User created and credentials sent',
            'data' => $user,
        ], 201);
    }

    /**
     * Reset user password
     * POST /api/users/{user}/reset-password
     */
    public function resetPassword(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'send_email' => 'nullable|boolean',
        ]);

        $temporaryPassword = $this->userService->resetUserPassword(
            $user,
            $request->send_email ?? true
        );

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully',
            'temporary_password' => $temporaryPassword,
        ]);
    }

    /**
     * Bulk import users from CSV
     * POST /api/users/bulk-import
     */
    public function bulkImport(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        try {
            $file = $request->file('file');
            $rows = Excel::toArray([], $file)[0] ?? [];

            if (empty($rows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CSV file is empty',
                ], 422);
            }

            // Skip header row
            $header = array_shift($rows);
            $usersData = [];

            foreach ($rows as $row) {
                if (empty($row[0])) {
                    continue; // Skip empty rows
                }

                $usersData[] = [
                    'name' => $row[0] ?? '',
                    'email' => $row[1] ?? '',
                    'department_id' => $row[2] ?? null,
                    'phone_number' => $row[3] ?? null,
                    'roles' => isset($row[4]) ? explode(',', $row[4]) : [],
                ];
            }

            $results = $this->userService->bulkImportUsers($usersData);

            return response()->json([
                'success' => true,
                'message' => 'Bulk import completed',
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk user import failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Bulk import failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get bulk import template
     * GET /api/users/bulk-import-template
     */
    public function getBulkImportTemplate(): mixed
    {
        $filename = 'users-import-template-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Email', 'Department ID', 'Phone Number', 'Roles (comma-separated)']);
            fputcsv($file, ['John Doe', 'john@example.com', '1', '+1234567890', 'asset_manager']);
            fputcsv($file, ['Jane Smith', 'jane@example.com', '2', '+0987654321', 'asset_manager,admin']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
