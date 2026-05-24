<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function __construct(private UserManagementService $userService)
    {
    }

    /**
     * Send password reset link
     * POST /api/password-reset/request
     */
    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Send reset link
        $status = Password::sendResetLink(['email' => $request->email]);

        return response()->json([
            'success' => $status === Password::RESET_LINK_SENT,
            'message' => trans($status),
        ]);
    }

    /**
     * Verify reset token
     * GET /api/password-reset/verify/{token}
     */
    public function verifyToken(string $token): JsonResponse
    {
        $record = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token',
            ], 401);
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            return response()->json([
                'success' => false,
                'message' => 'Token has expired',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token is valid',
            'data' => ['email' => $record->email],
        ]);
    }

    /**
     * Reset password with token
     * POST /api/password-reset/confirm
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        // Verify token
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token',
            ], 401);
        }

        // Find user
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Update password
        $this->userService->updatePassword($user, $request->password);

        // Delete token
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully',
        ]);
    }
}
