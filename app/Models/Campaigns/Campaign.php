<?php

namespace App\Models\Campaigns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $fillable = [
        'identifier',
        'name',
        'url',
        'event_macro',
        'has_multi_event',
        'is_active',
    ];

    protected $casts = [
        'has_multi_event' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Campaign $campaign) {
            $campaign->identifier = \Str::uuid();
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(CampaignUser::class, 'campaign_id', 'id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(CampaignEvent::class, 'campaign_id', 'id');
    }


}
