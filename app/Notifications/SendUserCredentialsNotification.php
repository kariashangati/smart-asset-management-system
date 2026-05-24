<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendUserCredentialsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $user;
    public string $temporaryPassword;
    public bool $adminCanResetPassword;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, string $temporaryPassword, bool $adminCanResetPassword = true)
    {
        $this->user = $user;
        $this->temporaryPassword = $temporaryPassword;
        $this->adminCanResetPassword = $adminCanResetPassword;
        $this->onQueue('notifications');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $loginUrl = route('login', absolute: true);

        $message = (new MailMessage)
            ->subject('Your Account Credentials - Smart Asset Management System')
            ->greeting('Welcome to Smart Asset Management System')
            ->line('Your account has been created by an administrator.')
            ->line('**Email:** ' . $this->user->email)
            ->line('**Temporary Password:** ' . $this->temporaryPassword)
            ->line('Please log in with these credentials and change your password immediately.');

        if ($this->adminCanResetPassword) {
            $message->line('The administrator may reset your password at any time.')
                ->line('To request a password reset, contact your administrator.');
        } else {
            $message->line('To reset your password, use the "Forgot Password" feature on the login page.');
        }

        return $message
            ->action('Login to Your Account', $loginUrl)
            ->line('Thank you for being part of our system!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'email' => $this->user->email,
            'message' => 'New user account created',
            'temporary_password_sent' => true,
        ];
    }
}
