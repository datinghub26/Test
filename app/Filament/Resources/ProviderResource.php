<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderResource\Pages;
use App\Filament\Resources\ProviderResource\RelationManagers;
use App\Models\Level;
use App\Models\Provider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;

class ProviderResource extends Resource
{
    protected static ?string $model = Provider::class;
    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?string $label = 'Provider';
    protected static ?string $navigationGroup = 'Offers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\ColorPicker::make('bg_color')
                    ->default('#45b6f2')
                    ->label('Background Color')
                    ->required(),
                RadioDeck::make('type')
                    ->options([
                        'offer' => 'Offer',
                        'survey' => 'Survey',
                    ])
                    ->default('offer')
                    ->color('primary')
                    ->icons([
                        'offer' => 'heroicon-o-sparkles',
                        'survey' => 'heroicon-o-cursor-arrow-rays',
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\FileUpload::make('image')
                    ->label('Logo')
                    ->disk('public')
                    ->directory('providers')
                    ->visibility('public')
                    ->image()
                    ->required(fn(string $context): bool => $context === 'create')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('badge')
                    ->helperText('Badge shown on the top right corner of the offer card')
                    ->label('Badge'),
                Forms\Components\ColorPicker::make('badge_bg_color')
                    ->default('#ea5455')
                    ->label('Badge Background Color'),
                Forms\Components\TextInput::make('url')
                    ->columnSpanFull()
                    ->helperText('Replace the user id part with {user_id}')
                    ->label('URL'),
                Forms\Components\Checkbox::make('show_rate')
                    ->helperText('Show rate stars on offer card')
                    ->label('Show Rate'),
                Forms\Components\TextInput::make('rate')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->label('Rate'),
                Forms\Components\Select::make('unlock_level')
                    ->options(Level::pluck('level', 'id')->toArray())
                    ->required()
                    ->default('1'),
                Forms\Components\Section::make([
                    Forms\Components\Toggle::make('is_mobile')
                        ->default(false)
                        ->label('Is Mobile'),
                    Forms\Components\KeyValue::make('sdk_data')
                        ->default(fn($record) => [
                            'appName' => $record?->name ?? '',
                            'appId' => '',
                        ])
                        ->label('SDK Data'),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')
                    ->label('Logo'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Name'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn($record) => $record->type === 'offer' ? 'success' : 'info')
                    ->icons([
                        'heroicon-o-sparkles' => 'offer',
                        'heroicon-o-cursor-arrow-rays' => 'survey',
                    ])
                    ->label('Type'),
                Tables\Columns\TextColumn::make('badge')
                    ->badge()
                    ->color(fn(Provider $provider) => Color::hex($provider->badge_bg_color))
                    ->label('Badge'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),
                Tables\Columns\TextColumn::make('url')
                    ->limit(50)
                    ->wrap()
                    ->tooltip('Click to copy')
                    ->copyable()
                    ->label('URL'),
                Tables\Columns\IconColumn::make('show_rate')
                    ->boolean()
                    ->label('Show Rate'),
                Tables\Columns\TextColumn::make('rate')
                    ->state(fn(Provider $provider) => $provider->rate ?? 0 . '/5')
                    ->label('Rate'),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->filters([
                //
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
            'index' => Pages\ManageProviders::route('/'),
        ];
    }
}
