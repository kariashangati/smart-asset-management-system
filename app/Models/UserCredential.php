<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCredential extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'temporary_password',
        'credentials_sent_at',
        'password_reset_at',
        'sent_to_email',
    ];

    protected $casts = [
        'credentials_sent_at' => 'datetime',
        'password_reset_at' => 'datetime',
    ];
}
