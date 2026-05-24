<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPasswordNotification extends ResetPassword implements ShouldQueue
{
    use Queueable;

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], absolute: true);

        return (new MailMessage)
            ->subject('Reset Password Request')
            ->greeting('Hello ' . $notifiable->name)
            ->line('You have requested to reset your password.')
            ->line('This password reset link will expire in 60 minutes.')
            ->action('Reset Password', $url)
            ->line('If you did not request a password reset, no further action is required.')
            ->line('Thank you for using Smart Asset Management System');
    }
}
