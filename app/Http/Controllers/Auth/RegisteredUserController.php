<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.unified', ['activeTab' => 'register']);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $email = strtolower($request->email);

        // Check if email was verified in session
        $verifiedEmail = session('email_verified');
        $verifiedAt = session('email_verified_at');

        if (!$verifiedEmail || $verifiedEmail !== $email) {
            return redirect()->back()
                ->withErrors(['email' => 'יש לאמת את האימייל לפני ההרשמה.'])
                ->withInput();
        }

        // Check if verification is not too old (max 30 minutes)
        if ($verifiedAt) {
            $verifiedTime = \Carbon\Carbon::parse($verifiedAt);
            if ($verifiedTime->diffInMinutes(now()) > 30) {
                return redirect()->back()
                    ->withErrors(['email' => 'אימות האימייל פג תוקף. אנא אמת את האימייל שוב.'])
                    ->withInput();
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(), // Mark as verified since we verified via OTP
        ]);

        event(new Registered($user));

        // Clear verification session
        session()->forget(['email_verified', 'email_verified_at']);

        Auth::login($user);

        return redirect('/');
    }
}
