<?php

namespace App\Http\Controllers;

use App\Helpers\CampaignHelper;
use App\Models\User;
use App\Models\UsersGeoData;
use App\Models\UsersReferral;
use App\Models\UsersReferralData;
use Auth;
use Hash;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('guest', only: ['resetPassword', 'referral']),
            new Middleware('auth', only: ['verifyEmail', 'logout']),
            new Middleware('signed', only: ['verifyEmail']),
        ];
    }

    public function resetPassword($token, $email)
    {
        return redirect()->route('home', ['token' => $token, 'email' => $email]);
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();
        \Toaster::success("Your email has been verified.");
        return redirect('/');
    }

    public function referral($referral)
    {
        session()->put('referral', $referral);
        return redirect('/');
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $authUser = User::where('email', $googleUser->email)->first();
            if ($authUser) {
                if ($authUser->is_banned) {
                    \Toaster::error("Your account has been banned.");
                    return redirect('/');
                }

                auth()->login($authUser);
                return redirect()->route('home');
            }

            if (static::hasExceededMaxAccountsPerIp()) {
                \Toaster::error("You are not allowed to register anymore.");
                return redirect()->route('home');
            }

            $user = static::register($googleUser->email, \Str::random(), true);
            static::checkReferred($user);
            CampaignHelper::handleRegistration($user);
            Auth::login($user);
        } catch (\Exception $e) {
            report($e);
            \Toaster::error("An error occurred while logging in with Google.");
        }

        return redirect()->route('home');
    }

    public static function register($email, $password, $verified = false): User
    {
        $user = User::create([
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => $verified ? now() : null,
        ]);

        $user->referralData()->create([
            'referral_code' => static::generateReferralCode(),
        ]);

        $user->geo()->create([
            'ip' => ip(),
            'country_code' => country_code(),
            'user_agent' => request()->userAgent(),
        ]);

        return $user;
    }

    public static function generateReferralCode(): string
    {
        return strtoupper(substr(md5(uniqid()), 0, 6));
    }

    public static function hasExceededMaxAccountsPerIp(): bool
    {
        if (setting('protection.enable_max_accounts_per_ip', true)) {
            $max = setting('protection.max_accounts_per_ip', 2);
            $count = UsersGeoData::where(function ($query) {
                $query->where('ip', ip())->orWhere('latest_login_ip', ip());
            })
                ->whereBetween('created_at', [now()->subDays(7), now()])
                ->count();

            return $count >= $max;
        }

        return false;
    }

    public static function checkBeforeRegister(\Closure $closure = null): bool
    {
        if (setting('protection.enable_max_accounts_per_ip', true)) {
            $max = setting('protection.max_accounts_per_ip', 2);
            $count = UsersGeoData::where(function ($query) {
                $query->where('ip', ip())->orWhere('latest_login_ip', ip());
            })
                ->whereBetween('created_at', [now()->subDays(7), now()])
                ->count();

            if ($count >= $max) {
                if ($closure)
                    $closure();
                return true;
            }
        }

        return false;
    }

    public static function checkReferred(User $u): void
    {
        $user = User::find($u->id);
        if (session()->has('referral')) {
            $referral = UsersReferralData::where('referral_code', session()->get('referral'))->first();
            if ($referral) {
                if (setting('referral.enable_rewards', true)) {
                    $points = setting('referral.points', 100);

                    $user->updateUserPointsAndLevel($points);
                    $user->leads()->create([
                        'name' => 'Referral Bonus',
                        'points' => $points,
                        'payout' => 0,
                        'ip' => ip(),
                        'country_code' => country_code(),
                    ]);

                    $user->addNotification('Referral Bonus', "You have received $points point for using a referral code.");
                }

                UsersReferral::create([
                    'user_id' => $user->id,
                    'referred_by_id' => $referral->user_id,
                ]);
            }
        }
    }
}
