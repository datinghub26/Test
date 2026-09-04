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

Route::get('/fix-admin-notifications', function () {
    $results = [];
    try {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('notifications', 'notifiable_type')) {
            \Illuminate\Support\Facades\Schema::table('notifications', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('notifiable_type')->nullable()->after('user_id');
                $table->unsignedBigInteger('notifiable_id')->nullable()->after('notifiable_type');
                $table->longText('data')->nullable()->after('notifiable_id');
                $table->timestamp('read_at')->nullable()->after('data');
            });
            $results[] = "Added notifiable_type, notifiable_id, data, read_at columns to notifications table.";
        } else {
            $results[] = "Columns already exist on notifications table.";
        }

        try {
            \Illuminate\Support\Facades\Schema::table('notifications', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->index(['notifiable_type', 'notifiable_id'], 'notifications_notifiable_index');
            });
            $results[] = "Index added.";
        } catch (\Throwable $e) {
            $results[] = "Index status: " . $e->getMessage();
        }

        $cachePath = base_path('bootstrap/cache/filament');
        if (file_exists($cachePath)) {
            \Illuminate\Support\Facades\File::deleteDirectory($cachePath);
            $results[] = "Deleted bootstrap/cache/filament.";
        }

        $viewsPath = storage_path('framework/views');
        if (file_exists($viewsPath)) {
            foreach (glob("$viewsPath/*.php") as $f) {
                @unlink($f);
            }
            $results[] = "Cleared compiled blade view cache.";
        }

        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $results[] = "Artisan optimize:clear executed.";


        return response()->json(['status' => 'success', 'results' => $results]);
    } catch (\Throwable $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'results' => $results], 500);
    }
});

Route::get('/debug-storage', function () {
    $info = [
        'storage_path_public' => storage_path('app/public'),
        'public_dir_exists' => is_dir(storage_path('app/public')),
        'public_files_count' => is_dir(storage_path('app/public')) ? count(scandir(storage_path('app/public'))) : 0,
        'app_files_count' => is_dir(storage_path('app')) ? count(scandir(storage_path('app'))) : 0,
        'public_html_storage_exists' => file_exists(base_path('../public_html/storage')),
        'public_html_storage_is_link' => is_link(base_path('../public_html/storage')),
        'public_html_storage_target' => is_link(base_path('../public_html/storage')) ? readlink(base_path('../public_html/storage')) : null,
    ];

    // Find any 01KY files
    $info['files_in_app_public'] = glob(storage_path('app/public/*01KY*')) ?: [];
    $info['files_in_app'] = glob(storage_path('app/*01KY*')) ?: [];
    $info['providers'] = \App\Models\Provider::all(['id', 'name', 'image', 'url'])->toArray();

    return response()->json($info);
});

Route::get('/storage/{path}', function ($path) {
    $candidates = [
        storage_path('app/public/' . $path),
        storage_path('app/' . $path),
        public_path('storage/' . $path),
        base_path('../public_html/storage/' . $path),
    ];
    $filePath = null;
    foreach ($candidates as $candidate) {
        if (file_exists($candidate) && is_file($candidate)) {
            $filePath = $candidate;
            break;
        }
    }
    if (!$filePath) {
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


