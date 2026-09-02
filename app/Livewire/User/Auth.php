<?php

namespace App\Livewire\User;

use App\Helpers\CampaignHelper;
use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth as AuthBase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Auth extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public bool $acceptTerms = false;
    public int $activeTab = 1;
    public string $token;

    public function render()
    {
        // $this->dispatchBrowserEvent('recaptcha:refresh');
        return view('livewire.user.auth');
    }
    
    
//     public function switchTab(int $tab): void
// {
//     $this->activeTab = $tab;
//     $this->dispatchBrowserEvent('recaptcha:refresh');
// }



    public function login(): void
    {
        $credentials = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'boolean',
        ]);

        if (User::where('email', $credentials['email'])->where('is_banned', 1)->exists()) {
            $this->addError('email', 'Your account has been banned.');
            return;
        }

        if (AuthBase::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $this->remember)) {
            AuthBase::user()->geo()->update([
                'latest_login_ip' => ip(),
                'latest_login_at' => now(),
                'latest_user_agent' => request()->userAgent(),
            ]);
            $this->redirect('/');
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }

    public function register(): void
    {
        $isCustomDomain = setting('protection.email_custom_domain');
        $specificDomain = setting('protection.email_custom_domain_value');
        $customDomainRule = $isCustomDomain && count($specificDomain) > 0 ? '|ends_with:' . implode(',', $specificDomain) : '';
        $this->validate([
            'email' => 'required|email|unique:users' . $customDomainRule,
            'password' => 'required|min:6',
            'acceptTerms' => 'accepted',
        ]);

        if (AuthController::hasExceededMaxAccountsPerIp()) {
            $this->addError('email', "You are not allowed to register anymore.");
            return;
        }

        $user = AuthController::register($this->email, $this->password);
        AuthController::checkReferred($user);
        CampaignHelper::handleRegistration($user);
        AuthBase::login($user);
        $this->redirect('/');
    }

    #[On('register')]
    public function registerByEvent($email, $password): void
    {
        $isCustomDomain = setting('protection.email_custom_domain');
        $specificDomain = setting('protection.email_custom_domain_value');
        $customDomainRule = $isCustomDomain && count($specificDomain) > 0 ? '|ends_with:' . implode(',', $specificDomain) : '';

        $validator = Validator::make(['email' => $email, 'password' => $password], [
            'email' => 'required|email|unique:users' . $customDomainRule,
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            Toaster::error($validator->errors()->first());
            $this->dispatch('register-finished');
            return;
        }

        if (AuthController::hasExceededMaxAccountsPerIp()) {
            Toaster::error("You are not allowed to register anymore.");
            $this->dispatch('register-finished');
            return;
        }

        $user = AuthController::register($email, $password);
        AuthController::checkReferred($user);
        CampaignHelper::handleRegistration($user);
        AuthBase::login($user);
        $this->redirect('/');
    }

    public function forgetPassword(): void
    {
        $this->validate([
            'email' => 'required|email|exists:users',
        ]);

        try {
            $status = Password::sendResetLink(['email' => $this->email]);
            if ($status !== Password::RESET_LINK_SENT) {
                $this->addError('email', 'An error occurred while sending the reset email.');
                return;
            }
            Toaster::success('Password reset email has been sent.');
        } catch (\Exception $e) {
            $this->addError('email', 'An error occurred while sending the reset email.');
        }
    }

    public function isHasValidToken(): bool
    {
        if (auth()->check())
            return false;

        $token = request()->input('token') ?? '';
        if (!$token)
            return false;

        $email = request()->input('email') ?? '';
        if (!$email)
            return false;

        $validator = Validator::make(['email' => $email, 'token' => $token], [
            'email' => 'required|email|exists:users',
            'token' => 'required|string',
        ]);

        if ($validator->fails())
            return false;

        $user = User::where('email', $email)->first();
        if (!Password::broker()->tokenExists($user, $token))
            return false;

        $this->email = $email;
        $this->token = $token;

        return true;
    }

    public function resetPassword(): void
    {
        if (auth()->check()) {
            $this->addError('email', 'You are not authorized to reset the password.');
            return;
        }

        $this->validate([
            'email' => 'required|email|exists:users',
            'token' => 'required|string',
            'password' => 'required|min:6',
        ]);

        $status = Password::reset([
            'email' => $this->email,
            'token' => $this->token,
            'password' => $this->password,
            'password_confirmation' => $this->password,
        ], function (User $user, string $password) {
            $user->forceFill(['password' => Hash::make($password)])
                ->setRememberToken(Str::random(60));
            $user->save();
            event(new PasswordReset($user));
        });


        if ($status === Password::PASSWORD_RESET) {
            Toaster::success('Password has been reset successfully.');
            $this->redirect('/');
        }

        back()->withErrors(['email' => [__($status)]]);
    }
//
//    public function loginAsAdmin(): void
//    {
//        $this->email = 'admin@ziadt.dev';
//        $this->password = 'password';
//        $this->login();
//    }
}
