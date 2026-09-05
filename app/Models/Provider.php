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

    protected $appends = [
        'image_url',
    ];

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->image)) {
                    return asset('assets/img/placeholder-provider.svg');
                }
                if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                    return $this->image;
                }
                $cleaned = ltrim($this->image, '/');
                if (str_starts_with($cleaned, 'storage/')) {
                    $cleaned = substr($cleaned, 8);
                }
                return \Illuminate\Support\Facades\Storage::disk('public')->url($cleaned);
            }
        );
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
