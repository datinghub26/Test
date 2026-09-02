<?php

namespace Database\Seeders;

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(LevelSeeder::class);
        $this->call(StreakSeeder::class);
        $this->call(LeaderboardSeeder::class);

        if (!app()->isProduction()) {
            $user = User::create([
                'email' => 'admin@ziadt.dev',
                'password' => \Hash::make('password'),
                'avatar' => 3,
                'username' => 'admin',
                'role' => 'admin',
                'points' => 99999,
            ]);

            $user->referralData()->create([
                'referral_code' => AuthController::generateReferralCode(),
            ]);

            $user->geo()->create([
                'ip' => fake()->ipv4,
                'country_code' => fake()->countryCode,
                'user_agent' => fake()->userAgent,
            ]);
        }
    }
}
