<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Send password reset link via email
     * POST /api/password/forgot
     */
    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        try {
            $user = User::where('email', $request->email)->first();
            $token = Str::random(64);

            // Store reset token in database
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => hash('sha256', $token),
                    'created_at' => now(),
                ]
            );

            // Send email with reset link - dispatched to queue (async)
            $resetUrl = url('/password-reset/' . $token . '?email=' . urlencode($request->email));
            
            // Dispatch notification asynchronously using the queue
            try {
                $user->notify(new PasswordResetNotification($resetUrl));
                Log::info('Password reset email queued for: ' . $user->email);
            } catch (\Exception $e) {
                Log::error('Failed to queue password reset email: ' . $e->getMessage());
                // Still return success but log the error for debugging
            }

            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email. Check your inbox and spam folder.',
            ]);
        } catch (\Exception $e) {
            Log::error('Password reset request error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again later.',
            ], 500);
        }
    }

    /**
     * Reset password
     * POST /api/password/reset
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $record = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$record || !hash_equals($record->token, hash('sha256', $request->token))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired reset token',
                ], 400);
            }

            // Check if token is expired (older than 60 minutes)
            if ($record->created_at < now()->subMinutes(60)) {
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Reset token has expired. Please request a new password reset link.',
                ], 400);
            }

            // Update password
            $user = User::where('email', $request->email)->first();
            $user->update(['password' => bcrypt($request->password)]);

            // Delete reset token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            Log::info('Password successfully reset for user: ' . $user->email);

            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully. You can now login with your new password.',
            ]);
        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while resetting password. Please try again later.',
            ], 500);
        }
    }
}
