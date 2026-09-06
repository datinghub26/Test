<?php

namespace App\Livewire\User\Pages;

use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class ProfilePage extends Component
{
    use WithPagination;

    public $private;
    public $tab = 1;

    public $username;
    public $email;

    public function mount(): void
    {
        if (!auth()->check()) {
            Toaster::info('You must be logged in to view this page');
            redirect()->route('home');
        }
    }

    public function render()
    {
        $this->private = auth()->user()->privacy;
        $this->username = auth()->user()->username;
        $this->email = auth()->user()->email;
        $leadsCount = auth()->user()->leads()->count();
        $leadsPoints = auth()->user()->leads()->where('status', 'approved')->sum('points');
        $lastMonthLeadsPoints = auth()->user()->leads()->where('status', 'approved')->where('created_at', '>=', now()->subMonth())->sum('points');
        $referralsCount = auth()->user()->referrals()->count();
        $leads = auth()->user()->leads()->latest()->paginate(10);
        $withdrawals = auth()->user()->cashouts()->where('status', 'approved')->latest()->paginate(10);
        $pendingWithdrawals = auth()->user()->cashouts()->where('status', 'pending')->latest()->paginate(10);
        return view('livewire.user.pages.profile-page', compact(
            'leadsCount',
            'leadsPoints',
            'lastMonthLeadsPoints',
            'referralsCount',
            'leads',
            'withdrawals',
            'pendingWithdrawals'
        ))->layout('layouts.app');
    }

    public function togglePrivacy(): void
    {
        if ($this->private) {
            auth()->user()->update(['privacy' => true]);
            Toaster::success('Your profile is now private.');
        } else {
            auth()->user()->update(['privacy' => false]);
            Toaster::error('Your profile is now public.');
        }
    }

    public function updateProfile()
    {
        if (auth()->user()->username === $this->username && auth()->user()->email === $this->email) {
            Toaster::info('Nothing to update');
            return;
        }

        $this->validate();
        auth()->user()->update([
            'username' => $this->username,
            'email' => $this->email,
        ]);

        Toaster::success('Profile updated successfully');
    }

    public function sendEmailVerificationLink(): void
    {
        if (auth()->user()->hasVerifiedEmail()) {
            Toaster::info('Your email is already verified');
            return;
        }

        $executed = RateLimiter::attempt('send-email-verification-link:' . auth()->id(), $perMinute = 1, function () {
            auth()->user()->sendEmailVerificationNotification();
        });

        if (!$executed) {
            Toaster::error('You can only send verification email once per minute');
            return;
        }

        Toaster::success('Verification email has been sent');
    }

    public function rules(): array
    {
        $isCustomDomain = setting('protection.email_custom_domain');
        $specificDomain = setting('protection.email_custom_domain_value');
        $customDomainRule = $isCustomDomain && count($specificDomain) > 0 ? '|ends_with:' . implode(',', $specificDomain) : '';

        return [
            'username' => 'required|string|min:3|max:20|unique:users,username,' . auth()->id(),
            'email' => 'required|email|unique:users,email,' . auth()->id() . $customDomainRule,
        ];

    }
}
