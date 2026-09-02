<?php

namespace App\Filament\Resources\CashoutRequestResource\Pages;

use App\Filament\Resources\CashoutRequestResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCashoutRequests extends ListRecords
{
    protected static string $resource = CashoutRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'pending' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending')),
            'approved' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'approved')),
            'rejected' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'rejected')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CashoutRequestResource\Widgets\CashoutRequestOverview::class
        ];
    }
}
