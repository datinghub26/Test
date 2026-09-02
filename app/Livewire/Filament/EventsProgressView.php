<?php

namespace App\Livewire\Filament;

use App\Models\Campaigns\CampaignUserEventProgress;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class EventsProgressView extends Component implements HasForms, HasTable, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithInfolists;

    public $record;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => CampaignUserEventProgress::query()->where('user_id', $this->record->user_id))
            ->columns([
                Tables\Columns\TextColumn::make('campaign.name'),
                Tables\Columns\TextColumn::make('event.name'),
                Tables\Columns\IconColumn::make('is_completed')
                    ->boolean()
                    ->label('Completed'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false)
            ->queryStringIdentifier('events-progress');
    }

    public function render(): View
    {
        return view('livewire.filament.events-progress-view');
    }
}
