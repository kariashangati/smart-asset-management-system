<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'device_name',
        'platform',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
