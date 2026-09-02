<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashoutPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'cashout_method_id',
        'price'
    ];

    public function cashout(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CashoutMethod::class);
    }
}
