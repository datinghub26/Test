<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = [
        'name',
        'type',
        'image',
        'bg_color',
        'badge',
        'badge_bg_color',
        'show_rate',
        'rate',
        'url',
        'order',
        'is_active',
        'is_mobile',
        'sdk_data',
        'unlock_level',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_mobile' => 'boolean',
        'sdk_data' => 'array',
    ];

    public static function boot(): void
    {
        parent::boot();
        static::creating(function ($element) {
            $biggestOrder = Provider::max('order');
            $element->order = $biggestOrder + 1;
        });
    }

    protected function finalUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => str_replace('{user_id}', auth()?->id() ?? '', $this->url)
        );
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', 1);
    }

    public function scopeOffer(Builder $query): Builder
    {
        return $query->where('type', 'offer');
    }

    public function scopeSurvey(Builder $query): Builder
    {
        return $query->where('type', 'survey');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }

    public function isLocked(): bool
    {
        return auth()->check() && (auth()->user()->level < $this->unlock_level);
    }
}
