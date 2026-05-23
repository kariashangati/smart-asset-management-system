<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;
class TrackerDevice extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'device_code',
        'device_name',
        'imei',
        'sim_number',
        'api_token_hash',
        'status',
        'last_seen_at',
        'battery_level',
        'firmware_version',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    public function assignments()
    {
        return $this->hasMany(AssetDeviceAssignment::class);
    }

    public function activeAssignment()
    {
        return $this->hasOne(AssetDeviceAssignment::class)->where('is_active', true);
    }

    public function locationLogs()
    {
        return $this->hasMany(LocationLog::class);
    }
}