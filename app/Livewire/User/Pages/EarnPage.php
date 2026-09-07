<?php

namespace App\Livewire\User\Pages;

use App\Models\Provider;
use Livewire\Component;

class EarnPage extends Component
{
    public function render()
    {
        $userLevel = auth()->check() ? auth()->user()->level : 0;
        $offerPartners = Provider::offer()->active()->ordered()->get()->reject(function ($provider) use ($userLevel) {
            return stripos($provider->name, 'cpagrip') !== false && $userLevel < 9;
        });
        $surveyPartners = Provider::survey()->active()->ordered()->get();
        return view('livewire.user.pages.earn-page', compact('offerPartners', 'surveyPartners', 'userLevel'))
            ->layout('layouts.app');
    }
}
