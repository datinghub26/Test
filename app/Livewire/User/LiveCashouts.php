<?php

namespace App\Livewire\User;

use App\Models\CashoutRequest;
use App\Models\Lead;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy]
class LiveCashouts extends Component
{
    protected $listeners = ['echo:leads,LeadsUpdated' => '$refresh'];

    public function render()
    {
        $this->dispatch('render-live-cashouts');
        return view('livewire.user.live-cashouts', [
            'cashouts' => $this->withdrawalsAndLeads()
        ]);
    }

    protected function withdrawalsAndLeads()
    {
        try {
            $leads = Lead::where('type', 'offer')->selectRaw("id, name, points as amount, created_at, updated_at, user_id")
                ->with('user')
                ->latest('created_at')
                ->limit(10)
                ->get();

            $withdrawals = CashoutRequest::where('status', 'approved')
                ->join('cashout_methods', 'cashout_requests.method_name', '=', 'cashout_methods.name')
                ->selectRaw("cashout_requests.id, cashout_requests.method_name as name, cashout_requests.amount, cashout_requests.method_image, cashout_requests.created_at, cashout_requests.updated_at, cashout_requests.user_id, cashout_methods.bg_color as bg_color")
                ->with('user')
                ->latest('cashout_requests.created_at')
                ->limit(10)
                ->get();

            $mergedResults = $leads->merge($withdrawals)->sortByDesc('updated_at');
            return $mergedResults->values();
        } catch (\Throwable $e) {
            return collect();
        }
    }
}
