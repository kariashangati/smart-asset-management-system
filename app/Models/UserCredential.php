<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserCredential extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'password_hash',
        'temp_password',
        'password_changed_at',
        'password_expires_at',
        'status',
        'last_login_at',
        'login_attempts',
        'locked_until',
    ];

    protected $casts = [
        'password_changed_at' => 'datetime',
        'password_expires_at' => 'datetime',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
    ];

    protected $hidden = [
        'password_hash',
        'temp_password',
    ];

    /**
     * Get the user that owns this credential
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if password has expired
     */
    public function isPasswordExpired(): bool
    {
        return $this->password_expires_at && $this->password_expires_at->isPast();
    }

    /**
     * Check if account is locked
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Reset login attempts
     */
    public function resetLoginAttempts(): void
    {
        $this->update([
            'login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    /**
     * Increment login attempts and lock if needed
     */
    public function incrementLoginAttempts(): void
    {
        $attempts = ($this->login_attempts ?? 0) + 1;
        $lockTime = $attempts >= 5 ? Carbon::now()->addMinutes(30) : null;

        $this->update([
            'login_attempts' => $attempts,
            'locked_until' => $lockTime,
        ]);
    }

    /**
     * Update last login timestamp
     */
    public function recordLogin(): void
    {
        $this->update([
            'last_login_at' => Carbon::now(),
            'login_attempts' => 0,
            'locked_until' => null,
        ]);
    }
}
