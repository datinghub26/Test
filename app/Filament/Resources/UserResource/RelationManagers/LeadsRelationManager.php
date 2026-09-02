<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;

class LeadsRelationManager extends RelationManager
{
    protected static string $relationship = 'leads';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->formatStateUsing(fn($record) => view('filament.offer-img-name', ['image' => $record->image, 'name' => $record->name])),
                Tables\Columns\TextColumn::make('points')
                    ->sortable()
                    ->numeric(),
                Tables\Columns\TextColumn::make('payout')
                    ->money('USD'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn($record) => $record->type === 'offer' ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('ip'),
                Tables\Columns\TextColumn::make('country_code')
                    ->formatStateUsing(fn(string $state): View => view(
                        'filament.flag', ['state' => $state],
                    ))
                    ->label('Country'),

                Tables\Columns\TextColumn::make('created_at')
                    ->since(),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(5)
            ->filters([
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
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
}
