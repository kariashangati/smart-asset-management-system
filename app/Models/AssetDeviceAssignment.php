<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;
class AssetDeviceAssignment extends Model
{
    use HasFactory;
     use LogsActivity;

    protected $fillable = [
        'asset_id',
        'tracker_device_id',
        'assigned_at',
        'unassigned_at',
        'assigned_by',
        'is_active',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'unassigned_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function trackerDevice()
    {
        return $this->belongsTo(TrackerDevice::class);
    }

    public function assignedByUser()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}