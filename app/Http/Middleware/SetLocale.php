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
     * Supported locales
     */
    private const SUPPORTED_LOCALES = ['he', 'ar', 'ru', 'en'];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = 'he'; // Default locale
        
        // First, try to get locale from URL prefix (e.g., /he/trainers, /en/trainers)
        $path = trim($request->path(), '/');
        $segments = explode('/', $path);
        
        if (!empty($segments[0]) && in_array($segments[0], self::SUPPORTED_LOCALES)) {
            // Language prefix found in URL
            $locale = $segments[0];
        } else {
            // Fall back to session
            $locale = Session::get('locale', 'he');
        }
        
        // Validate locale
        if (!in_array($locale, self::SUPPORTED_LOCALES)) {
            $locale = 'he';
        }
        
        // Set the application locale
        App::setLocale($locale);
        
        // Store in session for consistency
        Session::put('locale', $locale);
        
        // Also set it in config for consistency
        config(['app.locale' => $locale]);
        
        return $next($request);
    }
}

