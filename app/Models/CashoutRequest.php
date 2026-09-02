<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashoutRequest extends Model
{
    protected $fillable = [
        'user_id',
        'method_name',
        'method_image',
        'amount',
        'address',
        'status',
        'rejection_reason'
    ];


    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
