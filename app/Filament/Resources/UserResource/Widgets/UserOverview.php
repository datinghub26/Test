<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::count())
                ->descriptionIcon('heroicon-o-users', IconPosition::Before)
                ->description('Total number of users')
                ->color('success'),

            Stat::make('Today Users', User::whereDate('created_at', now())->count())
                ->descriptionIcon('heroicon-o-user-plus', IconPosition::Before)
                ->description('Number of users registered today')
                ->color('info'),

            Stat::make('Banned Users', User::where('is_banned', 1)->count())
                ->descriptionIcon('heroicon-o-no-symbol', IconPosition::Before)
                ->description('Number of banned users')
                ->color('danger'),
        ];
    }
}
