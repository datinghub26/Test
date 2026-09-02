<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendingOfferResource\Pages;
use App\Models\Offer;
use App\Models\PendingOffer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PendingOfferResource extends Resource
{
    protected static ?string $model = PendingOffer::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Pending Offers';
    protected static ?string $modelLabel = 'Pending Offer Rule';
    protected static ?string $navigationGroup = 'Offers';
    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make([
                    Forms\Components\TextInput::make('offer_id')
                        ->label('Offer ID')
                        ->helperText('The unique Offer ID or Campaign ID from the provider.')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\TextInput::make('hold_duration_days')
                        ->label('Hold Duration (Days)')
                        ->helperText('Number of days to hold user reward payout before releasing.')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->default(7)
                        ->suffix('Days'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Rule Active')
                        ->helperText('Enable or disable holding payouts for this offer.')
                        ->default(true),

                    Forms\Components\Textarea::make('description')
                        ->label('Notes / Reason')
                        ->placeholder('e.g., High-risk offer hold period / anti-fraud check')
                        ->maxLength(255)
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('offer_id')
                    ->label('Offer ID')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('offer.title')
                    ->label('Offer Title')
                    ->placeholder('Custom/Direct ID')
                    ->limit(35)
                    ->searchable(),

                Tables\Columns\TextColumn::make('hold_duration_days')
                    ->label('Hold Duration')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn($state) => "{$state} Days")
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Notes')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Rules Only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManagePendingOffers::route('/'),
        ];
    }
}
