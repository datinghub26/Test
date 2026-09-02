<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferResource\Pages;
use App\Filament\Resources\OfferResource\RelationManagers;
use App\Models\Offer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use ValentinMorice\FilamentJsonColumn\FilamentJsonColumn;

class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'Offers';
    protected static ?string $navigationGroup = 'Offers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('offer_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('provider')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->maxLength(1000),
                Forms\Components\TextInput::make('instructions'),
                Forms\Components\TextInput::make('requirements')
                    ->maxLength(255),
                Forms\Components\TextInput::make('image')
                    ->url(),
                Forms\Components\TextInput::make('link')
                    ->maxLength(255),
                Forms\Components\TextInput::make('points')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('payout')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_featured')
                    ->label('Featured Offer')
                    ->helperText('Pin this offer in the Top Offers / Featured section.')
                    ->default(false),
                Forms\Components\Toggle::make('is_manual')
                    ->label('Manual Override')
                    ->helperText('Manually prioritize in Top Offers selection.')
                    ->default(false),
                Forms\Components\TextInput::make('hold_duration_days')
                    ->label('Hold Duration (Days)')
                    ->helperText('Days to hold payout on completion (0 = instant release).')
                    ->numeric()
                    ->default(0)
                    ->suffix('Days'),
                Forms\Components\TextInput::make('categories')->json(),
                Forms\Components\TextInput::make('countries')->json(),
                Forms\Components\TextInput::make('devices')->json(),
                FilamentJsonColumn::make('events')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('offer_id')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('provider')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_featured')
                    ->label('Featured'),
                Tables\Columns\ToggleColumn::make('is_manual')
                    ->label('Manual'),
                Tables\Columns\TextColumn::make('hold_duration_days')
                    ->label('Hold (Days)')
                    ->badge()
                    ->color(fn($state) => $state > 0 ? 'warning' : 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('requirements')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('link')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('points')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payout')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider')
                    ->options(Offer::query()->pluck('provider')->flatten()->unique()->mapWithKeys(fn($provider) => [$provider => $provider])),
                Tables\Filters\SelectFilter::make('devices')
                    ->options(Offer::query()->pluck('devices')->flatten()->unique()->mapWithKeys(fn($device) => [$device => $device]))
                    ->query(function (Builder $query, array $data) {
                        return $data['value'] ? $query->whereJsonContains('devices', $data['value']) : $query;
                    }),
                Tables\Filters\SelectFilter::make('categories')
                    ->options(Offer::query()->pluck('categories')->flatten()->unique()->mapWithKeys(fn($device) => [$device => $device]))
                    ->query(function (Builder $query, array $data) {
                        return $data['value'] ? $query->whereJsonContains('categories', $data['value']) : $query;
                    }),
                Tables\Filters\SelectFilter::make('countries')
                    ->options(function () {
                        $countries = Offer::query()->pluck('countries')->flatten()->unique();
                        return $countries->mapWithKeys(function ($country) {
                            if ($country == 'all' || $country == 'ALL' || $country == null) {
                                return ['ALL' => 'ALL'];
                            }

                            return [$country => $country];
                        });


//                        dd(Offer::query()->pluck('countries')->flatten()->unique()->mapWithKeys(fn($country) => [$country => $country]));
//                        return Offer::query()->pluck('countries')->flatten()->unique()->mapWithKeys(fn($country) => [$country => $country]);
                    })
                    ->query(function (Builder $query, array $data) {
                        return $data['value'] ? $query->whereJsonContains('countries', $data['value']) : $query;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOffers::route('/'),
            'create' => Pages\CreateOffer::route('/create'),
            'edit' => Pages\EditOffer::route('/{record}/edit'),
        ];
    }
}
