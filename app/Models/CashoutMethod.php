<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashoutMethod extends Model
{
    protected $fillable = [
        'name',
        'image',
        'category',
        'bg_color',
        'fee',
        'minimum',
        'payment_title',
        'payment_note',
    ];

    protected $appends = [
        'image_url',
    ];

    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return asset('assets/img/icon-light.png');
        }
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->image);
    }



    public static function boot(): void
    {
        parent::boot();
        static::creating(function ($element) {
            $biggestOrder = CashoutMethod::max('order');
            $element->order = $biggestOrder + 1;
        });
    }

    public function prices(): HasMany
    {
        return $this->hasMany(CashoutPrice::class);
    }
}
