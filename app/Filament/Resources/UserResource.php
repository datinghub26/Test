<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Tables\Columns\UserColumn;
use DeviceDetector\DeviceDetector;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationBadge(): ?string
    {
        $count = User::whereDate('created_at', 'today')->count();
        return $count > 0 ? $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->unique('users', 'email', ignoreRecord: true)
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->visibleOn('create')
                    ->required(fn(string $context): bool => $context === 'create')
                    ->password()
                    ->maxLength(255),
                Forms\Components\TextInput::make('username')
                    ->visibleOn('edit')
                    ->required(fn(string $context): bool => $context === 'edit')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('role')
                    ->options([
                        'user' => 'User',
                        'admin' => 'Admin',
                    ])
                    ->default('user')
                    ->required(),
                Forms\Components\TextInput::make('avatar')
                    ->visibleOn('edit')
                    ->required(fn(string $context): bool => $context === 'edit')
                    ->default(rand(1, 35))
                    ->numeric(),
                Forms\Components\TextInput::make('level')
                    ->visibleOn('edit')
                    ->required(fn(string $context): bool => $context === 'edit')
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('exp')
                    ->visibleOn('edit')
                    ->required(fn(string $context): bool => $context === 'edit')
                    ->numeric()
                    ->default(45),
                Forms\Components\TextInput::make('points')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('privacy')
                    ->visibleOn('edit')
                    ->required(fn(string $context): bool => $context === 'edit'),
                Forms\Components\Toggle::make('is_banned')
                    ->visibleOn('edit')
                    ->required(fn(string $context): bool => $context === 'edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                UserColumn::make('user')
                    ->searchable(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->state(fn($record) => (bool)$record->email_verified_at)
                    ->boolean(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn($record) => match ($record->role) {
                        'user' => 'primary',
                        'admin' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('level')
                    ->badge()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('points')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('privacy')
                    ->boolean(),
                Tables\Columns\TextColumn::make('geo.country_code')
                    ->formatStateUsing(fn(string $state): View => view(
                        'filament.flag', ['state' => $state],
                    ))
                    ->label('Country'),
//                Tables\Columns\TextColumn::make('geo.user_agent')
//                    ->formatStateUsing(function (string $state): View {
//                        $dd = new DeviceDetector($state);
//                        $dd->parse();
//                        return view('filament.device', ['dd' => $dd]);
//                    })
//                    ->label('Device'),

            ])
            ->recordUrl(null)
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\LeadsRelationManager::class,
            RelationManagers\CashoutsRelationManager::class,
            RelationManagers\ReferralsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
