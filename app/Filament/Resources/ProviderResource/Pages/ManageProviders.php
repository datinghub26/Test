<?php

namespace App\Filament\Resources\ProviderResource\Pages;

use App\Filament\Resources\ProviderResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageProviders extends ManageRecords
{
    protected static string $resource = ProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'offer' => Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'offer')),
            'survey' => Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'survey')),
        ];
    }

}
