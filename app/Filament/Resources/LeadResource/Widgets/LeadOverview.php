<?php

namespace App\Filament\Resources\LeadResource\Widgets;

use App\Models\Lead;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Leads', Lead::where('type', 'offer')->count())
                ->descriptionIcon('heroicon-o-rocket-launch', IconPosition::Before)
                ->description('Total number of offer leads')
                ->color('success'),

            Stat::make('Today Leads', Lead::where('type', 'offer')
                ->whereDate('created_at', now()->timezone('Indian/Chagos')->toDateString())
                ->count())
                ->descriptionIcon('heroicon-o-rocket-launch', IconPosition::Before)
                ->description('Number of offer leads today')
                ->color('info'),
        ];
    }
}
