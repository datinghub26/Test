<?php

namespace App\Filament\Resources\PendingOfferResource\Pages;

use App\Filament\Resources\PendingOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePendingOffers extends ManageRecords
{
    protected static string $resource = PendingOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
