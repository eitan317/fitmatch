<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/auth.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);
        
        // Trust Railway proxy headers for HTTPS detection
        // Railway sets X-Forwarded-Proto header - trust all proxies (always safe on Railway)
        // Always enable on Railway (APP_ENV=production) or if explicitly requested
        $appEnv = env('APP_ENV', 'local');
        if ($appEnv === 'production' || env('TRUST_PROXIES', false)) {
            $middleware->trustProxies(at: '*');
        }
        
        // Add SecurityHeaders, TrackPageViews and SetLocale middleware to web group
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\TrackPageViews::class,
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
