<?php

namespace Database\Seeders;

use App\Models\Streak;
use Illuminate\Database\Seeder;

class StreakSeeder extends Seeder
{
    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Streak::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Streak::insert([
            [
                'day' => 1,
                'points' => 25,
            ],
            [
                'day' => 2,
                'points' => 50,
            ],
            [
                'day' => 3,
                'points' => 100,
            ],
            [
                'day' => 4,
                'points' => 200,
            ],
            [
                'day' => 5,
                'points' => 400,
            ],
            [
                'day' => 6,
                'points' => 800,
            ],
            [
                'day' => 7,
                'points' => 1000,
            ]
        ]);
    }
}
