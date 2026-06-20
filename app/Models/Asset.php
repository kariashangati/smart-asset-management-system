<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Asset extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'asset_type',
        'asset_code',
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
     * Category relationship
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    /**
     * Direct tracker_device_id relationship.
     *
     * NOTE (audit #3): assets.tracker_device_id is NOT set anywhere in the
     * real admin flow (DeviceAssignmentController uses the AssetDeviceAssignment
     * pivot instead). This relation is left intact/unchanged so nothing that
     * currently references it breaks, but it will return null for assets
     * assigned through the actual app. Code that needs "the device currently
     * on this asset" should use activeAssignment->trackerDevice instead — see
     * the fixed WebhookController and LogAssetLocation listener.
     */
    public function trackerDevice(): BelongsTo
    {
        return $this->belongsTo(TrackerDevice::class);
    }

    /**
     * All assignment history for this asset (active + ended)
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(AssetDeviceAssignment::class);
    }

    /**
     * Active assignment relationship (already correct, unchanged)
     */
    public function activeAssignment(): HasOne
    {
        return $this->hasOne(AssetDeviceAssignment::class)
            ->where('is_active', true);
    }

    /**
     * Latest location relationship
     */
    public function latestLocation(): HasOne
    {
        return $this->hasOne(AssetLatestLocation::class)->latest('last_recorded_at');
    }

    /**
     * Location logs relationship
     */
    public function locationLogs(): HasMany
    {
        return $this->hasMany(LocationLog::class);
    }

    /**
     * Alerts relationship
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    /**
     * FIX (audit #7): This used to be belongsToMany(Geofence::class), implying an
     * asset_geofence pivot table that is never created or written to anywhere in
     * the app. The real schema (see Geofence::asset(), admin/geofences views,
     * GeofenceController) is the other direction: geofences.asset_id -> assets.id,
     * i.e. one-to-many from Asset's perspective (an asset can accumulate multiple
     * geofence records over time, only one of which is typically 'active').
     *
     * GeofenceService::checkAndCreateAlerts() and AlertService::generateGeofenceAlert()
     * both call $asset->geofences()->where('status','active'), which works correctly
     * against a hasMany and previously returned nothing against the unused pivot.
     */
    public function geofences(): HasMany
    {
        return $this->hasMany(Geofence::class);
    }

    /**
     * Custom alert rules relationship
     */
    public function customAlertRules(): HasMany
    {
        return $this->hasMany(CustomAlertRule::class);
    }
}
