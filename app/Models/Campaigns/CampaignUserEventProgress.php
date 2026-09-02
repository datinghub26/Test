<?php

namespace App\Models\Campaigns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignUserEventProgress extends Model
{
    protected $fillable = [
        'user_id',
        'campaign_id',
        'campaign_event_id',
        'is_completed',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(CampaignUser::class, 'user_id', 'user_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(CampaignEvent::class, 'campaign_event_id', 'id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }


}
