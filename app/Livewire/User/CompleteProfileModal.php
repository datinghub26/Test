<?php

namespace App\Livewire\User;

use Livewire\Component;

class CompleteProfileModal extends Component
{
    public string $username = '';
    public int $avatar = 1;

    public function render()
    {
        return view('livewire.user.complete-profile-modal');
    }

    public function save(): void
    {
        if (!auth()->check()) {
            $this->redirect(route('login'));
        }

        $this->resetErrorBag();
        $this->validate([
            'username' => 'required|min:6|max:20',
            'avatar' => 'required',
        ]);

        auth()->user()->update([
            'username' => $this->username,
            'avatar' => $this->avatar,
        ]);

        $this->redirect(route('home'));
    }

    public function avatars(): array
    {
        return collect(range(1, 36))->map(function ($avatar_num) {
            return asset("assets/avatars/memoji_$avatar_num.png");
        })->toArray();
    }

    public function names(): array
    {
        return [
            'TwistedAnvil',
            'CrimsonDagger',
            'SneakyShadow',
            'SilentWraith',
            'MysticWitch',
            'DemonicVampire',
            'DarkLord',
            'ShadowKing',
            'SneakySniper',
            'ChaoticRaven',
            'MalevolentDagger',
            'VenomousFang',
            'SavageWarlock',
            'WickedBlade',
            'VengefulVulture',
            'ViciousViper',
            'SinisterSorcerer',
            'CunningCobra',
            'DeadlyDemon',
            'VileVillain',
            'EvilEnchantress',
            'WickedWitch',
            'MysticMage',
            'DarkDiva',
            'ShadowShaman',
        ];
    }
}
