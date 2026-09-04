<?php

namespace App\Livewire\User\Pages;

use App\Models\CashoutRequest;
use App\Models\Offer;
use App\Models\Provider;
use App\Models\User;
use Date;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class HomePage extends Component
{
    public int $availableOffersCount = 0;

    public function render()
    {
        if (auth()->check()) {
            redirect()->route('earn');
        }

        try {
            $offerPartners = Provider::offer()->active()->ordered()->get();
        } catch (\Throwable $e) {
            $offerPartners = collect();
        }

        try {
            $surveyPartners = Provider::survey()->active()->ordered()->get();
        } catch (\Throwable $e) {
            $surveyPartners = collect();
        }

        try {
            $topOffers = Offer::getTopHybridOffers(3);
        } catch (\Throwable $e) {
            $topOffers = collect();
        }

        try {
            $stats = $this->statistics();
        } catch (\Throwable $e) {
            $stats = [
                'totalEarnedYesterday' => '0.00',
                'totalEarnedYesterdayAvg' => '0.00',
                'totalEarned' => '0.00',
                'userLatestCashoutTimeAvg' => '00h 00m',
            ];
        }

        return view('livewire.user.pages.home-page')->with([
            'offerPartners' => $offerPartners,
            'surveyPartners' => $surveyPartners,
            'topOffers' => $topOffers,
            'statistics' => $stats,
            'faqsInfo' => FaqPage::faqs(),
        ])->layout('layouts.app');
    }

    public function loadAvailableOffersCount(): void
    {
        try {
            $cc = strtoupper(country_code() ?? 'US');
            $this->availableOffersCount = Offer::whereJsonContains('countries', $cc)->count();
        } catch (\Throwable $e) {
            $this->availableOffersCount = 0;
        }
    }

    protected function statistics(): array
    {
        try {
            $isSqlite = DB::connection()->getDriverName() === 'sqlite';
            $diffRaw = $isSqlite
                ? 'AVG((strftime("%s", updated_at) - strftime("%s", created_at)) / 60) as avg_minutes'
                : 'AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as avg_minutes';

            $userLatestCashoutTimeAvg = CashoutRequest::where('status', '!=', 'pending')
                ->whereNotNull('updated_at')
                ->selectRaw($diffRaw)
                ->value('avg_minutes');

            $totalEarnedYesterday = User::query()
                ->join('leads', 'users.id', '=', 'leads.user_id')
                ->whereDate('leads.created_at', Date::yesterday())
                ->sum('leads.points');

            $totalEarnedYesterday = number_format(to_money($totalEarnedYesterday), 2);
            $totalEarnedYesterdayAvg = User::query()
                ->join('leads', 'users.id', '=', 'leads.user_id')
                ->whereDate('leads.created_at', Date::yesterday())
                ->avg('leads.points');

            $totalEarnedYesterdayAvg = number_format(to_money($totalEarnedYesterdayAvg), 2);

            $totalEarned = User::query()
                ->join('leads', 'users.id', '=', 'leads.user_id')
                ->sum('leads.points');

            $totalEarned = number_format(to_money($totalEarned), 2);

            return [
                'totalEarnedYesterday' => $totalEarnedYesterday,
                'totalEarnedYesterdayAvg' => $totalEarnedYesterdayAvg,
                'totalEarned' => $totalEarned,
                'userLatestCashoutTimeAvg' => $userLatestCashoutTimeAvg ? gmdate('H\h i\m', $userLatestCashoutTimeAvg * 60) : '00h 00m',
            ];
        } catch (\Throwable $e) {
            return [
                'totalEarnedYesterday' => '0.00',
                'totalEarnedYesterdayAvg' => '0.00',
                'totalEarned' => '0.00',
                'userLatestCashoutTimeAvg' => '00h 00m',
            ];
        }
    }

}
