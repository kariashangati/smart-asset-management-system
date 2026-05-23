<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Asset extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'asset_code',
        'name',
        'serial_number',
        'asset_category_id',
        'department_id',
        'description',
        'purchase_date',
        'status',
        'image',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    /**
     * Asset category
     */
    public function category()
    {
        return $this->belongsTo(
            AssetCategory::class,
            'asset_category_id'
        );
    }

    /**
     * Department
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Device assignments
     */
    public function assignments()
    {
        return $this->hasMany(AssetDeviceAssignment::class);
    }

    /**
     * Current active assignment
     */
    public function activeAssignment()
    {
        return $this->hasOne(AssetDeviceAssignment::class)
            ->where('is_active', true);
    }

    /**
     * Geofences
     */
    public function geofences()
    {
        return $this->hasMany(Geofence::class);
    }

    /**
     * Alerts
     */
    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    /**
     * Location history logs
     */
    public function locationLogs()
    {
        return $this->hasMany(LocationLog::class);
    }
}