<?php

namespace App\Filament\Resources\CampaignUserResource\Pages;

use App\Filament\Resources\CampaignUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCampaignUsers extends ManageRecords
{
    protected static string $resource = CampaignUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
