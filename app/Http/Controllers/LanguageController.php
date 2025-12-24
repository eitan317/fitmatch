<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switchLanguage($locale)
    {
        $supportedLocales = ['he', 'ar', 'ru', 'en'];
        
        if (in_array($locale, $supportedLocales)) {
            // Set locale in session
            Session::put('locale', $locale);
            
            // Set locale in application
            App::setLocale($locale);
            
            // Also set in config
            config(['app.locale' => $locale]);
            
            // Save session immediately
            Session::save();
        }
        
        return redirect()->back();
    }
}

