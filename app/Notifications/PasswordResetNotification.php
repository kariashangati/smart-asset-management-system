<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $resetUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $resetUrl)
    {
        $this->resetUrl = $resetUrl;
        $this->onQueue('notifications');
        $this->onConnection('database'); // Use database queue connection
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🔐 Password Reset Request - Smart Asset Management')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->line('**This password reset link will expire in 60 minutes.**')
            ->action('Reset Password', $this->resetUrl)
            ->line('---')
            ->line('If you did not request a password reset, no further action is required.')
            ->line('If you experience any issues, please contact system administrator.')
            ->line('---')
            ->line('Smart Asset Management System Team')
            ->footer('This is an automated message. Please do not reply to this email.');
    }
}
