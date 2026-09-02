<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavbarButton extends Model
{
    protected $fillable = [
        'name',
        'image',
        'bg_color',
//        'url',
        'order',
        'is_active'
    ];

    public static function boot(): void
    {
        parent::boot();
        static::creating(function ($element) {
            $biggestOrder = NavbarButton::max('order');
            $element->order = $biggestOrder + 1;
        });
    }
}
