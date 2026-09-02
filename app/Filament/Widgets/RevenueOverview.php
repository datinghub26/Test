<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RevenueOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalRevenue = Lead::where('type', 'offer')->sum('payout');
        $todayRevenue = Lead::where('type', 'offer')->whereDate('created_at', today())->sum('payout');
        $totalLeads = Lead::where('type', 'offer')->count();
        $activeUsersToday = User::whereDate('last_activity_at', today())->count();
        $activeUsersThisWeek = User::whereBetween('last_activity_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $activeUsersThisMonth = User::whereBetween('last_activity_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
        return [
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('Total revenue earned')
                ->descriptionIcon('heroicon-o-currency-dollar', IconPosition::Before)
                ->color('success'),

            Stat::make("Today's Revenue", '$' . number_format($todayRevenue, 2))
                ->description("Today's revenue earned")
                ->descriptionIcon('heroicon-o-calendar', IconPosition::Before)
                ->color('info'),

            Stat::make('Total Leads', $totalLeads)
                ->description('Total number of leads')
                ->descriptionIcon('heroicon-o-rocket-launch', IconPosition::Before)
                ->color('warning'),


            Stat::make('Active Users Today', $activeUsersToday)
                ->descriptionIcon('heroicon-o-user', IconPosition::Before)
                ->description('Active users today')
                ->color('success'),

            Stat::make('Active Users This Week', $activeUsersThisWeek)
                ->descriptionIcon('heroicon-o-user', IconPosition::Before)
                ->description('Active users this week')
                ->color('warning'),

            Stat::make('Active Users This Month', $activeUsersThisMonth)
                ->descriptionIcon('heroicon-o-user', IconPosition::Before)
                ->description('Active users this month')
                ->color('success'),

        ];
    }
}
