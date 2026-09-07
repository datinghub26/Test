<?php

namespace App\Livewire\User\Pages;

use App\Models\Provider;
use Livewire\Component;

class PartnersPage extends Component
{
    public function render()
    {
        $userLevel = auth()->check() ? auth()->user()->level : 0;
        $offers = Provider::where('type', 'offer')->where('is_active', 1)->orderBy('order')->get()->reject(function ($provider) use ($userLevel) {
            return stripos($provider->name, 'cpagrip') !== false && $userLevel < 9;
        });
        return view('livewire.user.pages.partners-page', compact('offers'))->layout('layouts.app');
    }
}
