<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignResource\Pages;
use App\Models\Campaigns\Campaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationGroup = 'Offers';
    protected static ?int $navigationSort = 99;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->placeholder('Provider Name')
                    ->label('Name')
                    ->required(),
                Forms\Components\TextInput::make('url')
                    ->placeholder('https://example.com?click_id={click_id}&secretKey=********')
                    ->helperText('replace click_id marco with {click_id}')
                    ->label('URL')
                    ->required(),
                Forms\Components\ToggleButtons::make('is_active')
                    ->label('Status')
                    ->options([
                        0 => 'Inactive',
                        1 => 'Active',
                    ])
                    ->default(1)
                    ->boolean()
                    ->inline()
                    ->required(),
                Forms\Components\ToggleButtons::make('has_multi_event')
                    ->label('Has Multi Event')
                    ->live()
                    ->options([
                        0 => 'No',
                        1 => 'Yes',
                    ])
                    ->default(0)
                    ->boolean()
                    ->inline()
                    ->required(),
                Forms\Components\TextInput::make('event_macro')
                    ->label('Event Macro')
                    ->placeholder('{event_name}')
                    ->hidden(fn(Get $get) => !$get('has_multi_event'))
                    ->required(fn(Get $get) => $get('has_multi_event')),

                Forms\Components\Repeater::make('events')
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->placeholder('completed_registration')
                            ->label('Name')
                            ->required(),
                        Forms\Components\Select::make('action')
                            ->live()
                            ->label('Action')
                            ->options([
                                'registration' => 'Registration',
                                'points' => 'Points',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('target')
                            ->integer()
                            ->label('Target')
                            ->hidden(fn(Get $get) => $get('action') !== 'points')
                            ->required(fn(Get $get) => $get('action') === 'points'),
                        Forms\Components\ToggleButtons::make('is_active')
                            ->label('Status')
                            ->options([
                                0 => 'Inactive',
                                1 => 'Active',
                            ])
                            ->default(1)
                            ->boolean()
                            ->inline()
                            ->required(),
                    ])
                    ->columns(4)
                    ->addActionLabel('Add Event')
                    ->addable(fn(Get $get) => $get('has_multi_event'))
                    ->columnSpanFull(),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Status')
                    ->sortable(),
                Tables\Columns\IconColumn::make('has_multi_event')
                    ->boolean()
                    ->label('CPE')
                    ->sortable(),
                Tables\Columns\TextColumn::make('events')
                    ->label('Events')
                    ->getStateUsing(fn($record) => $record->events->pluck('name')->join(', ')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Track Url')
                    ->icon('heroicon-o-sparkles')
                    ->color('info')
                    ->action(function ($record, $livewire) {
                        $url = route('campaign-tracking') . "?campaign=$record->identifier&source={source}&click={click_id}";
                        $livewire->js('window.navigator.clipboard.writeText("' . $url . '");');
                        Notification::make()
                            ->title("Copied to clipboard")
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCampaigns::route('/'),
        ];
    }
}
