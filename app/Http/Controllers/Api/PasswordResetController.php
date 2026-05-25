<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function __construct(private UserManagementService $userService)
    {
    }

    /**
     * Send password reset link to email
     * POST /api/password/forgot
     */
    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check user exists first
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Return success anyway to prevent email enumeration
            return response()->json([
                'success' => true,
                'message' => 'If that email exists in our system, a reset link has been sent.',
            ]);
        }

        // Use Laravel's built-in password broker to send reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset link has been sent to your email.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unable to send reset link. Please try again later.',
        ], 429);
    }

    /**
     * Verify reset token is valid
     * GET /api/password-reset/verify/{token}
     *
     * Fixed: Laravel stores tokens as bcrypt hashes so we
     * cannot query by raw token — we must find by email first
     * then use Hash::check() to verify the token
     */
    public function verifyToken(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        // Find the reset record by email (not by token)
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token.',
            ], 401);
        }

        // Check if token has expired (60 minutes)
        $createdAt = \Carbon\Carbon::parse($record->created_at);
        if (now()->diffInMinutes($createdAt) > 60) {
            // Clean up expired token
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return response()->json([
                'success' => false,
                'message' => 'Token has expired. Please request a new reset link.',
            ], 401);
        }

        // Verify token using Hash::check() because
        // Laravel hashes the token before storing it
        if (!Hash::check($request->token, $record->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token is valid.',
            'data'    => ['email' => $record->email],
        ]);
    }

    /**
     * Reset password using token
     * POST /api/password/reset
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email'                 => 'required|email',
            'token'                 => 'required|string',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        // Use Laravel's built-in password reset which
        // handles token hashing and verification correctly
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Also update the UserManagementService credential record
                $this->userService->updatePassword($user, $password);

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully.',
            ]);
        }

        // Map Laravel password broker status to messages
        $message = match ($status) {
            Password::INVALID_TOKEN => 'Invalid or expired reset token.',
            Password::INVALID_USER  => 'No account found with that email address.',
            default                 => 'Unable to reset password. Please try again.',
        };

        return response()->json([
            'success' => false,
            'message' => $message,
        ], 422);
    }
}
