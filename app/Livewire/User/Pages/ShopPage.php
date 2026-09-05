<?php

namespace App\Livewire\User\Pages;

use App\Models\CashoutMethod;
use App\Models\CashoutPrice;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class ShopPage extends Component
{
    public $selectedMethod;
    public $selectedPrice;
    public string $address;
    public int $amount;
    public bool $show = false;
    public bool $isCoin = true;

    public function render()
    {
        $methodsGrouped = CashoutMethod::where('is_active', true)
            ->with('prices')
            ->orderBy('order')
            ->get()
            ->groupBy('category');
        return view('livewire.user.pages.shop-page', compact('methodsGrouped'))->layout('layouts.app');
    }

    public function cashout($amount, $method_id, $isCoin = true): void
    {
        if (!auth()->check()) {
            Toaster::info('You must be logged in to cashout');
            return;
        }

        if (setting('protection.email_verify_to_withdraw', false) && !auth()->user()->hasVerifiedEmail()) {
            Toaster::warning('You must verify your email to cashout');
            return;
        }

        $this->amount = $amount;
        $this->selectedMethod = CashoutMethod::find($method_id);
        $this->isCoin = $isCoin;

        $this->validate();
        $amount = $this->isCoin ? $this->amount : $this->amount * 1000;

        if ($amount < $this->selectedMethod->minimum) {
            $requiredAmount = $isCoin ? number_format($this->selectedMethod->minimum) . ' ERC' : '$' . to_money_str($this->selectedMethod->minimum, 2);
            $this->addError('amount', "Minimum amount is $requiredAmount");
            return;
        }

        $realAmount = $amount + percentage_value($amount, $this->selectedMethod->fee);
        if ($realAmount > auth()->user()->points) {
            Toaster::info("You don't have enough ERC to cashout");
            return;
        }

        $cashout = auth()->user()->cashouts()->create([
            'method_name' => $this->selectedMethod->name,
            'method_image' => $this->selectedMethod->image ?? 'assets/img/icon-light.png',
            'address' => $this->address,
            'amount' => $amount,
        ]);

        auth()->user()->decrement('points', $realAmount);
        auth()->user()->addNotification('Withdraw Request', "You have requested a cashout of {$amount} ERC", 'cashout_submitted', route('shop'));
        $this->dispatch('update-balance', balance: auth()->user()->points);
        $this->dispatch('play-notification-sound', id: 'shop_' . $cashout->id);

        Toaster::success('Cashout request has been submitted successfully');
        $this->dispatch('close-cashout-modal');
    }

    public function rules(): array
    {
        return [
            'selectedMethod' => 'required',
            'address' => 'required',
            'amount' => 'required|numeric|min:1'
        ];
    }

}
