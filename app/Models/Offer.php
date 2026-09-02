<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Offer extends Model
{
    protected $fillable = [
        'offer_id',
        'provider',
        'title',
        'description',
        'instructions',
        'requirements',
        'image',
        'link',
        'points',
        'payout',
        'is_featured',
        'is_manual',
        'hold_duration_days',
        'categories',
        'countries',
        'devices',
        'events',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_manual' => 'boolean',
        'hold_duration_days' => 'integer',
        'instructions' => 'array',
        'categories' => 'array',
        'countries' => 'array',
        'devices' => 'array',
        'events' => 'array',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'offer_id', 'offer_id');
    }

    public function pendingRule(): HasOne
    {
        return $this->hasOne(PendingOffer::class, 'offer_id', 'offer_id');
    }

    /**
     * Scope for manually featured / pinned offers.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('is_featured', true)
              ->orWhere('is_manual', true);
        });
    }

    /**
     * Fetch top offers using the hybrid model:
     * 1. Manual/Admin featured offers first
     * 2. Highest completion count offers to fill remaining slots
     */
    public static function getTopHybridOffers(int $limit = 10, ?string $country = null)
    {
        $country = $country ?? strtoupper(country_code() ?? 'UNKNOWN');

        // 1. Fetch manually featured / pinned offers
        $manualOffers = static::query()
            ->featured()
            ->withCount('leads')
            ->where(function ($query) use ($country) {
                return $query->whereJsonContains('countries', $country)
                    ->orWhereJsonLength('countries', 0);
            })
            ->latest('updated_at')
            ->take($limit)
            ->get();

        $manualCount = $manualOffers->count();

        // 2. If slots are still available, fill with highest completion counts
        if ($manualCount < $limit) {
            $remaining = $limit - $manualCount;
            $manualIds = $manualOffers->pluck('id')->toArray();

            $popularOffers = static::query()
                ->whereNotIn('id', $manualIds)
                ->withCount('leads')
                ->where(function ($query) use ($country) {
                    return $query->whereJsonContains('countries', $country)
                        ->orWhereJsonLength('countries', 0);
                })
                ->orderByDesc('leads_count')
                ->orderByDesc('points')
                ->take($remaining)
                ->get();

            return $manualOffers->concat($popularOffers);
        }

        return $manualOffers;
    }
}
