<?php

namespace App\Livewire\User\Pages\Support;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toastable;
use Masmerise\Toaster\Toaster;
use Spatie\LivewireFilepond\WithFilePond;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithFilePond;
    use WithPagination;
    use Toastable;

    public $subject = '';
    public $description = '';
    public $priority = 'low';
    public $file;

    protected $rules = [
        'subject' => 'required|min:5',
        'description' => 'required|min:10',
        'priority' => 'required|in:low,medium,high',
        'file' => 'nullable|mimetypes:image/jpg,image/jpeg,image/png,application/pdf|max:2048'
    ];

    public function mount(): void
    {
        if (!auth()->check()) {
            Toaster::info('You must be logged in to view this page');
            redirect()->route('home');
        }
    }

    public function render()
    {
        $tickets = auth()->user()->tickets()
            ->latest()
            ->paginate(10);

        return view('livewire.user.pages.support.index', compact('tickets'));
    }

    public function createTicket(): void
    {
        $this->validate();

        try {

            $ticket = auth()->user()->tickets()->create([
                'title' => $this->subject,
                'priority' => $this->priority,
            ]);

            $ticket->messages()->create([
                'message' => $this->description,
                'user_id' => auth()->id()
            ]);

            if ($this->file) {
                $ticket
                    ->addMedia($this->file)
                    ->toMediaCollection('attachments');
            }

            $this->reset();
            $this->dispatch('ticket-created');
        } catch (\Exception $e) {
            $this->error('Failed to create ticket. Please try again');
            return;
        }

        $this->success('Ticket created successfully');
    }

    public function closeTicket($id): void
    {
        try {
            $ticket = auth()->user()->tickets()->findOrFail($id);
            $ticket->close();
        } catch (\Exception $e) {
            $this->error('Failed to close ticket. Please try again');
        }

        $this->success('Ticket closed successfully');
    }
}
