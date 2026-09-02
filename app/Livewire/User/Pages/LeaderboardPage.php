<?php

namespace App\Livewire\User\Pages;

use App\Models\Lead;
use App\Models\LeaderboardReward;
use Livewire\Component;

class LeaderboardPage extends Component
{
    public function render()
    {
        $start = now()->startOfWeek();
        $end = now()->endOfWeek();
        $rank_counts = LeaderboardReward::max('rank');
        $leaders = Lead::/*whereBetween('created_at', [$start, $end])*/
            select(['user_id', \DB::raw('SUM(points) as points')])
            ->groupBy('user_id')
            ->orderBy('points', 'desc')
            ->limit($rank_counts)
            ->get();
        return view('livewire.user.pages.leaderboard-page', compact('leaders', 'start', 'end'))->layout('layouts.app');
    }

    public function getPrize($rank)
    {
        return LeaderboardReward::where('rank', $rank)?->first()?->reward_points ?? 0;
    }
}
