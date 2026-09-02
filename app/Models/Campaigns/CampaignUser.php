<?php

namespace App\Models\Campaigns;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignUser extends Model
{
    protected $fillable = [
        'user_id',
        'campaign_id',
        'click_id',
        'source',
        'oid',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'user_id', 'user_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function eventProgress(): HasMany
    {
        return $this->hasMany(CampaignUserEventProgress::class, 'user_id', 'user_id');
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->eventProgress->where('is_completed', true)->count() === $this->campaign->events->count();
    }

}
