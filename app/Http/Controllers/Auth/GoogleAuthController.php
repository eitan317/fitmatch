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
            \Log::error('Google OAuth: Socialite not installed');
            return redirect('/login')->with('error', 'Laravel Socialite לא מותקן. אנא הרץ: composer install');
        }
        
        try {
            // Get Google user information
            $googleUser = \Laravel\Socialite\Facades\Socialite::driver('google')->user();
            
            // Validate email exists
            if (!$googleUser->email) {
                \Log::error('Google OAuth: No email provided by Google', [
                    'google_id' => $googleUser->id ?? null,
                    'name' => $googleUser->name ?? null
                ]);
                return redirect('/login')->with('error', 'לא ניתן היה לקבל כתובת אימייל מגוגל. אנא נסה שוב.');
            }
            
            // Check if user exists by email
            $user = User::where('email', $googleUser->email)->first();
            
            if ($user) {
                // Update existing user with Google info
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar ?? $user->avatar, // Keep existing avatar if Google doesn't provide one
                ]);
                
                \Log::info('Google OAuth: Existing user logged in', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->name ?? 'User',
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => bcrypt(uniqid() . time()), // Random password with timestamp for uniqueness
                    'email_verified_at' => now(), // Google emails are verified
                ]);
                
                \Log::info('Google OAuth: New user created', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }

            // Log the user in
            Auth::login($user, true);
            
            // Redirect to intended page or home
            return redirect()->intended('/');
            
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            // Handle invalid state (CSRF token mismatch or expired)
            \Log::warning('Google OAuth: Invalid state exception', [
                'error' => $e->getMessage()
            ]);
            return redirect('/login')->with('error', 'פג תוקף הבקשה. אנא נסה להתחבר שוב.');
            
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle OAuth errors from Google
            \Log::error('Google OAuth: Client exception', [
                'error' => $e->getMessage(),
                'response' => $e->getResponse()->getBody()->getContents() ?? null
            ]);
            return redirect('/login')->with('error', 'שגיאה בהתחברות עם גוגל. אנא נסה שוב מאוחר יותר.');
            
        } catch (\Exception $e) {
            // Handle any other errors
            \Log::error('Google OAuth: Unexpected error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/login')->with('error', 'שגיאה בהתחברות עם גוגל: ' . $e->getMessage());
        }
    }
}

