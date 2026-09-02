<?php

namespace App\Filament\Resources\CashoutRequestResource\Widgets;

use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CashoutRequestOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pending Requests', \App\Models\CashoutRequest::where('status', 'pending')->count())
                ->description('Total number of cashout requests')
                ->descriptionIcon('heroicon-o-stop-circle', IconPosition::Before)
                ->color('warning'),

            Stat::make('Approved Requests', \App\Models\CashoutRequest::where('status', 'approved')->count())
                ->description('Total number of approved cashout requests')
                ->descriptionIcon('heroicon-o-check-circle', IconPosition::Before)
                ->color('success'),

            Stat::make('Rejected Requests', \App\Models\CashoutRequest::where('status', 'rejected')->count())
                ->description('Total number of rejected cashout requests')
                ->descriptionIcon('heroicon-o-x-circle', IconPosition::Before)
                ->color('danger'),
        ];
    }
}
