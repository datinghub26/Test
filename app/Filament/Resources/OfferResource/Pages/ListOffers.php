<?php

namespace App\Filament\Resources\OfferResource\Pages;

use App\Filament\Pages\Settings\OffersSettings;
use App\Filament\Resources\OfferResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOffers extends ListRecords
{
    protected static string $resource = OfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('Configure')
                ->label('Configure')
                ->icon('heroicon-o-cog')
                ->color('secondary')
                ->url(OffersSettings::getUrl()),
        ];
    }
}
