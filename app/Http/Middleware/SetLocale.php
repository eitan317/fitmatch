<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from session, default to 'he'
        $locale = Session::get('locale', 'he');
        
        // Validate locale
        $supportedLocales = ['he', 'ar', 'ru', 'en'];
        if (!in_array($locale, $supportedLocales)) {
            $locale = 'he';
        }
        
        // Set the application locale
        App::setLocale($locale);
        
        // Also set it in config for consistency
        config(['app.locale' => $locale]);
        
        return $next($request);
    }
}

