<?php

namespace App\Notifications;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Alert $alert;

    /**
     * Create a new notification instance.
     */
    public function __construct(Alert $alert)
    {
        $this->alert = $alert;
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
        return (new MailMessage)
            ->subject($this->alert->title)
            ->greeting('Alert Notification')
            ->line('Asset: ' . $this->alert->asset->name)
            ->line('Type: ' . $this->alert->alert_type)
            ->line('Severity: ' . strtoupper($this->alert->severity))
            ->line($this->alert->message)
            ->action('View Alert', route('alerts.show', $this->alert->id))
            ->line('Thank you for using Smart Asset Management System');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'alert_id' => $this->alert->id,
            'asset_id' => $this->alert->asset_id,
            'asset_name' => $this->alert->asset->name,
            'alert_type' => $this->alert->alert_type,
            'severity' => $this->alert->severity,
            'title' => $this->alert->title,
            'message' => $this->alert->message,
            'triggered_at' => $this->alert->triggered_at,
        ];
    }
}
