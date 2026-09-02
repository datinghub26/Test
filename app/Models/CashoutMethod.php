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
