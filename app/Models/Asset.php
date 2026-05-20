<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

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

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function assignments()
    {
        return $this->hasMany(AssetDeviceAssignment::class);
    }

    public function activeAssignment()
    {
        return $this->hasOne(AssetDeviceAssignment::class)->where('is_active', true);
    }

    public function geofences()
    {
        return $this->hasMany(Geofence::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
}