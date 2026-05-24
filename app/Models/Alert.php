<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Jobs\SendAlertNotificationJob;

class Alert extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'asset_id',
        'tracker_device_id',
        'alert_type',
        'severity',
        'title',
        'message',
        'status',
        'latitude',
        'longitude',
        'triggered_at',
        'resolved_at',
        'resolution_notes',
        'email_sent',
        'sms_sent',
        'push_sent',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'triggered_at' => 'datetime',
        'resolved_at' => 'datetime',
        'email_sent' => 'boolean',
        'sms_sent' => 'boolean',
        'push_sent' => 'boolean',
    ];

    protected static function booted(): void
    {
        /**
         * Dispatch notification job when alert is created
         */
        static::created(function (Alert $alert) {
            // Dispatch the notification job to send emails
            SendAlertNotificationJob::dispatch($alert)->onQueue('notifications');
        });

        /**
         * Log alert updates
         */
        static::updating(function (Alert $alert) {
            if ($alert->isDirty('status') && $alert->status === 'resolved') {
                $alert->resolved_at = now();
            }
        });
    }

    /**
     * Get activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'severity', 'title', 'message'])
            ->logOnlyDirty()
            ->useLogName('alert');
    }

    /**
     * Asset relationship
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Tracker device relationship
     */
    public function trackerDevice(): BelongsTo
    {
        return $this->belongsTo(TrackerDevice::class);
    }

    /**
     * Get severity color
     */
    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            'critical' => '#ef4444',
            'high' => '#f97316',
            'medium' => '#eab308',
            'low' => '#22c55e',
            default => '#3b82f6',
        };
    }

    /**
     * Get severity badge class
     */
    public function getSeverityBadgeAttribute(): string
    {
        return match ($this->severity) {
            'critical' => 'badge-danger',
            'high' => 'badge-warning',
            'medium' => 'badge-info',
            'low' => 'badge-success',
            default => 'badge-secondary',
        };
    }

    /**
     * Check if alert is unread
     */
    public function isUnread(): bool
    {
        return $this->status === 'unread';
    }

    /**
     * Check if alert is resolved
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    /**
     * Mark alert as read
     */
    public function markAsRead(): void
    {
        $this->update(['status' => 'read']);
    }

    /**
     * Mark alert as resolved
     */
    public function markAsResolved(string $notes = null): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_notes' => $notes,
        ]);
    }
}
