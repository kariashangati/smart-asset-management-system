<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class LocationLog extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'tracker_device_id',
        'asset_id',
        'latitude',
        'longitude',
        'speed',
        'motion_detected',
        'recorded_at',
        'received_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'speed' => 'float',
        'motion_detected' => 'boolean',
        'recorded_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    /**
     * Device that sent the location
     */
    public function trackerDevice()
    {
        return $this->belongsTo(TrackerDevice::class);
    }

    /**
     * Asset related to this log
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}