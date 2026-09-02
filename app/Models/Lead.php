<?php

namespace App\Models;

use App\Events\LeadsUpdated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Event;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'offer_id',
        'offer_name',
        'offer_trx_id',
        'image',
        'points',
        'payout',
        'ip',
        'country_code',
        'status',
        'release_at',
        'hold_duration_days',
        'reason',
    ];

    protected $casts = [
        'points' => 'float',
        'payout' => 'float',
        'release_at' => 'datetime',
        'hold_duration_days' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn(Lead $lead) => $lead->image = $lead->offer->image ?? null);
        static::created(fn(Lead $lead) => LeadsUpdated::dispatch());
        static::updated(fn(Lead $lead) => LeadsUpdated::dispatch());
        static::deleted(fn(Lead $lead) => LeadsUpdated::dispatch());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'offer_id', 'offer_id');
    }

    public function getImageAttribute($value): ?string
    {
        if ($value)
            return $value;

        return $this->offer->image ?? null;
    }

    /**
     * Scope for pending leads that are ready to be released
     */
    public function scopeReadyToRelease(Builder $query): Builder
    {
        return $query->where('status', 'pending')
            ->whereNotNull('release_at')
            ->where('release_at', '<=', now());
    }
}
