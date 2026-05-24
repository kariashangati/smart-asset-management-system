<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class UserCredentialsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $user;
    public string $password;
    public bool $shouldResetPassword;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, string $password, bool $shouldResetPassword = false)
    {
        $this->user = $user;
        $this->password = $password;
        $this->shouldResetPassword = $shouldResetPassword;
        $this->onQueue('notifications');
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
        $message = (new MailMessage)
            ->subject('Welcome to Smart Asset Management System')
            ->greeting('Welcome, ' . $this->user->name)
            ->line('Your account has been created in the Smart Asset Management System.')
            ->line('**Email:** ' . $this->user->email)
            ->line('**Password:** ' . $this->password)
            ->line('---');

        if ($this->shouldResetPassword) {
            $message->line('⚠️ You must reset your password on first login.')
                ->action('Login to System', route('login'));
        } else {
            $message->action('Login Now', route('login'));
        }

        return $message->line('Smart Asset Management System Team');
    }
}
