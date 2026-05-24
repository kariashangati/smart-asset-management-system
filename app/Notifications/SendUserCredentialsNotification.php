<?php

namespace App\Notifications;

use App\Models\UserCredential;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendUserCredentialsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private UserCredential $credential)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
            ->subject('Your Smart Asset Management System Credentials')
            ->greeting("Welcome, {$notifiable->name}!")
            ->line('Your account has been created in the Smart Asset Management System.')
            ->line("**Email:** {$this->credential->email}")
            ->line("**Temporary Password:** {$this->credential->temp_password}")
            ->line('Please log in and change your password immediately for security reasons.')
            ->action('Login Now', url('/login'))
            ->line('If you have any questions, please contact your administrator.')
            ->salutation('Best regards,\nSmart Asset Management System');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'credential_id' => $this->credential->id,
            'email' => $this->credential->email,
        ];
    }
}
