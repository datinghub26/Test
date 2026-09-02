<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\LeaderboardReward;
use Illuminate\Console\Command;

class AwardLeaderboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:award-leaderboard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Award the leaderboard winners';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (setting('leaderboard.enable_rewards', true)) {
            $rank_counts = LeaderboardReward::max('rank');
            $leaders = Lead::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->select(['user_id', \DB::raw('SUM(points) as points')])
                ->groupBy('user_id')
                ->orderBy('points', 'desc')
                ->limit($rank_counts)
                ->get();

            foreach ($leaders as $index => $leader) {
                $rank = $index + 1;
                $reward_points = LeaderboardReward::where('rank', $rank)->first()->reward_points ?? 0;
                $leader->user->updateUserPointsAndLevel($reward_points);
                $leader->user->leads()->create([
                    'points' => $reward_points,
                    'payout' => 0,
                    'type' => 'leaderboard',
                    'name' => "Leaderboard Rank #$rank Reward",
                ]);
                $leader->user->addNotification(
                    "Leaderboard Rank #$rank",
                    "You have been awarded $reward_points points for being in the top $rank of the leaderboard.",
                );
            }
        }
    }
}
