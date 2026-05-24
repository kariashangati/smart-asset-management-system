<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlertRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'rule_type',
        'condition',
        'threshold_value',
        'action',
        'status',
        'created_by',
        'asset_id',
        'department_id',
    ];

    protected $casts = [
        'condition' => 'array',
        'threshold_value' => 'float',
    ];
}
