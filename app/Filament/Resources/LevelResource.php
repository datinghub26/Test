<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LevelResource\Pages;
use App\Filament\Resources\LevelResource\RelationManagers;
use App\Models\Level;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LevelResource extends Resource
{
    protected static ?string $model = Level::class;
    protected static ?string $navigationIcon = 'heroicon-o-hashtag';
    protected static ?string $navigationGroup = 'Rewards';
    protected static ?int $navigationSort = 200;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('level')
                    ->default(fn() => Level::max('level') + 1)
                    ->unique()
                    ->required()
                    ->numeric(),
                TextInput::make('exp')
                    ->default(fn() => Level::max('exp') + 120)
                    ->required()
                    ->numeric(),
                TextInput::make('exp_to_next_level')
                    ->default(fn() => Level::max('exp_to_next_level') + 120)
                    ->required()
                    ->numeric(),
                TextInput::make('reward_points')
                    ->default(10)
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('level')
                    ->badge()
                    ->formatStateUsing(fn($record) => "Level $record->level")
                    ->sortable(),
                Tables\Columns\TextColumn::make('exp')
                    ->label('EXP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('exp_to_next_level')
                    ->label('Next Level EXP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reward_points')
                    ->label('Reward Points')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn($record) => "$record->reward_points point")
                    ->sortable(),
            ])
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
            'index' => Pages\ManageLevels::route('/'),
        ];
    }
}
