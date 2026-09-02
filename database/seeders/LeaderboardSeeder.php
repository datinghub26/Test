<?php

namespace Database\Seeders;

use App\Models\LeaderboardConfig;
use App\Models\LeaderboardReward;
use Illuminate\Database\Seeder;

class LeaderboardSeeder extends Seeder
{
    public function run(): void
    {
        LeaderboardReward::truncate();

        $rewards = [
            ['rank' => 1, 'reward_points' => 5000],
            ['rank' => 2, 'reward_points' => 3500],
            ['rank' => 3, 'reward_points' => 2000],
            ['rank' => 4, 'reward_points' => 1000],
            ['rank' => 5, 'reward_points' => 500],
            ['rank' => 6, 'reward_points' => 250],
            ['rank' => 7, 'reward_points' => 150],
            ['rank' => 8, 'reward_points' => 150],
            ['rank' => 9, 'reward_points' => 150],
            ['rank' => 10, 'reward_points' => 150],
            ['rank' => 11, 'reward_points' => 100],
            ['rank' => 12, 'reward_points' => 100],
            ['rank' => 13, 'reward_points' => 100],
            ['rank' => 14, 'reward_points' => 100],
            ['rank' => 15, 'reward_points' => 100],
        ];

        foreach ($rewards as $reward)
            LeaderboardReward::create($reward);
    }
}
