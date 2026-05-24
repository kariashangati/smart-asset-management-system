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
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];
        
        if ($notifiable->phone_number && $notifiable->sms_notifications_enabled) {
            $channels[] = 'twilio';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('alerts.show', $this->alert->id);
        $severityColor = match($this->alert->severity) {
            'high' => '#dc2626',
            'medium' => '#f59e0b',
            default => '#10b981',
        };

        return (new MailMessage)
            ->subject('[' . strtoupper($this->alert->severity) . '] ' . $this->alert->title)
            ->greeting('Alert Notification')
            ->line('An important alert has been triggered:')
            ->line('**Asset:** ' . $this->alert->asset->name)
            ->line('**Type:** ' . $this->alert->alert_type)
            ->line('**Severity:** ' . strtoupper($this->alert->severity))
            ->line('**Message:** ' . $this->alert->message)
            ->line('**Time:** ' . $this->alert->triggered_at->format('Y-m-d H:i:s'))
            ->action('View Alert Details', $url)
            ->line('Please take appropriate action as soon as possible.')
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
