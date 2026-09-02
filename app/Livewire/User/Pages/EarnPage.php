<?php

namespace App\Livewire\User\Pages;

use App\Models\Provider;
use Livewire\Component;

class EarnPage extends Component
{
    public function render()
    {
        $offerPartners = Provider::offer()->active()->ordered()->get();
        $surveyPartners = Provider::survey()->active()->ordered()->get();
        $userLevel = auth()->check() ? auth()->user()->level : 0;
        return view('livewire.user.pages.earn-page', compact('offerPartners', 'surveyPartners', 'userLevel'))
            ->layout('layouts.app');
    }
}
