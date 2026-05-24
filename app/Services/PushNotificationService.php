<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class PushNotificationService
{
    /**
     * Send push notification to user
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        $tokens = $user->deviceTokens()
            ->where('is_active', true)
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return;
        }

        foreach ($tokens as $token) {
            $this->sendToToken($token, $title, $body, $data);
        }
    }

    /**
     * Send push notification to token
     */
    public function sendToToken(string $token, string $title, string $body, array $data = []): void
    {
        // Implementation depends on FCM setup
        // This is a placeholder for the actual FCM integration
    }
}
