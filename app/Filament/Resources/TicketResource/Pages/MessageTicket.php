<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Ticket;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use \Filament\Tables\Columns\Concerns\HasRecord;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MessageTicket extends Page implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $resource = TicketResource::class;

    protected static string $view = 'filament.resources.ticket-resource.pages.message-ticket';

    public Ticket $ticket;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Reply')
                ->form([
                    RichEditor::make('message')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->ticket->messages()->create([
                        'user_id' => auth()->id(),
                        'message' => $data['message'],
                    ]);
                })
                ->modalHeading('Reply to Ticket'),

            Action::make('ticket.close')
                ->label(fn() => $this->ticket->isClosed() ? 'Reopen Ticket' : 'Close Ticket')
                ->requiresConfirmation()
                ->color(fn() => $this->ticket->isClosed() ? 'success' : 'danger')
                ->action(function () {
                    $isClosed = $this->ticket->isClosed();
                    $isClosed ? $this->ticket->reopen() : $this->ticket->close();
                    Notification::make()
                        ->body($isClosed ? 'Ticket closed successfully' : 'Ticket reopened successfully')
                        ->send();
                }),
        ];
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->requiresConfirmation()
            ->modalHeading('Delete message')
            ->modalDescription('Are you sure you\'d like to delete this message? This cannot be undone.')
            ->color('danger')
            ->icon('heroicon-s-trash')
            ->iconButton()
            ->action(function (array $arguments) {
                $message = $this->ticket->messages()->findOrFail($arguments['message']);
                $message?->delete();

                Notification::make()
                    ->body('Message deleted successfully')
                    ->send();
            });
    }

}
