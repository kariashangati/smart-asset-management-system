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

    protected Alert $alert;
    protected int $tries = 3;

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
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['mail'];
        
        // Add database channel for in-app notifications
        $channels[] = 'database';

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $severityColor = match ($this->alert->severity) {
            'critical' => '#ef4444',
            'high' => '#f97316',
            'medium' => '#eab308',
            'low' => '#22c55e',
            default => '#3b82f6',
        };

        $severityLabel = match ($this->alert->severity) {
            'critical' => '🚨 CRITICAL',
            'high' => '⚠️ HIGH',
            'medium' => '⚡ MEDIUM',
            'low' => 'ℹ️ LOW',
            default => '📢 INFO',
        };

        $actionUrl = route('admin.alerts.show', $this->alert);

        return (new MailMessage)
            ->subject("[{$severityLabel}] " . $this->alert->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new alert has been triggered in the Asset Management System.')
            ->line('')
            ->line('**Alert Details:**')
            ->line('📌 **Title:** ' . $this->alert->title)
            ->line('📝 **Type:** ' . str_replace('_', ' ', ucfirst($this->alert->alert_type)))
            ->line('🎯 **Asset:** ' . ($this->alert->asset?->name ?? 'Unknown'))
            ->line('🔴 **Severity:** ' . ucfirst($this->alert->severity))
            ->line('⏰ **Triggered:** ' . $this->alert->triggered_at->format('d M Y H:i:s'))
            ->line('')
            ->line('**Message:**')
            ->line($this->alert->message)
            ->when(
                $this->alert->latitude && $this->alert->longitude,
                fn($mail) => $mail->line('📍 **Location:** ' . $this->alert->latitude . ', ' . $this->alert->longitude)
            )
            ->line('')
            ->action('View Alert', $actionUrl)
            ->line('')
            ->line('Please log in to the system to review and take necessary action.')
            ->line('')
            ->line('Best regards,')
            ->line('Smart Asset Management System');
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
            'alert_type' => $this->alert->alert_type,
            'title' => $this->alert->title,
            'message' => $this->alert->message,
            'severity' => $this->alert->severity,
            'asset_id' => $this->alert->asset_id,
            'asset_name' => $this->alert->asset?->name,
            'triggered_at' => $this->alert->triggered_at->toIso8601String(),
            'url' => route('admin.alerts.show', $this->alert),
        ];
    }
}
