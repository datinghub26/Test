<?php

namespace App\Livewire\User\Pages;

use App\Models\Provider;
use Livewire\Component;

class PartnersPage extends Component
{
    public function render()
    {
        $offers = Provider::where('type', 'offer')->where('is_active', 1)->orderBy('order')->get();
        return view('livewire.user.pages.partners-page', compact('offers'))->layout('layouts.app');
    }
}
