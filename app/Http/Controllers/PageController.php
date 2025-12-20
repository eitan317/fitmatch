<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use App\Mail\ContactMail;
use App\Models\Trainer;
use App\Models\User;
use App\Models\Review;

class PageController extends Controller
{
    /**
     * Display the welcome/home page with dynamic statistics.
     */
    public function welcome()
    {
        // Check if status column exists (for migration compatibility)
        $activeTrainers = 0;
        if (Schema::hasColumn('trainers', 'status')) {
            $activeTrainers = Trainer::where('status', 'approved')->count();
        } else {
            // Fallback: count all trainers if status column doesn't exist yet
            $activeTrainers = Trainer::count();
        }

        // Check if reviews table exists (for migration compatibility)
        $averageRating = 0;
        $totalReviews = 0;
        if (Schema::hasTable('reviews')) {
            $averageRating = round(Review::avg('rating') ?? 0, 1);
            $totalReviews = Review::count();
        }

        $stats = [
            'active_trainers' => $activeTrainers,
            'satisfied_trainees' => User::count(),
            'average_rating' => $averageRating,
            'total_reviews' => $totalReviews,
        ];

        return view('welcome', compact('stats'));
    }

    /**
     * Display the about us page.
     */
    public function about()
    {
        return view('pages.about');
    }

    /**
     * Display the FAQ page.
     */
    public function faq()
    {
        return view('pages.faq');
    }

    /**
     * Display the contact page.
     */
    public function contact()
    {
        return view('pages.contact');
    }

    /**
     * Handle contact form submission.
     */
    public function storeContact(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        try {
            Mail::to('fitmatchcoil@gmail.com')
                ->send(new ContactMail(
                    $validated['name'],
                    $validated['email'],
                    $validated['subject'],
                    $validated['message']
                ));
            
            return redirect()->route('contact')
                ->with('success', 'תודה על פנייתך! נחזור אליך בהקדם האפשרי.');
        } catch (\Exception $e) {
            \Log::error('Contact form email error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // אם זה development, אפשר להציג את השגיאה
            if (config('app.debug')) {
                return redirect()->route('contact')
                    ->with('error', 'שגיאה: ' . $e->getMessage());
            }
            
            return redirect()->route('contact')
                ->with('error', 'אירעה שגיאה בשליחת ההודעה. אנא נסה שוב או צור קשר ישירות במייל: fitmatchcoil@gmail.com');
        }
    }

    /**
     * Display the privacy policy page.
     */
    public function privacy()
    {
        return view('pages.privacy');
    }

    /**
     * Display the terms of use page.
     */
    public function terms()
    {
        return view('pages.terms');
    }
}
