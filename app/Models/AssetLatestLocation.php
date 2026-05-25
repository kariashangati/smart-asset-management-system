<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetLatestLocation extends Model
{
    use HasFactory;

    // Removed LogsActivity trait intentionally —
    // this model updates on every GPS ping which would
    // flood the audit_logs table with millions of records

    protected $fillable = [
        'asset_id',
        'tracker_device_id',
        'latitude',
        'longitude',
        'last_motion_detected',
        'last_recorded_at',
    ];

    protected $casts = [
        'latitude'             => 'float',
        'longitude'            => 'float',
        'last_motion_detected' => 'boolean',
        'last_recorded_at'     => 'datetime',
    ];

    /**
     * Asset relationship
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Tracker device relationship
     */
    public function trackerDevice()
    {
        return $this->belongsTo(TrackerDevice::class);
    }
}
