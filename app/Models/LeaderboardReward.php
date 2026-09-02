<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaderboardReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'rank',
        'reward_points',
    ];
}
