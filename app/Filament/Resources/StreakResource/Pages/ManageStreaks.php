<?php

namespace App\Filament\Resources\StreakResource\Pages;

use App\Filament\Resources\StreakResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStreaks extends ManageRecords
{
    protected static string $resource = StreakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
