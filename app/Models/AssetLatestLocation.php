<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetLatestLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id', 'tracker_device_id', 'latitude', 'longitude',
        'last_motion_detected', 'last_recorded_at'
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'last_motion_detected' => 'boolean',
        'last_recorded_at' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function trackerDevice()
    {
        return $this->belongsTo(TrackerDevice::class);
    }
}