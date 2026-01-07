<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class LanguageController extends Controller
{
    /**
     * Supported locales
     */
    private const SUPPORTED_LOCALES = ['he', 'ar', 'ru', 'en'];

    /**
     * Switch language and redirect to language-prefixed URL
     */
    public function switchLanguage($locale)
    {
        if (!in_array($locale, self::SUPPORTED_LOCALES)) {
            $locale = 'he';
        }
        
        // Set locale in session
        Session::put('locale', $locale);
        
        // Set locale in application
        App::setLocale($locale);
        
        // Also set in config
        config(['app.locale' => $locale]);
        
        // Save session immediately
        Session::save();
        
        // Get the current URL path
        $previousUrl = url()->previous();
        $currentPath = parse_url($previousUrl, PHP_URL_PATH);
        
        // Remove existing language prefix if present
        $pathSegments = explode('/', trim($currentPath, '/'));
        if (!empty($pathSegments[0]) && in_array($pathSegments[0], self::SUPPORTED_LOCALES)) {
            array_shift($pathSegments);
        }
        
        // Build new path with language prefix
        $newPath = '/' . $locale;
        if (!empty($pathSegments)) {
            $newPath .= '/' . implode('/', $pathSegments);
        }
        
        // Preserve query string if present
        $queryString = parse_url($previousUrl, PHP_URL_QUERY);
        if ($queryString) {
            $newPath .= '?' . $queryString;
        }
        
        return redirect($newPath);
    }
}

