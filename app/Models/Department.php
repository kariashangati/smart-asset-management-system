<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\LogsActivity;

class Department extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'code',
        'name',
        'description',
        'location',
    ];

    /**
     * Assets in this department
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    /**
     * Managers assigned to this department
     */
    public function managers(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Count managers in this department
     */
    public function getManagerCountAttribute(): int
    {
        return $this->managers()
            ->whereHas('roles', function ($query) {
                $query->where('name', 'asset_manager');
            })
            ->count();
    }
}
