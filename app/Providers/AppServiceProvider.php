<?php

namespace App\Providers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            config([
                'services.google.client_id' => setting('services.google.client_id') ?? config('services.google.client_id'),
                'services.google.client_secret' => setting('services.google.client_secret') ?? config('services.google.client_secret'),
            ]);

            Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page') {
                $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

                return new LengthAwarePaginator(
                    $total ? $this : $this->forPage($page, $perPage)->values(),
                    $total ?: $this->count(),
                    $perPage,
                    $page,
                    [
                        'path' => LengthAwarePaginator::resolveCurrentPath(),
                        'pageName' => $pageName,
                    ]
                );
            });

            LogViewer::auth(function ($request) {
                return $request->user() && $request->user()->isAdmin();
            });
        } catch (\Exception $e) {
        }
    }
}
