<?php

namespace App\Livewire\User\Pages;

use Livewire\Component;
use Masmerise\Toaster\Toaster;

class ReferralsPage extends Component
{
    public function mount(): void
    {
        if (!auth()->check()) {
            Toaster::info('You must be logged in to view this page');
            redirect()->route('home');
        }
    }

    public function render()
    {
        return view('livewire.user.pages.referrals-page')->layout('layouts.app');
    }
}
