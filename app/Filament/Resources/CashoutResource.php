<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashoutResource\Pages;
use App\Filament\Resources\CashoutResource\RelationManagers;
use App\Models\Cashout;
use App\Models\CashoutMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CashoutResource extends Resource
{
    protected static ?string $model = CashoutMethod::class;
    protected static ?string $label = 'Cashout';
    protected static ?string $navigationGroup = 'Cashouts';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-on-square-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->placeholder('Enter the name of the cashout method'),

                    Forms\Components\ColorPicker::make('bg_color')
                        ->label('Background Color')
                        ->required(),

                    Forms\Components\TextInput::make('fee')
                        ->prefix('%')
                        ->default(fn() => 0)
                        ->numeric()
                        ->maxValue(100)
                        ->label('Fee')
                        ->required()
                        ->placeholder('Enter the fee (in %)'),
                    Forms\Components\TextInput::make('minimum')
                        ->label('Minimum')
                        ->default(fn() => 1000)
                        ->required()
                        ->numeric()
                        ->placeholder('Enter the minimum amount'),
                    Forms\Components\TextInput::make('payment_title')
                        ->label('Payment Title')
                        ->placeholder('Your PayPal Address'),
                    Forms\Components\TextInput::make('payment_note')
                        ->label('Payment Note')
                        ->placeholder('Enter the payment note')
                ])
                    ->columnSpan(2),

                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('category')
                        ->label('Category')
                        ->placeholder('Cash & Crypto')
                        ->required(),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Status')
                        ->default(true),

                    Forms\Components\FileUpload::make('image')
                        ->label('Logo')
                        ->required(fn(string $context): bool => $context === 'create')
                        ->image(),
                ])->columnSpan(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Name'),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Logo'),
                Tables\Columns\TextColumn::make('category')
                    ->label('Category')
                    ->badge(),
                Tables\Columns\ColorColumn::make('bg_color')
                    ->label('BG Color'),
                Tables\Columns\TextInputColumn::make('payment_title')
                    ->label('Payment Title'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status'),
                Tables\Columns\TextColumn::make('fee')
                    ->state(fn(CashoutMethod $record) => $record->fee . '%')
                    ->numeric()
                    ->label('Fee'),
                Tables\Columns\TextColumn::make('minimum')
                    ->numeric()
                    ->label('Minimum'),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        true => 'Active',
                        false => 'Inactive',
                    ])
                    ->label('Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()
                    ->label('Prices')
                    ->icon('heroicon-o-currency-dollar')

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
            RelationManagers\PricesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashouts::route('/'),
            'edit' => Pages\EditCashout::route('/{record}/edit'),
        ];
    }
}
