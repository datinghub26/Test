<?php

namespace App\Livewire\User\Pages;

use App\Models\Offer;
use Livewire\Attributes\Url;
use Livewire\Component;

class OffersPage extends Component
{
    #[Url]
    public $search = '';
    #[Url]
    public $sort = 3;
    #[Url]
    public $provider = '';
    #[Url]
    public $devices = [];
    #[Url]
    public $category = '';

    public function render()
    {
        $providers = Offer::pluck('provider')->unique();
        return view('livewire.user.pages.offers-page', compact('providers'))->layout('layouts.app');
    }

    public function pushDevice($device): void
    {
        if (in_array($device, $this->devices)) {
            $this->devices = array_diff($this->devices, [$device]);
        } else {
            $this->devices[] = $device;
        }
    }


}
