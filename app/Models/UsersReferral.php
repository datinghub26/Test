<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersReferral extends Model
{
    protected $fillable = [
        'user_id',
        'referred_by_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
