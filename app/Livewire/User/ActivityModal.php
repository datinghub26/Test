<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityModal extends Component
{
    use WithPagination;

    public bool $show = false;
    public $user;

    public function render()
    {
        $leads = $this->user && !$this->user->privacy
            ? $this->user->leads()->paginate(5)
            : collect();
        return view('livewire.user.activity-modal', compact('leads'));
    }

    #[On('activity-open')]
    public function openModel($user_id): void
    {
        $this->show = true;
        $this->user = User::find($user_id);
    }

    public function closeModal(): void
    {
        $this->show = false;
        $this->resetPage();
    }
}
