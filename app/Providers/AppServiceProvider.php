<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production (important for Railway/cloud deployments)
        // Use env() directly instead of config() to avoid bootstrap timing issues
        $appEnv = env('APP_ENV', 'local');
        $isProduction = $appEnv === 'production';
        $forceHttps = env('FORCE_HTTPS', false);
        
        if ($isProduction || $forceHttps) {
            URL::forceScheme('https');
            
            // Ensure trusted proxies are set for Railway's proxy headers
            // This helps Laravel correctly detect HTTPS behind Railway's proxy
            \Illuminate\Http\Request::setTrustedProxies(
                ['*'], 
                \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR | 
                \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO | 
                \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST
            );
        }

        // Ensure storage symlink exists (for Railway and other cloud platforms)
        $this->ensureStorageSymlink();

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('trainers:check-trial-expiration')->daily();
        });
    }

    /**
     * Ensure storage symlink exists.
     */
    protected function ensureStorageSymlink(): void
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        // Check if symlink already exists and is valid
        if (is_link($link)) {
            $currentTarget = readlink($link);
            if ($currentTarget === $target || realpath($currentTarget) === realpath($target)) {
                return; // Symlink already exists and points to correct location
            }
            // Remove broken symlink
            @unlink($link);
        }

        // Create symlink if it doesn't exist
        if (!file_exists($link) && !is_link($link)) {
            try {
                symlink($target, $link);
            } catch (\Exception $e) {
                // Silently fail - the route in web.php will handle file serving
                \Log::warning('Could not create storage symlink: ' . $e->getMessage());
            }
        }
    }
}
