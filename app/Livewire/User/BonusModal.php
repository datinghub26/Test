<?php

namespace App\Livewire\User;

use App\Models\Bonus;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class BonusModal extends Component
{
    public $code;

    public function render()
    {
        return view('livewire.user.bonus-modal');
    }

    public function redeem(): void
    {
        $this->validate([
            'code' => 'required|string',
        ]);

        $code = Bonus::where('code', $this->code)->first();
        if (!$code) {
            $this->addError('code', 'Invalid code.');
            return;
        }

        if ($code->expires_at < now()) {
            $this->addError('code', 'Code has expired.');
            return;
        }

        if ($code->usage()?->count() >= $code->max_uses) {
            $this->addError('code', 'Code has reached its limit.');
            return;
        }

        if ($code->usage()->where('user_id', auth()->id())->exists()) {
            $this->addError('code', 'You have already redeemed this code.');
            return;
        }

        $code->usage()->create([
            'user_id' => auth()->id(),
        ]);

        auth()->user()->increment('points', $code->reward_points);
        $this->dispatch('update-balance', balance: auth()->user()->points);
        auth()->user()->addNotification('Bonus Code', "You have redeemed {$code->reward_points} point", 'info', route('shop'));
        auth()->user()->leads()->create([
            'name' => "Bonus Code",
            'points' => $code->reward_points,
            'payout' => 0,
            'ip' => ip(),
            'country_code' => country_code(),
        ]);

        Toaster::success('You have successfully redeemed the code.');
        $this->code = '';
    }
}
