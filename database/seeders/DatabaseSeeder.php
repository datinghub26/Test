<?php

namespace Database\Seeders;

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Database\Seeder;

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

        $user = User::firstOrCreate(
            ['email' => 'admin@ziadt.dev'],
            [
                'password' => \Hash::make('password'),
                'avatar' => 3,
                'username' => 'admin',
                'role' => 'admin',
                'points' => 99999,
            ]
        );

        if (!$user->referralData()->exists()) {
            $user->referralData()->create([
                'referral_code' => AuthController::generateReferralCode(),
            ]);
        }

        if (!$user->geo()->exists()) {
            $user->geo()->create([
                'ip' => '127.0.0.1',
                'country_code' => 'US',
                'user_agent' => 'Mozilla/5.0',
            ]);
        }
    }
}
