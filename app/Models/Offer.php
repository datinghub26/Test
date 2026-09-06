<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'is_active',
        'hold_duration_days',
        'categories',
        'countries',
        'devices',
        'events',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_manual' => 'boolean',
        'is_active' => 'boolean',
        'hold_duration_days' => 'integer',
        'categories' => 'array',
        'countries' => 'array',
        'devices' => 'array',
        'events' => 'array',
    ];

    /**
     * Ensure instructions is always accessed and stored as an array of step strings.
     */
    protected function instructions(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) return [];
                if (is_array($value)) return array_values(array_filter($value));
                $decoded = json_decode($value, true);
                if (is_array($decoded)) return array_values(array_filter($decoded));
                if (is_string($value)) {
                    $trimmed = trim($value);
                    if (str_contains($trimmed, "\n")) {
                        return array_values(array_filter(array_map(fn($s) => trim(preg_replace('/^\d+[\.\)]\s*/', '', $s)), explode("\n", $trimmed))));
                    }
                    $parts = preg_split('/(?:^|\s+)\d+[\.\)]\s+/', $trimmed, -1, PREG_SPLIT_NO_EMPTY);
                    if (count($parts) > 1) {
                        return array_values(array_filter(array_map('trim', $parts)));
                    }
                    return [trim(preg_replace('/^\d+[\.\)]\s*/', '', $trimmed))];
                }
                return [];
            },
            set: function ($value) {
                if (is_array($value)) {
                    return json_encode(array_values(array_filter($value)));
                }
                if (is_string($value)) {
                    $trimmed = trim($value);
                    if (str_starts_with($trimmed, '[') && str_ends_with($trimmed, ']')) {
                        $decoded = json_decode($trimmed, true);
                        if (is_array($decoded)) return json_encode(array_values(array_filter($decoded)));
                    }
                    if (str_contains($trimmed, "\n")) {
                        $lines = array_values(array_filter(array_map(fn($s) => trim(preg_replace('/^\d+[\.\)]\s*/', '', $s)), explode("\n", $trimmed))));
                        return json_encode($lines);
                    }
                    $parts = preg_split('/(?:^|\s+)\d+[\.\)]\s+/', $trimmed, -1, PREG_SPLIT_NO_EMPTY);
                    if (count($parts) > 1) {
                        return json_encode(array_values(array_filter(array_map('trim', $parts))));
                    }
                    return json_encode([trim(preg_replace('/^\d+[\.\)]\s*/', '', $trimmed))]);
                }
                return json_encode([]);
            }
        );
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'offer_id', 'offer_id');
    }

    public function pendingRule(): HasOne
    {
        return $this->hasOne(PendingOffer::class, 'offer_id', 'offer_id');
    }

    /**
     * Scope for active (non-disabled) offers.
     */
    public function scopeActive(Builder $query): Builder
    {
        if (\Illuminate\Support\Facades\Schema::hasColumn('offers', 'is_active')) {
            return $query->where('is_active', true);
        }
        return $query;
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
     * Fetch top offers using dynamic mode (auto, manual, hybrid):
     * Supports admin configuration: top_offers.mode, top_offers.limit, top_offers.ranking_metric
     */
    public static function getTopHybridOffers(int $limit = 3, ?string $country = null)
    {
        $country = $country ?? strtoupper(country_code() ?? 'UNKNOWN');
        $configuredLimit = (int) (function_exists('setting') ? setting('top_offers.limit', $limit) : $limit);
        $limit = $configuredLimit > 0 ? $configuredLimit : $limit;
        $mode = function_exists('setting') ? setting('top_offers.mode', 'hybrid') : 'hybrid';
        $metric = function_exists('setting') ? setting('top_offers.ranking_metric', 'conversions') : 'conversions';

        $countryFilter = function ($query) use ($country) {
            return $query->whereJsonContains('countries', $country)
                ->orWhereJsonLength('countries', 0)
                ->orWhereNull('countries');
        };

        // Manual mode: strictly manual / featured offers
        if ($mode === 'manual') {
            return static::query()
                ->active()
                ->featured()
                ->withCount('leads')
                ->where($countryFilter)
                ->latest('updated_at')
                ->take($limit)
                ->get();
        }

        // Auto mode: ranked strictly by conversion / performance metric
        if ($mode === 'auto') {
            $query = static::query()
                ->active()
                ->withCount('leads')
                ->where($countryFilter);

            if ($metric === 'points') {
                $query->orderByDesc('points')->orderByDesc('leads_count');
            } elseif ($metric === 'payout') {
                $query->orderByDesc('payout')->orderByDesc('leads_count');
            } else {
                $query->orderByDesc('leads_count')->orderByDesc('points');
            }

            return $query->take($limit)->get();
        }

        // Hybrid mode (default): Manual/Admin featured offers first, then best-converting to fill slots
        $manualOffers = static::query()
            ->active()
            ->featured()
            ->withCount('leads')
            ->where($countryFilter)
            ->latest('updated_at')
            ->take($limit)
            ->get();

        $manualCount = $manualOffers->count();

        if ($manualCount < $limit) {
            $remaining = $limit - $manualCount;
            $manualIds = $manualOffers->pluck('id')->toArray();

            $popularQuery = static::query()
                ->active()
                ->whereNotIn('id', $manualIds)
                ->withCount('leads')
                ->where($countryFilter);

            if ($metric === 'points') {
                $popularQuery->orderByDesc('points')->orderByDesc('leads_count');
            } elseif ($metric === 'payout') {
                $popularQuery->orderByDesc('payout')->orderByDesc('leads_count');
            } else {
                $popularQuery->orderByDesc('leads_count')->orderByDesc('points');
            }

            $popularOffers = $popularQuery->take($remaining)->get();

            return $manualOffers->concat($popularOffers);
        }

        return $manualOffers;
    }
}
