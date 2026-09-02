<?php

namespace App\Livewire\User\Pages\Support;

use App\Models\Ticket;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toastable;

#[Layout('layouts.app')]
class View extends Component
{
    public $ticket;
    public $message;

    use Toastable;

    public function mount($ticket)
    {
        if (!auth()->check()) {
            $this->info('Please login to access this page');
            return redirect()->route('home');
        }

        $this->ticket = auth()->user()->tickets()
            ->with([
                'messages' => fn($query) => $query->orderBy('created_at', 'asc')->latest()->limit(10),
                'messages.user'
            ])
            ->findOrFail($ticket);
    }

    public function render()
    {
        return view('livewire.user.pages.support.view');
    }

    public function addReply(): void
    {
        $this->validate([
            'message' => 'required|min:5'
        ]);

        $this->ticket->messages()->create([
            'message' => $this->message,
            'user_id' => auth()->id()
        ]);

        if ($this->ticket->isClosed()) {
            $this->ticket->reopen();
        }

        $this->ticket->load('messages');
        $this->message = '';

        $this->success('Reply added successfully');
    }

    public function closeTicket(): void
    {
        $this->ticket->close();
        $this->success('Ticket closed successfully');
    }
}
