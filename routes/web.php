<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\OfferwallController;
use App\Livewire\User\Pages\EarnPage;
use App\Livewire\User\Pages\FaqPage;
use App\Livewire\User\Pages\HomePage;
use App\Livewire\User\Pages\LeaderboardPage;
use App\Livewire\User\Pages\OffersPage;
use App\Livewire\User\Pages\PartnersPage;
use App\Livewire\User\Pages\PrivacyPage;
use App\Livewire\User\Pages\ProfilePage;
use App\Livewire\User\Pages\ReferralsPage;
use App\Livewire\User\Pages\RewardsPage;
use App\Livewire\User\Pages\ShopPage;
use App\Livewire\User\Pages\Support;
use App\Livewire\User\Pages\SurveysPage;
use App\Livewire\User\Pages\TermsPage;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


Route::get('/', HomePage::class)->name('home');
Route::get('/earn', EarnPage::class)->name('earn');
Route::get('/offers', OffersPage::class)->name('offers');
Route::get('/partners', PartnersPage::class)->name('partners');
Route::get('/surveys', SurveysPage::class)->name('surveys');
Route::get('/shop', ShopPage::class)->name('shop');
Route::get('/leaderboard', LeaderboardPage::class)->name('leaderboard');
Route::get('/referrals', ReferralsPage::class)->name('referrals');
Route::get('/rewards', RewardsPage::class)->name('rewards');
Route::get('/profile', ProfilePage::class)->name('profile');
Route::get('/terms-of-service', TermsPage::class)->name('terms');
Route::get('/privacy-policy', PrivacyPage::class)->name('privacy');
Route::get('/faq', FaqPage::class)->name('faq');
Route::get('/support', Support\Index::class)->name('support');
Route::get('/support/{ticket}', Support\View::class)->name('support.show');
Volt::route('/private/live-leads', 'user.hidden-live-leads')->name('live-leads');

/* Authentication routes */
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::get('/reset-password/{token}/{email}', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::get('/ref/{referral}', [AuthController::class, 'referral'])->name('referral');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

/* Socialite routes */
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

/* Custom Offerwall routes */
Route::get('/offerwall/ogads', [OfferwallController::class, 'ogads'])->name('offerwall.ogads');

/* API Postback endpoints */
Route::group(['prefix' => 'api/postback'], function () {
    Route::any('/clickwall', [\App\Http\Controllers\Postback\PostbackController::class, 'clickwall']);
    Route::any('/cpagrip', [\App\Http\Controllers\Postback\PostbackController::class, 'cpagrip']);
});


/* Test routes */
Route::get('/test-mail', function () {
    $s = new \Illuminate\Auth\Notifications\VerifyEmail();
    return $s->toMail(auth()->user())->render();
});

Route::get('campaign-tracking', function () {
    $params = ['campaign', 'source', 'click', 'oid'];
    foreach ($params as $param) {
        Session::remove($param);
        $value = request()->input($param);
        if ($value)
            Session::put($param, $value);
    }

    return redirect()->route('home');
})->name('campaign-tracking');

Route::get('/storage/{path}', function ($path) {
    $candidates = [
        storage_path('app/public/' . $path),
        storage_path('app/' . $path),
        storage_path('app/public/providers/' . basename($path)),
        storage_path('app/public/methods/' . basename($path)),
        storage_path('app/public/sounds/' . basename($path)),
        storage_path('app/providers/' . basename($path)),
        storage_path('app/methods/' . basename($path)),
        storage_path('app/sounds/' . basename($path)),
        public_path('storage/' . $path),
        public_path('storage/' . basename($path)),
        dirname(base_path()) . '/public_html/storage/' . $path,
        dirname(base_path()) . '/public_html/storage/' . basename($path),
        '/home/earneslx/public_html/storage/' . $path,
        '/home/earneslx/public_html/storage/' . basename($path),
    ];
    $filePath = null;
    foreach ($candidates as $candidate) {
        if (file_exists($candidate) && is_file($candidate)) {
            $filePath = $candidate;
            break;
        }
    }
    if (!$filePath) {
        $baseName = basename($path);
        $matches = glob(storage_path('app/**/' . $baseName)) ?: glob(storage_path('app/' . $baseName));
        if (!empty($matches) && is_file($matches[0])) {
            $filePath = $matches[0];
        }
    }
    if (!$filePath || !is_file($filePath)) {
        abort(404);
    }
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $mimeType = match ($ext) {
        'png' => 'image/png',
        'jpg', 'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'gif' => 'image/gif',
        'mp3' => 'audio/mpeg',
        'wav' => 'audio/wav',
        'ogg' => 'audio/ogg',
        'ico' => 'image/x-icon',
        default => mime_content_type($filePath) ?: 'application/octet-stream',
    };
    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*');



