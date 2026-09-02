<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignUserResource\Pages;
use App\Filament\Resources\CampaignUserResource\RelationManagers;
use App\Livewire\Filament\EventsProgressView;
use App\Models\Campaigns\CampaignUser;
use App\Tables\Columns\UserColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CampaignUserResource extends Resource
{
    protected static ?string $model = CampaignUser::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Offers';
    protected static ?int $navigationSort = 100;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                UserColumn::make('user')
                    ->searchable(),
                Tables\Columns\TextColumn::make('campaign.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Progress')
                    ->getStateUsing(function (CampaignUser $record) {
                        $events = $record->campaign?->events;
                        if ($events === null)
                            return '0/0';

                        return $record->eventProgress()->where('is_completed', true)->count() . '/' . $events->count();
                    }),
                Tables\Columns\IconColumn::make('is_completed')
                    ->boolean()
                    ->label('Completed'),
                Tables\Columns\TextColumn::make('leads_sum_points')
                    ->label('Points')
                    ->sum('leads', 'points')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View Progress'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Livewire::make(EventsProgressView::class)
                    ->columnSpanFull()
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCampaignUsers::route('/'),
        ];
    }
}
