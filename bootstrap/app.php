<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::namespace('postback')->prefix('postback')->name('postback.')->group(base_path('routes/postback.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: ['livewire/*']);
        $middleware->redirectGuestsTo(fn() => '/');
        $middleware->redirectUsersTo(fn() => '/');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
