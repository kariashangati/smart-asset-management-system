<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'tracker_device_id',
        'alert_type',
        'severity',
        'title',
        'message',
        'latitude',
        'longitude',
        'triggered_at',
        'status',
        'read_by',
        'read_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'triggered_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function trackerDevice()
    {
        return $this->belongsTo(TrackerDevice::class);
    }

    public function readByUser()
    {
        return $this->belongsTo(User::class, 'read_by');
    }
}