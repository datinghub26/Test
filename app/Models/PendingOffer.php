<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PendingOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'hold_duration_days',
        'is_active',
        'description',
    ];

    protected $casts = [
        'hold_duration_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'offer_id', 'offer_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'offer_id', 'offer_id');
    }

    public static function getHoldDurationForOffer(string $offerId): ?int
    {
        $rule = static::where('offer_id', $offerId)
            ->where('is_active', true)
            ->first();

        if ($rule) {
            return $rule->hold_duration_days;
        }

        $offer = Offer::where('offer_id', $offerId)->first();
        if ($offer && $offer->hold_duration_days > 0) {
            return $offer->hold_duration_days;
        }

        return null;
    }
}
