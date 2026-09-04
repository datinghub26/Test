<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\LeadResource\RelationManagers;
use App\Models\Lead;
use App\Tables\Columns\UserColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;
    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';
    protected static ?int $navigationSort = 100;


    public static function getNavigationBadge(): ?string
    {
        $count = Lead::whereDate('created_at', today())->where('type', 'offer')->count();
        return $count > 0 ? $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                UserColumn::make('user')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->formatStateUsing(fn($record) => view('filament.offer-img-name', ['image' => $record->image, 'name' => $record->name]))
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn($record) => $record->type === 'offer' ? 'success' : 'info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($record) => $record->status === 'approved' ? 'success' : ($record->status === 'rejected' ? 'danger' : 'warning'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('points')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payout')
                    ->money('USD')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country_code')
                    ->formatStateUsing(fn(string $state): View => view(
                        'filament.flag', ['state' => $state],
                    ))
                    ->label('Country'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime("Y-m-d h:i A")
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->default('offer')
                    ->options([
                        'offer' => 'Offer',
                        'activity' => 'Activity',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'approved' => 'Approved',
                        'pending' => 'Pending',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('provider')
                    ->options(function () {
                        // Get distinct non-null provider values
                        return Lead::whereNotNull('provider')
                            ->distinct()
                            ->orderBy('provider')
                            ->pluck('provider', 'provider')
                            ->toArray();
                    })
                    ->searchable() // Make the dropdown searchable for better UX with many providers
                    ->preload() // Load options when the page loads rather than when clicked
                    ->placeholder('All Providers')
                    ->label('Provider') // Explicit label
                    ->indicateUsing(function (array $state): ?string {
                        // Show selected filter in the indicator
                        return $state['value'] ?? null;
                    }),
                DateRangeFilter::make('created_at')
                    ->timezone('Indian/Chagos')
            ])
            ->actions([
                Tables\Actions\Action::make('Track')
                    ->icon('heroicon-o-cursor-arrow-rays')
                    ->action(function ($record) {
                        return redirect()->to(UserResource::getUrl('view', ['record' => $record->user->id]));
                    }),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('Approve')
                        ->hidden(fn($record) => $record->status !== 'pending')
                        ->action(function ($record) {
                            $record->update(['status' => 'approved']);
                            $record->user->updateUserPointsAndLevel($record->points);
                            $record->user->addNotification(
                                'Offer Completed',
                                "Your offer '{$record->name}' was approved and {$record->points} ERC credited!",
                                'offer_completed'
                            );
                        })
                        ->requiresConfirmation()
                        ->color('success')
                        ->icon('heroicon-o-check-circle'),
                    Tables\Actions\Action::make('Reject')
                        ->hidden(fn($record) => $record->status !== 'pending')
                        ->form(self::rejectSchema())
                        ->modalWidth(MaxWidth::FitContent)
                        ->action(function ($record, $data) {
                            self::rejectAction($record, $data);
                            Notification::make()
                                ->body('The lead has been rejected successfully.')
                                ->success()
                                ->send();
                        })
                        ->color('danger')
                        ->icon('heroicon-o-x-circle'),
                ]),
            ])
            ->bulkActions([
                ExportBulkAction::make()->exports([
                    ExcelExport::make()->withColumns([
                        Column::make('user')
                            ->formatStateUsing(fn($record) => "{$record->user->email}"),
                        Column::make('name')
                            ->formatStateUsing(fn($record) => $record->name),
                        Column::make('type'),
                        Column::make('status'),
                        Column::make('points'),
                        Column::make('payout'),
                        Column::make('ip'),
                        Column::make('country_code')
                            ->heading('Country')
                            ->formatStateUsing(fn($record) => $record->country_code),
                        Column::make('created_at'),
                    ]),
                ]),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('Approve')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'approved']);
                                $record->user->updateUserPointsAndLevel($record->points);
                            });
                            Notification::make()
                                ->body("{$records->count()} leads have been approved successfully.")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->icon('heroicon-s-check-circle')
                        ->color('success'),
                    Tables\Actions\BulkAction::make('Reject')
                        ->form(self::rejectSchema())
                        ->modalWidth(MaxWidth::FitContent)
                        ->action(function (array $data, Collection $records) {
                            $records->each(function ($record) use ($data) {
                                self::rejectAction($record, $data);
                            });
                            Notification::make()
                                ->body("{$records->count()} leads have been rejected successfully.")
                                ->success()
                                ->send();
                        })
                        ->icon('heroicon-s-x-circle')
                        ->color('danger'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLeads::route('/'),
        ];
    }

    public static function rejectSchema(): array
    {
        return [
            RadioDeck::make('type')
                ->options([
                    'Chargeback' => 'Chargeback',
                    'Proxy Usage' => 'Proxy Usage',
                    'Other' => 'Other'
                ])
                ->default('Chargeback')
                ->color('danger')
                ->icons([
                    'Chargeback' => 'heroicon-o-sparkles',
                    'Proxy Usage' => 'heroicon-o-cursor-arrow-rays',
                    'Other' => 'heroicon-o-cursor-arrow-rays',
                ])
                ->live()
                ->columns(3)
                ->columnSpanFull()
                ->required(),
            TextInput::make('reason')
                ->hidden(fn(Get $get): bool => $get('type') !== 'Other'),
            Toggle::make('reduce')
                ->label('Reduce Points')
                ->default(true)
                ->columnSpanFull(),
            Toggle::make('ban')
                ->label('Ban User')
                ->default(false)
                ->columnSpanFull(),
        ];
    }

    public static function rejectAction($record, $data): void
    {
        $record->update([
            'status' => 'rejected',
            'reason' => $data['reason'] ?? $data['type'],
        ]);

        if ($data['reduce'] === true)
            $record->user->updateUserPointsAndLevel($record->points * -1, $record->type == 'offer');

        if ($data['ban'] === true)
            $record->user->update(['is_banned' => true]);

        $record->user->addNotification(
            'Offer Chargeback',
            "Offer '{$record->name}' was marked as {$data['type']}. {$record->points} ERC deducted.",
            'chargeback'
        );
    }
}
