<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StreaksClaim extends Model
{
    protected $fillable = [
        'streak_id',
        'user_id',
        'points',
    ];

    public function streak(): BelongsTo
    {
        return $this->belongsTo(Streak::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
