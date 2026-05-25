<?php

namespace App\Notifications;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlertEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Alert $alert;

    /**
     * Create a new notification instance.
     */
    public function __construct(Alert $alert)
    {
        $this->alert = $alert;
        $this->onQueue('notifications');
    }

    /**
     * Get the notification's delivery channels.
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
        $severityColor = match($this->alert->severity) {
            'high' => '#dc2626',
            'medium' => '#f59e0b',
            'low' => '#10b981',
            default => '#6b7280',
        };

        return (new MailMessage)
            ->subject('🚨 Alert: ' . $this->alert->title)
            ->greeting('Alert Notification')
            ->line('**Severity:** ' . strtoupper($this->alert->severity))
            ->line('**Asset:** ' . $this->alert->asset->name)
            ->line('**Type:** ' . str_replace('_', ' ', ucfirst($this->alert->alert_type)))
            ->line('**Time:** ' . $this->alert->triggered_at->format('M d, Y H:i:s'))
            ->line('**Details:** ' . $this->alert->message)
            ->action('View Alert', route('admin.alerts.show', $this->alert))
            ->line('Location: ' . $this->alert->latitude . ', ' . $this->alert->longitude)
            ->line('---')
            ->line('Smart Asset Management System');
    }

    /**
     * Get the array representation of the notification.
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
