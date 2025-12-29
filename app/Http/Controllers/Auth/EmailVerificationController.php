<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationMail;
use App\Models\EmailVerificationCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class EmailVerificationController extends Controller
{
    /**
     * Check if email exists and send OTP code.
     */
    public function checkEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = strtolower($validated['email']);
        $ipAddress = $request->ip();

        // Rate limiting: 3 codes per hour per email
        $key = 'email_verification:' . $email;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "יותר מדי בקשות. נסה שוב בעוד " . ceil($seconds / 60) . " דקות.",
            ]);
        }

        // Check if email already exists
        if (User::where('email', $email)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'אימייל זה כבר רשום במערכת. אנא התחבר במקום.',
                'email_exists' => true,
            ], 422);
        }

        // Check mail configuration
        $mailer = config('mail.default');
        $mailHost = config('mail.mailers.smtp.host');
        
        // Log configuration
        \Log::info('Email verification attempt', [
            'email' => $email,
            'mailer' => $mailer,
            'mail_host' => $mailHost,
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_username' => config('mail.mailers.smtp.username') ? 'set' : 'not set',
            'mail_password' => config('mail.mailers.smtp.password') ? 'set' : 'not set',
            'mail_from' => config('mail.from.address'),
        ]);

        // Create verification code
        $verificationCode = EmailVerificationCode::createForEmail($email, $ipAddress);
        
        // If using 'log' driver, save code to log for testing and return success
        if ($mailer === 'log') {
            \Log::info('═══════════════════════════════════════════════════════');
            \Log::info('EMAIL VERIFICATION CODE (Log Driver Active)');
            \Log::info('═══════════════════════════════════════════════════════');
            \Log::info('Email: ' . $email);
            \Log::info('Code: ' . $verificationCode->code);
            \Log::info('═══════════════════════════════════════════════════════');
            \Log::info('Mail driver is set to "log".');
            \Log::info('To send real emails, change MAIL_MAILER=smtp in .env');
            \Log::info('and add MAIL_PASSWORD (Gmail App Password)');
            \Log::info('═══════════════════════════════════════════════════════');
            
            RateLimiter::hit($key, 3600); // 1 hour

            return response()->json([
                'success' => true,
                'message' => 'קוד אימות נשלח. בדוק את storage/logs/laravel.log - הקוד שם!',
                'code' => $verificationCode->code, // Return code for testing
                'mailer' => 'log',
            ]);
        }

        // Try to send email via SMTP
        try {
            Mail::to($email)->send(new EmailVerificationMail($verificationCode->code));
            
            \Log::info('Verification email sent successfully', [
                'email' => $email,
                'code' => $verificationCode->code,
                'mailer' => $mailer,
            ]);
            
            RateLimiter::hit($key, 3600); // 1 hour

            return response()->json([
                'success' => true,
                'message' => 'קוד אימות נשלח לאימייל שלך. אנא בדוק את תיבת הדואר.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error sending verification email', [
                'email' => $email,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'mailer' => $mailer,
                'mail_host' => $mailHost,
                'trace' => $e->getTraceAsString(),
            ]);
            
            $errorMessage = 'שגיאה בשליחת האימייל.';
            
            // Provide helpful error messages
            if (str_contains($e->getMessage(), 'Connection') || str_contains($e->getMessage(), 'SMTP')) {
                $errorMessage .= ' בעיית חיבור לשרת האימייל. בדוק את הגדרות SMTP ב-.env';
            } elseif (str_contains($e->getMessage(), 'Authentication')) {
                $errorMessage .= ' שגיאת אימות. בדוק את שם המשתמש והסיסמה ב-.env';
            } else {
                $errorMessage .= ' ' . $e->getMessage();
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Verify the OTP code.
     */
    public function verifyCode(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'code' => 'required|string|size:6',
        ]);

        $email = strtolower($validated['email']);
        $code = $validated['code'];
        $ipAddress = $request->ip();

        // Rate limiting: 5 attempts per code
        $key = 'verify_code:' . $email . ':' . $code;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'code' => "יותר מדי ניסיונות. נסה שוב בעוד " . ceil($seconds / 60) . " דקות.",
            ]);
        }

        // Find valid code
        $verificationCode = EmailVerificationCode::findValid($email, $code);

        if (!$verificationCode) {
            RateLimiter::hit($key, 300); // 5 minutes
            
            throw ValidationException::withMessages([
                'code' => 'קוד האימות שגוי או פג תוקף. אנא נסה שוב.',
            ]);
        }

        // Mark code as used
        $verificationCode->markAsUsed();

        // Store verification in session
        session([
            'email_verified' => $email,
            'email_verified_at' => now()->toDateTimeString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'אימייל אומת בהצלחה!',
        ]);
    }

    /**
     * Resend verification code.
     */
    public function resendCode(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = strtolower($validated['email']);
        $ipAddress = $request->ip();

        // Rate limiting: 3 codes per hour per email
        $key = 'email_verification:' . $email;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "יותר מדי בקשות. נסה שוב בעוד " . ceil($seconds / 60) . " דקות.",
            ]);
        }

        // Check if email already exists
        if (User::where('email', $email)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'אימייל זה כבר רשום במערכת.',
                'email_exists' => true,
            ], 422);
        }

        // Check mail configuration
        $mailer = config('mail.default');
        
        // Create verification code
        $verificationCode = EmailVerificationCode::createForEmail($email, $ipAddress);
        
        // If using 'log' driver, save code to log and return it
        if ($mailer === 'log') {
            \Log::info('═══════════════════════════════════════════════════════');
            \Log::info('EMAIL VERIFICATION CODE (Resend - Log Driver Active)');
            \Log::info('═══════════════════════════════════════════════════════');
            \Log::info('Email: ' . $email);
            \Log::info('Code: ' . $verificationCode->code);
            \Log::info('═══════════════════════════════════════════════════════');
            
            RateLimiter::hit($key, 3600); // 1 hour

            return response()->json([
                'success' => true,
                'message' => 'קוד חדש נשלח. בדוק את storage/logs/laravel.log - הקוד שם!',
                'code' => $verificationCode->code,
                'mailer' => 'log',
            ]);
        }

        // Try to send email via SMTP
        try {
            Mail::to($email)->send(new EmailVerificationMail($verificationCode->code));
            
            \Log::info('Verification email resent successfully', [
                'email' => $email,
                'code' => $verificationCode->code,
            ]);
            
            RateLimiter::hit($key, 3600); // 1 hour

            return response()->json([
                'success' => true,
                'message' => 'קוד אימות חדש נשלח לאימייל שלך.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error resending verification email', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'שגיאה בשליחת האימייל: ' . $e->getMessage() . '. אנא נסה שוב מאוחר יותר.',
            ], 500);
        }
    }
}

