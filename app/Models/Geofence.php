<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Geofence extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'name',
        'center_latitude',
        'center_longitude',
        'radius_meters',
        'status',
        'created_by',
    ];

    protected $casts = [
        'center_latitude' => 'float',
        'center_longitude' => 'float',
        'radius_meters' => 'integer',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}