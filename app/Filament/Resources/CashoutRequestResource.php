<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashoutRequestResource\Pages;
use App\Filament\Resources\CashoutRequestResource\RelationManagers;
use App\Models\CashoutRequest;
use App\Tables\Columns\UserColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class CashoutRequestResource extends Resource
{
    protected static ?string $model = CashoutRequest::class;
    protected static ?string $label = 'Requests';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';
    protected static ?string $navigationGroup = 'Cashouts';
    protected static ?int $navigationSort = 100;

    public static function getNavigationBadge(): ?string
    {
        return CashoutRequest::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->relationship('user')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('User')
                            ->readOnly()
                            ->disabled(),
                    ]),
                Forms\Components\TextInput::make('method_name')
                    ->disabled(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Forms\Components\Textarea::make('rejection_reason')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                UserColumn::make('user')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('method_image')
                    ->label('Logo'),
                Tables\Columns\TextColumn::make('method_name')
                    ->label('Method')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(CashoutRequest $record) => match ($record->status) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
//                    ->default('pending'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->iconButton()
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->action(function (CashoutRequest $record) {
                        $record->update(['status' => 'approved']);
                        $record->user?->addNotification(
                            'Withdrawal Request Approved',
                            "Your cashout request of {$record->amount} ERC via {$record->method_name} has been processed successfully.",
                            'cashout_approved'
                        );
                    }),
                Tables\Actions\Action::make('reject')
                    ->fillForm(fn(CashoutRequest $record): array => ['rejection_reason' => $record->rejection_reason])
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason'),
                        Forms\Components\Toggle::make('refund')
                            ->label('Refund')
                            ->default(false),
                    ])
                    ->iconButton()
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (array $data, CashoutRequest $record) {
                        $record->status = 'rejected';
                        $record->rejection_reason = $data['rejection_reason'] ?? '';
                        if ($data['refund'] === true) {
                            $record->user->updateUserPointsAndLevel($record->amount);
                        }
                        $record->user?->addNotification('Withdrawal Request Rejected', "Your withdrawal request for {$record->amount} ERC has been rejected.", 'danger');
                        $record->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve')
                        ->requiresConfirmation()
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->action(fn(Collection $records) => $records->each(function (CashoutRequest $record) {
                            $record->update(['status' => 'approved']);
                            $record->user?->addNotification(
                                'Withdrawal Request Approved',
                                "Your cashout request of {$record->amount} ERC via {$record->method_name} has been processed successfully.",
                                'cashout_approved'
                            );
                        })),
                    Tables\Actions\BulkAction::make('reject')
                        ->requiresConfirmation()
                        ->color('danger')
                        ->icon('heroicon-o-x-mark')
                        ->form([
                            Forms\Components\Textarea::make('rejection_reason')
                                ->label('Rejection Reason'),
                            Forms\Components\Toggle::make('refund')
                                ->label('Refund')
                                ->default(false),
                        ])
                        ->action(function (array $data, Collection $records) {
                            $records->each(function (CashoutRequest $record) use ($data) {
                                $record->status = 'rejected';
                                $record->rejection_reason = $data['rejection_reason'] ?? '';
                                if ($data['refund'] === true) {
                                    $record->user->updateUserPointsAndLevel($record->amount);
                                }
                                $record->user?->addNotification('Withdrawal Request Rejected', "Your withdrawal request for {$record->amount} ERC has been rejected.", 'danger');
                                $record->save();
                            });

                            Notification::make()
                                ->body("{$records->count()} withdrawal requests have been rejected.")
                                ->success()
                                ->send();
                        }),
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
            'index' => Pages\ListCashoutRequests::route('/'),
        ];
    }
}
