<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomAlertRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'rule_name',
        'rule_type', // speed_threshold, geofence_breach, inactivity, custom
        'condition', // JSON format for complex conditions
        'threshold_value',
        'action', // email, sms, push, database
        'recipient_emails',
        'recipient_phones',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'recipient_emails' => 'array',
        'recipient_phones' => 'array',
        'condition' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Asset relationship
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Creator relationship
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
