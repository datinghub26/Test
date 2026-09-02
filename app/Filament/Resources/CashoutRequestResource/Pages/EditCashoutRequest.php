<?php

namespace App\Filament\Resources\CashoutRequestResource\Pages;

use App\Filament\Resources\CashoutRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashoutRequest extends EditRecord
{
    protected static string $resource = CashoutRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
