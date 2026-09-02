<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use App\Tables\Columns\UserColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function getNavigationBadge(): ?string
    {
        return Ticket::where('status', 'open')->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('uuid')
                    ->label('UUID')
                    ->maxLength(36),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'id')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('message')
                    ->maxLength(255),
                Forms\Components\TextInput::make('priority')
                    ->required()
                    ->maxLength(255)
                    ->default('low'),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('open'),
                Forms\Components\Toggle::make('is_resolved')
                    ->required(),
                Forms\Components\Toggle::make('is_locked')
                    ->required(),
                Forms\Components\TextInput::make('assigned_to')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                UserColumn::make('user')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'low' => 'success',
                        'medium' => 'warning',
                        'high' => 'danger',
                        default => 'primary',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'open' => 'warning',
                        'closed' => 'secondary',
                        default => 'primary',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('Message')
                    ->icon('heroicon-s-chat-bubble-left-right')
                    ->action(fn(Ticket $record) => redirect(self::getUrl('message', ['ticket' => $record]))),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle-status')
                    ->label(fn(Ticket $record) => $record->isClosed() ? 'Open' : 'Close')
                    ->color(fn(Ticket $record) => $record->isClosed() ? 'success' : 'danger')
                    ->icon(fn(Ticket $record) => $record->isClosed() ? 'heroicon-s-lock-open' : 'heroicon-s-lock-closed')
                    ->action(fn(Ticket $record) => $record->isClosed() ? $record->reopen() : $record->close())
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(null)
            ->recordAction(null)
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
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
            'message' => Pages\MessageTicket::route('/{ticket}/message'),
        ];
    }
}
