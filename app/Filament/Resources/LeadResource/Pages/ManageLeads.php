<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ManageLeads extends ManageRecords
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            ExportBulkAction::make(),
//            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LeadResource\Widgets\LeadOverview::class
        ];
    }

//    public function getTabs(): array
//    {
//        return [
//            'all' => Tab::make(),
//            'offers' => Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'offer')),
//        ];
//    }
}
