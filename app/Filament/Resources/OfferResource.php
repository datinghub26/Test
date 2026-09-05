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
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->helperText('Enable or disable this offer on the website.')
                    ->default(true),
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
                Tables\Columns\ImageColumn::make('image')
                    ->defaultImageUrl(asset('assets/img/placeholder-offer.svg')),
                Tables\Columns\TextColumn::make('provider')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),
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
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All Offers')
                    ->trueLabel('Active Only')
                    ->falseLabel('Disabled Only'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
                Tables\Filters\TernaryFilter::make('is_manual')
                    ->label('Manual Override'),
                Tables\Filters\SelectFilter::make('provider')
                    ->options(fn () => Offer::query()
                        ->whereNotNull('provider')
                        ->where('provider', '!=', '')
                        ->distinct()
                        ->pluck('provider')
                        ->filter(fn($val) => !blank($val))
                        ->mapWithKeys(fn($provider) => [(string) $provider => (string) $provider])
                        ->toArray()
                    ),
                Tables\Filters\SelectFilter::make('devices')
                    ->options(fn () => Offer::query()
                        ->whereNotNull('devices')
                        ->pluck('devices')
                        ->flatMap(function ($item) {
                            if (is_string($item)) {
                                $decoded = json_decode($item, true);
                                return is_array($decoded) ? $decoded : [$item];
                            }
                            return is_array($item) ? $item : [$item];
                        })
                        ->filter(fn($val) => !blank($val))
                        ->unique()
                        ->mapWithKeys(fn($device) => [(string) $device => ucfirst((string) $device)])
                        ->toArray()
                    )
                    ->query(function (Builder $query, array $data) {
                        return !blank($data['value'] ?? null) ? $query->whereJsonContains('devices', $data['value']) : $query;
                    }),
                Tables\Filters\SelectFilter::make('categories')
                    ->options(fn () => Offer::query()
                        ->whereNotNull('categories')
                        ->pluck('categories')
                        ->flatMap(function ($item) {
                            if (is_string($item)) {
                                $decoded = json_decode($item, true);
                                return is_array($decoded) ? $decoded : [$item];
                            }
                            return is_array($item) ? $item : [$item];
                        })
                        ->filter(fn($val) => !blank($val))
                        ->unique()
                        ->mapWithKeys(fn($category) => [(string) $category => (string) $category])
                        ->toArray()
                    )
                    ->query(function (Builder $query, array $data) {
                        return !blank($data['value'] ?? null) ? $query->whereJsonContains('categories', $data['value']) : $query;
                    }),
                Tables\Filters\SelectFilter::make('countries')
                    ->options(function () {
                        return Offer::query()
                            ->whereNotNull('countries')
                            ->pluck('countries')
                            ->flatMap(function ($item) {
                                if (is_string($item)) {
                                    $decoded = json_decode($item, true);
                                    return is_array($decoded) ? $decoded : [$item];
                                }
                                return is_array($item) ? $item : [$item];
                            })
                            ->filter(fn($val) => !blank($val))
                            ->unique()
                            ->mapWithKeys(function ($country) {
                                $c = trim((string) $country);
                                if (strtolower($c) === 'all') {
                                    return ['ALL' => 'ALL'];
                                }
                                return [$c => strtoupper($c)];
                            })
                            ->toArray();
                    })
                    ->query(function (Builder $query, array $data) {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }
                        if (strtoupper($data['value']) === 'ALL') {
                            return $query->where(function (Builder $q) {
                                $q->whereNull('countries')
                                    ->orWhereJsonContains('countries', 'all')
                                    ->orWhereJsonContains('countries', 'ALL')
                                    ->orWhereJsonLength('countries', 0);
                            });
                        }
                        return $query->whereJsonContains('countries', $data['value']);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('enable')
                        ->label('Enable Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn(\Illuminate\Database\Eloquent\Collection $records) => $records->each(fn(Offer $record) => $record->update(['is_active' => true]))),
                    Tables\Actions\BulkAction::make('disable')
                        ->label('Disable Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn(\Illuminate\Database\Eloquent\Collection $records) => $records->each(fn(Offer $record) => $record->update(['is_active' => false]))),
                    Tables\Actions\BulkAction::make('feature')
                        ->label('Feature Selected')
                        ->icon('heroicon-o-star')
                        ->action(fn(\Illuminate\Database\Eloquent\Collection $records) => $records->each(fn(Offer $record) => $record->update(['is_featured' => true]))),
                    Tables\Actions\BulkAction::make('unfeature')
                        ->label('Unfeature Selected')
                        ->icon('heroicon-o-star')
                        ->action(fn(\Illuminate\Database\Eloquent\Collection $records) => $records->each(fn(Offer $record) => $record->update(['is_featured' => false]))),
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
