<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect()
    {
        // Check if Socialite is installed
        if (!class_exists('Laravel\Socialite\Facades\Socialite')) {
            return redirect('/login')->with('error', 'Laravel Socialite לא מותקן. אנא הרץ: composer install');
        }
        
        // Check if Google credentials are configured
        $clientId = config('services.google.client_id');
        if (empty($clientId)) {
            return redirect('/login')->with('error', 'Google Client ID לא מוגדר. אנא הוסף GOOGLE_CLIENT_ID ל-.env והרץ: php artisan config:clear');
        }
        
        return \Laravel\Socialite\Facades\Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function callback()
    {
        // Check if Socialite is installed
        if (!class_exists('Laravel\Socialite\Facades\Socialite')) {
            return redirect('/login')->with('error', 'Laravel Socialite לא מותקן. אנא הרץ: composer install');
        }
        
        try {
            $googleUser = \Laravel\Socialite\Facades\Socialite::driver('google')->user();
            
            // Check if user exists by email
            $user = User::where('email', $googleUser->email)->first();
            
            if ($user) {
                // Update existing user with Google info
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => bcrypt(uniqid()), // Random password
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user, true);
            
            return redirect()->intended('/');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'שגיאה בהתחברות עם גוגל: ' . $e->getMessage());
        }
    }
}

