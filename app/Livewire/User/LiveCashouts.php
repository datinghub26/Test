<?php

namespace App\Livewire\User;

use App\Models\CashoutRequest;
use App\Models\Lead;
use Livewire\Attributes\On;
use Livewire\Component;

class LiveCashouts extends Component
{
    public $lastCheckedLeadId = 0;

    protected $listeners = [
        'echo:leads,LeadsUpdated' => 'handleNewLeads'
    ];

    public function mount(): void
    {
        $latest = Lead::where('type', 'offer')->where('status', 'approved')->latest('id')->first();
        $this->lastCheckedLeadId = $latest?->id ?? 0;
    }

    public function render()
    {
        return view('livewire.user.live-cashouts', [
            'activities' => $this->withdrawalsAndLeads()
        ]);
    }

    public function handleNewLeads(): void
    {
        $newLeads = Lead::where('type', 'offer')
            ->where('status', 'approved')
            ->where('id', '>', $this->lastCheckedLeadId)
            ->with(['user', 'offer'])
            ->latest('id')
            ->limit(5)
            ->get();

        foreach ($newLeads->reverse() as $lead) {
            $formatted = $this->formatLeadItem($lead);
            $this->dispatch('live-activity-prepend', newActivity: $formatted);
            $this->lastCheckedLeadId = max($this->lastCheckedLeadId, (int)$lead->id);
        }
    }

    public function checkNewActivity(): void
    {
        $this->handleNewLeads();
    }

    public function withdrawalsAndLeads(): array
    {
        try {
            $leads = Lead::where('type', 'offer')
                ->where('status', 'approved')
                ->with(['user', 'offer'])
                ->latest('created_at')
                ->limit(25)
                ->get();

            $withdrawals = CashoutRequest::where('status', 'approved')
                ->leftJoin('cashout_methods', 'cashout_requests.method_name', '=', 'cashout_methods.name')
                ->selectRaw("cashout_requests.id, cashout_requests.method_name as name, cashout_requests.amount, cashout_requests.method_image, cashout_requests.created_at, cashout_requests.updated_at, cashout_requests.user_id, cashout_methods.bg_color as bg_color")
                ->with('user')
                ->latest('cashout_requests.created_at')
                ->limit(25)
                ->get();

            $formattedLeads = $leads->map(fn($item) => $this->formatLeadItem($item));
            $formattedCashouts = $withdrawals->map(fn($item) => $this->formatCashoutItem($item));

            $merged = $formattedLeads->merge($formattedCashouts)->sortByDesc('timestamp');
            return $merged->values()->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    protected function formatLeadItem(Lead $item): array
    {
        $wall = $item->offer?->provider ?: ($item->provider ?: null);
        $offerTitle = $item->offer_name ?: ($item->offer?->name ?: null);

        if (!$wall && str_contains($item->name, ' - ')) {
            $parts = explode(' - ', $item->name, 2);
            $wall = $parts[0];
            $offerTitle = $offerTitle ?: $parts[1];
        }

        $wall = $wall ?: 'Offerwall';
        $offerTitle = $offerTitle ?: $item->name;

        return [
            'id' => (int)$item->id,
            'type' => 'lead',
            'user_id' => (int)$item->user_id,
            'username' => $item->user?->username ?? 'Member',
            'avatar' => $item->user ? $item->user->avatar() : asset('assets/img/icon-light.png'),
            'wall' => $wall,
            'offer' => $offerTitle,
            'amount' => (float)$item->points,
            'bg_color' => null,
            'image' => $item->image ?: asset('assets/img/icon-light.png'),
            'time' => $item->created_at ? $item->created_at->diffForHumans(null, true, true) : 'Just now',
            'timestamp' => $item->created_at?->timestamp ?? time(),
        ];
    }

    protected function formatCashoutItem($item): array
    {
        return [
            'id' => (int)$item->id,
            'type' => 'cashout',
            'user_id' => (int)$item->user_id,
            'username' => $item->user?->username ?? 'Member',
            'avatar' => $item->user ? $item->user->avatar() : asset('assets/img/icon-light.png'),
            'wall' => 'Cash Out',
            'offer' => $item->name,
            'amount' => (float)$item->amount,
            'bg_color' => $item->bg_color,
            'image' => $item->method_image ? \Storage::url($item->method_image) : asset('assets/img/icon-light.png'),
            'time' => $item->created_at ? $item->created_at->diffForHumans(null, true, true) : 'Just now',
            'timestamp' => $item->created_at?->timestamp ?? time(),
        ];
    }
}
