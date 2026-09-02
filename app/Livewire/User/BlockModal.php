<?php

namespace App\Livewire\User;

use Livewire\Component;

class BlockModal extends Component
{
    public string $message = 'Detected suspicious activity.';
    public bool $show = false;

    public function render()
    {
        if (auth()->check() && auth()->user()->is_banned)
            $this->openModal('Your account has been blocked.');
        else if ($this->checkIPChangeProtection())
            $this->openModal("Detected suspicious activity.");

        return view('livewire.user.block-modal');
    }

    protected function checkIPChangeProtection(): bool
    {
        if (!auth()->check())
            return false;

        if (!setting('protection.ip_change_enable', true))
            return false;

        if (ip() == auth()->user()->geo->ip)
            return false;

        if (setting('protection.ip_change_block_acc', false)) {
            auth()->user()->update(['is_banned' => true]);
        }

        return true;
    }

    protected function openModal($message): void
    {
        $this->message = $message;
        $this->show = true;
    }

    protected function closeModal(): void
    {
        $this->show = false;
    }
}
