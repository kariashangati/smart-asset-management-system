<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Asset extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'asset_code',
        'name',
        'description',
        'asset_type',
        'serial_number',
        'status',
        'department_id',
        'asset_category_id',
        'tracker_device_id',
        'asset_value',
        'purchase_date',
        'location',
        'notes',
        'image',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'asset_value' => 'float',
        'purchase_date' => 'date',
    ];

    /**
     * Get activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'status', 'asset_value', 'location'])
            ->logOnlyDirty()
            ->useLogName('asset');
    }

    /**
     * Department relationship
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Tracker device relationship
     */
    public function trackerDevice(): BelongsTo
    {
        return $this->belongsTo(TrackerDevice::class);
    }

    /**
     * Active assignment relationship
     */
    public function activeAssignment(): HasOne
    {
        return $this->hasOne(AssetDeviceAssignment::class)->where('is_active', true);
    }

    /**
     * All assignments relationship
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(AssetDeviceAssignment::class);
    }

    /**
     * Latest location relationship
     */
    public function latestLocation()
    {
        return $this->hasOne(AssetLatestLocation::class)->latest('last_recorded_at');
    }

    /**
     * Location logs relationship
     */
    public function locationLogs()
    {
        return $this->hasMany(LocationLog::class);
    }

    /**
     * Alerts relationship
     */
    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    /**
     * Geofences relationship
     */
    public function geofences()
    {
        return $this->belongsToMany(Geofence::class);
    }

    /**
     * Custom alert rules relationship
     */
    public function customAlertRules()
    {
        return $this->hasMany(CustomAlertRule::class);
    }
}
