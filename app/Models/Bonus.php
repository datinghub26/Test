<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bonus extends Model
{
    protected $fillable = [
        'code',
        'reward_points',
        'expires_at',
        'is_active',
        'max_uses',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function usage(): HasMany
    {
        return $this->hasMany(BonusUsage::class);
    }
}
