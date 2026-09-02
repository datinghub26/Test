<?php

namespace App\Livewire\User\Pages;

use App\Models\Provider;
use Livewire\Component;

class SurveysPage extends Component
{
    public function render()
    {
        $surveys = Provider::where('type', 'survey')->where('is_active', 1)->orderBy('order')->get();
        return view('livewire.user.pages.surveys-page', compact('surveys'))->layout('layouts.app');
    }
}
