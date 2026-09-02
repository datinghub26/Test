<?php

namespace App\Filament\Resources\NavbarButtonResource\Pages;

use App\Filament\Resources\NavbarButtonResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNavbarButtons extends ManageRecords
{
    protected static string $resource = NavbarButtonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
