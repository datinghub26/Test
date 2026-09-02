<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'level',
        'exp',
        'exp_to_next_level',
        'reward_points',
    ];

    public function progress($exp): float|int
    {
        return $exp / $this->exp_to_next_level * 100;
    }


}
