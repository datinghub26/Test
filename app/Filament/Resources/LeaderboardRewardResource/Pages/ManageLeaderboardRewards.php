<?php

namespace App\Filament\Resources\LeaderboardRewardResource\Pages;

use App\Filament\Resources\LeaderboardRewardResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLeaderboardRewards extends ManageRecords
{
    protected static string $resource = LeaderboardRewardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
