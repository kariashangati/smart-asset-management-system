<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Models\User;
use App\Notifications\AlertNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class SendAlertNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Alert $alert;
    protected int $tries = 3;
    protected int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(Alert $alert)
    {
        $this->alert = $alert;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Get admin users who should receive alerts
            // Fixed: was ->where('is_active', true) — User model uses 'status' column
            $adminUsers = User::role('admin')
                ->where('status', 'active')
                ->get();

            // Get asset manager for the asset's department
            $assetManagers = User::role('asset_manager')
                ->where('department_id', $this->alert->asset?->department_id)
                ->where('status', 'active')
                ->get();

            // Combine both groups and remove duplicates
            $recipients = $adminUsers->merge($assetManagers)->unique('id');

            // Send notifications
            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new AlertNotification($this->alert));

                // Mark email as sent
                $this->alert->update(['email_sent' => true]);
            }

            // Log the notification
            Log::info('Alert notification sent', [
                'alert_id'         => $this->alert->id,
                'recipients_count' => $recipients->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send alert notification', [
                'alert_id' => $this->alert->id,
                'error'    => $e->getMessage(),
            ]);

            // Retry or fail
            if ($this->attempts() < $this->tries) {
                $this->release(60);
            } else {
                $this->fail($e);
            }
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Alert notification job failed after ' . $this->tries . ' attempts', [
            'alert_id'  => $this->alert->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
