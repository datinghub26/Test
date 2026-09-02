<?php

namespace App\Livewire\User\Pages;

use App\Models\Streak;
use Livewire\Component;

class RewardsPage extends Component
{
    public function render()
    {
        $streaks = Streak::all();
        return view('livewire.user.pages.rewards-page', compact('streaks'))->layout('layouts.app');
    }

    public function isUserFirstStreak(): bool
    {
        $last_claim = auth()->user()?->streaksClaims()?->latest()->first();
        return !$last_claim;
    }

    public function streakEndTime()
    {
        $last_claim = auth()->user()?->streaksClaims()?->latest()->first();
        $end_timestamp = $last_claim?->created_at->addHours(setting('streaks.required_hours', 24));
        return $last_claim ? $end_timestamp : now();
    }

    public function isUserAchievedTargetToday(): bool
    {
        return auth()->user()?->leads()?->whereDate('created_at', today())->sum('points') >= setting('streaks.required_points', 1000);
    }

    public function isUserClaimedStreak($id): bool
    {
        return auth()->user()?->streaksClaims()?->where('streak_id', $id)->exists() ?? false;
    }

    public function isUserCanClaimStreak($id): bool
    {
        $streak = Streak::find($id);
        $last_claim = auth()->user()?->streaksClaims()?->latest()->first();
        if ($last_claim && $last_claim->streak_id == $id)
            return false;

        if ($last_claim && $last_claim->created_at->diffInHours(now()) > setting('streaks.required_hours', 24))
            return false;

        if ($last_claim && $last_claim->streak->day + 1 != $streak->day)
            return false;

        if (!$last_claim && $streak->day != 1)
            return false;

        $achieved_target = $this->isUserAchievedTargetToday();
        $is_claimed = $this->isUserClaimedStreak($id);
        $is_streak_ended = $last_claim && $last_claim?->created_at->diffInHours(now()) > setting('streaks.required_hours', 24);
        $is_claimed_today = $last_claim && $last_claim?->created_at->isToday();
        return $achieved_target && !$is_claimed && !$is_streak_ended && !$is_claimed_today;
    }

    public function isStreakEnded(): bool
    {
        $last_claim = auth()->user()?->streaksClaims()?->latest()->first();
        return $last_claim?->created_at->diffInHours(now()) > setting('streaks.required_hours', 24);
    }

    public function claimStreak($id): void
    {
        $streak = Streak::find($id);
        if (!$this->isUserCanClaimStreak($id)) {
            \Toaster::error('You can not claim this streak yet.');
            return;
        }

        if ($this->isUserClaimedStreak($id)) {
            \Toaster::error('You have already claimed this streak.');
            return;
        }

        auth()->user()?->streaksClaims()?->create([
            'streak_id' => $id,
            'points' => $streak->points,
        ]);

        auth()->user()->updateUserPointsAndLevel($streak->points);
        auth()->user()->addNotification('Streak Claimed', "You have claimed {$streak->points} points for completing the streak.");
        auth()->user()->leads()->create([
            'name' => 'Streak Reward',
            'points' => $streak->points,
            'payout' => 0,
            'type' => 'streak',
            'ip' => ip(),
            'country_code' => country_code(),
        ]);
        \Toaster::success('Streak claimed successfully.');
    }
}
