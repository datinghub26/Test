<?php

namespace App\Livewire\User;

use App\Models\Lead;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityModal extends Component
{
    use WithPagination;

    public bool $show = false;
    public $user;
    public $highlightLeadId = null;

    public function render()
    {
        $leads = $this->user && !$this->user->privacy
            ? $this->user->leads()->with('offer')->latest('created_at')->paginate(5)
            : collect();

        $highlightLead = $this->highlightLeadId ? Lead::with('offer')->find($this->highlightLeadId) : null;

        return view('livewire.user.activity-modal', compact('leads', 'highlightLead'));
    }

    #[On('activity-open')]
    public function openModel($user_id, $lead_id = null): void
    {
        if (is_array($user_id)) {
            $lead_id = $user_id['lead_id'] ?? null;
            $user_id = $user_id['user_id'] ?? null;
        }

        $this->show = true;
        $this->user = $user_id ? User::find($user_id) : null;
        $this->highlightLeadId = $lead_id;
    }

    public function closeModal(): void
    {
        $this->show = false;
        $this->highlightLeadId = null;
        $this->resetPage();
        $this->dispatch('activity-modal-closed');
    }
}
