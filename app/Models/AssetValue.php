<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'purchase_price',
        'current_value',
        'depreciation_rate',
        'last_valued_at',
        'notes',
    ];

    protected $casts = [
        'purchase_price' => 'float',
        'current_value' => 'float',
        'depreciation_rate' => 'float',
        'last_valued_at' => 'datetime',
    ];

    /**
     * Asset relationship
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
