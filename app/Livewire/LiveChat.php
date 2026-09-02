<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\User;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy]
class LiveChat extends Component
{
    public $isAbleToChat = false;
    public $isOpen = false;
    public $message = '';

    protected $listeners = ['echo:chat,MessageCreated,MessageDeleted' => '$refresh'];

    public function render()
    {
        $this->isAbleToChat = auth()->check();
        auth()?->user()?->update(['last_activity_at' => now()]);
        return view('livewire.live-chat', [
            'messages' => Message::with('user', 'user.levelRank')
                ->latest()
                ->take(50)
                ->get()
                ->reverse()
        ]);
    }

    public function toggleChat(): void
    {
        $this->isOpen = !$this->isOpen;
        $this->scrollChatToBottom();
    }

    public function sendMessage(): void
    {
        if (!$this->isAbleToChat)
            return;

        $this->validate([
            'message' => 'required'
        ]);

        auth()->user()->messages()->create([
            'message' => $this->message
        ]);


        $this->message = '';
//        $this->scrollChatToBottom();
    }


//    #[On('echo:online-users-count,MessageCreated')]
    public function totalOnlineUsers(): int
    {
        return User::where('last_activity_at', '>=', now()->subMinutes(5))->count();
    }

    public function fetchOnlineUsers()
    {

    }

    private function scrollChatToBottom(): void
    {
        $this->dispatch('scroll-chat-to-bottom', ['elementId' => 'chat-messages']);
    }
}
