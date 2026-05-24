<?php

namespace App\Notifications;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RealTimeAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private Alert $alert)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];

        if ($notifiable->email_notifications_enabled) {
            $channels[] = 'mail';
        }
        if ($notifiable->push_notifications_enabled) {
            $channels[] = 'fcm';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $severity = strtoupper($this->alert->severity);
        $color = match ($this->alert->severity) {
            'critical' => '#dc2626',
            'warning' => '#f59e0b',
            'info' => '#3b82f6',
            default => '#6b7280',
        };

        return (new MailMessage)
            ->subject("[$severity] Asset Alert: {$this->alert->asset->name}")
            ->greeting("Alert Notification")
            ->line("Asset: **{$this->alert->asset->name}**")
            ->line("Type: {$this->alert->alert_type}")
            ->line("Severity: <span style='color: $color; font-weight: bold;'>$severity</span>")
            ->line("Description: {$this->alert->description}")
            ->line("Time: {$this->alert->created_at->format('Y-m-d H:i:s')}")
            ->action('View Alert Details', url("/alerts/{$this->alert->id}"))
            ->markdown('emails.alert-notification');
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
            'description' => $this->alert->description,
        ];
    }
}
