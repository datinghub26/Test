<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('levels')->truncate();

        $rewardByPoints = 0;
        $levels = [];
        for ($i = 0; $i < 35; $i++) {
            $exp = $i * 120;
            $expToNext = ($i + 1) * 120;

            if ($i % 5 === 1)
                $rewardByPoints = 10;

            if ($i != 0 && $i % 5 === 0)
                $rewardByPoints = 250;
            else
                $rewardByPoints = $rewardByPoints + 10;


            $levels[] = [
                'level' => $i + 1,
                'exp' => $exp,
                'exp_to_next_level' => $expToNext,
                'reward_points' => $rewardByPoints,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('levels')->insert($levels);
    }
}
