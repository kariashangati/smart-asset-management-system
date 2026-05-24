<?php

namespace App\Models;

use Illuminate\Database\Eloquent\ Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'tracker_device_id',
        'latitude',
        'longitude',
        'speed',
        'motion_detected',
        'recorded_at',
        'received_at',
        'processed',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'speed' => 'float',
        'motion_detected' => 'boolean',
        'processed' => 'boolean',
        'recorded_at' => 'datetime',
        'received_at' => 'datetime',
    ];

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
