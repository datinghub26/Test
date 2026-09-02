<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BonusUsage extends Model
{
    protected $fillable = [
        'user_id',
        'bonus_id',
    ];

    public function bonus(): BelongsTo
    {
        return $this->belongsTo(Bonus::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
