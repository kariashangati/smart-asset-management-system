<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

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

    /**
     * Get activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'severity'])
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
}
