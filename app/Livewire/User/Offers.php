<?php

namespace App\Livewire\User;

use App\Models\Offer;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithPagination;

class Offers extends Component
{
    use WithPagination;

    public bool $ready = true;
    public bool $swiper = false;
    public ?int $limit = null;

    #[Reactive]
    public int $sort = 1;
    #[Reactive]
    public string $search = '';
    #[Reactive]
    public string $provider = '';
    #[Reactive]
    public array $devices = [];

    public string $category = '';

    public $selectedOffer;

    public function placeholder()
    {
        return <<<'HTML'
        <div class="mt-4 w-100">
            <div class="d-flex gap-2 overflow-hidden">
                <div class="card bg-dark bg-opacity-75 p-2 skeleton-card" style="width: 140px; min-width: 140px; height: 180px;">
                    <div class="skeleton skeleton-img mb-2" style="height: 90px; width: 100%;"></div>
                    <div class="skeleton skeleton-text mb-1" style="height: 14px;"></div>
                    <div class="skeleton skeleton-text" style="height: 12px; width: 60%;"></div>
                </div>
                <div class="card bg-dark bg-opacity-75 p-2 skeleton-card" style="width: 140px; min-width: 140px; height: 180px;">
                    <div class="skeleton skeleton-img mb-2" style="height: 90px; width: 100%;"></div>
                    <div class="skeleton skeleton-text mb-1" style="height: 14px;"></div>
                    <div class="skeleton skeleton-text" style="height: 12px; width: 60%;"></div>
                </div>
                <div class="card bg-dark bg-opacity-75 p-2 skeleton-card" style="width: 140px; min-width: 140px; height: 180px;">
                    <div class="skeleton skeleton-img mb-2" style="height: 90px; width: 100%;"></div>
                    <div class="skeleton skeleton-text mb-1" style="height: 14px;"></div>
                    <div class="skeleton skeleton-text" style="height: 12px; width: 60%;"></div>
                </div>
            </div>
        </div>
        HTML;
    }

    public function render()
    {
        return view('livewire.user.offers', [
            'offers' => $this->offers(),
        ]);
    }

    private function offers()
    {
        $country = strtoupper(country_code() ?? 'UNKNOWN');
        return Offer::query()
            ->active()
            ->whereDoesntHave('leads', function ($query) {
                $query->where('ip', '=', ip());
            })
            ->withCount('leads')
            ->where(function ($query) use ($country) {
                return $query->whereJsonContains('countries', $country)
                    ->orWhereJsonLength('countries', 0)
                    ->orWhereNull('countries');
            })->when($this->search, function ($query) {
                return $query->where('title', 'like', '%' . $this->search . '%');
            })->when($this->sort == 3, function ($query) {
                return $query->where('is_featured', true)
                    ->orderByDesc('leads_count')
                    ->orderByDesc('points');
            })->when($this->sort == 4, function ($query) {
                return $query->orderByDesc('is_manual')
                    ->orderByDesc('is_featured')
                    ->orderByDesc('leads_count')
                    ->orderByDesc('points');
            })->when($this->sort == 1, function ($query) {
                return $query->orderByDesc('is_featured')
                    ->orderByDesc('is_manual')
                    ->orderBy('points', 'desc');
            })->when($this->sort == 2, function ($query) {
                return $query->orderBy('points', 'asc');
            })->when($this->provider, function ($query) {
                return $query->where('provider', $this->provider);
            })->when($this->category, function ($query) {
                return $query->whereJsonContains('categories', $this->category);
            })->when(count($this->devices), function ($query) {
                return $query->where(function ($query) {
                    foreach ($this->devices as $device) {
                        $query->orWhereJsonContains('devices', $device);
                    }
                });
            })->paginate($this->limit ?? 63);
    }
}
