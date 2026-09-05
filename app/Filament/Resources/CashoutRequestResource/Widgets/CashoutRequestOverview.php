<?php

namespace App\Filament\Resources\CashoutRequestResource\Widgets;

use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CashoutRequestOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $pendingCount = \App\Models\CashoutRequest::where('status', 'pending')->count();
        $pendingSum = \App\Models\CashoutRequest::where('status', 'pending')->sum('amount');
        $approvedCount = \App\Models\CashoutRequest::where('status', 'approved')->count();
        $approvedSum = \App\Models\CashoutRequest::where('status', 'approved')->sum('amount');
        $rejectedCount = \App\Models\CashoutRequest::where('status', 'rejected')->count();

        return [
            Stat::make('Pending Requests', $pendingCount)
                ->description('Total: ' . number_format($pendingSum) . ' ERC')
                ->descriptionIcon('heroicon-o-stop-circle', IconPosition::Before)
                ->color('warning'),

            Stat::make('Approved Requests', $approvedCount)
                ->description('Total: ' . number_format($approvedSum) . ' ERC')
                ->descriptionIcon('heroicon-o-check-circle', IconPosition::Before)
                ->color('success'),

            Stat::make('Rejected Requests', $rejectedCount)
                ->description('Total rejected requests')
                ->descriptionIcon('heroicon-o-x-circle', IconPosition::Before)
                ->color('danger'),
        ];
    }
}
