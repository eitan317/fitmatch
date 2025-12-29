<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainerDashboardController extends Controller
{
    /**
     * Display the trainer dashboard.
     */
    public function index()
    {
        $trainer = Trainer::where('owner_email', Auth::user()->email)
            ->with(['reviews', 'profileViews'])
            ->firstOrFail();

        // Calculate statistics
        $stats = [
            'total_views' => $trainer->total_views,
            'views_today' => $trainer->views_today,
            'views_this_month' => $trainer->views_this_month,
            'total_reviews' => $trainer->reviews()->count(),
            'average_rating' => $trainer->average_rating ?? 0,
            'positive_reviews' => $trainer->reviews()->where('rating', '>=', 4)->count(),
            'negative_reviews' => $trainer->reviews()->where('rating', '<=', 2)->count(),
        ];

        // Get recent reviews
        $recentReviews = $trainer->reviews()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('trainer-dashboard', compact('trainer', 'stats', 'recentReviews'));
    }

    /**
     * Display all reviews for the trainer.
     */
    public function reviews(Request $request)
    {
        $trainer = Trainer::where('owner_email', Auth::user()->email)->firstOrFail();

        $query = $trainer->reviews()->orderBy('created_at', 'desc');

        // Filter by rating if provided
        if ($request->has('rating') && $request->rating) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->paginate(20);

        return view('trainer-reviews', compact('trainer', 'reviews'));
    }

    /**
     * Display detailed statistics.
     */
    public function statistics()
    {
        $trainer = Trainer::where('owner_email', Auth::user()->email)
            ->with(['reviews', 'profileViews'])
            ->firstOrFail();

        // Detailed statistics
        $stats = [
            'total_views' => $trainer->total_views,
            'views_today' => $trainer->views_today,
            'views_this_week' => $trainer->profileViews()
                ->where('viewed_at', '>=', now()->subWeek())
                ->count(),
            'views_this_month' => $trainer->views_this_month,
            'total_reviews' => $trainer->reviews()->count(),
            'average_rating' => $trainer->average_rating ?? 0,
            'rating_distribution' => [
                5 => $trainer->reviews()->where('rating', 5)->count(),
                4 => $trainer->reviews()->where('rating', 4)->count(),
                3 => $trainer->reviews()->where('rating', 3)->count(),
                2 => $trainer->reviews()->where('rating', 2)->count(),
                1 => $trainer->reviews()->where('rating', 1)->count(),
            ],
        ];

        // Get recent views (last 10)
        $recentViews = $trainer->profileViews()
            ->with('user')
            ->orderBy('viewed_at', 'desc')
            ->limit(10)
            ->get();

        return view('trainer-statistics', compact('trainer', 'stats', 'recentViews'));
    }

    /**
     * Show the form for editing the trainer profile.
     */
    public function editProfile()
    {
        $trainer = Trainer::where('owner_email', Auth::user()->email)
            ->firstOrFail();

        return view('trainer-edit-profile', compact('trainer'));
    }

    /**
     * Update the trainer profile.
     */
    public function updateProfile(Request $request)
    {
        $trainer = Trainer::where('owner_email', Auth::user()->email)->firstOrFail();

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:18|max:120',
            'city' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0',
            'main_specialization' => 'nullable|string|max:255',
            'price_per_session' => 'nullable|numeric|min:0',
            'training_types' => 'nullable|array',
            'instagram' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ], [
            'age.min' => 'הגיל המינימלי המותר הוא 18',
            'age.integer' => 'הגיל חייב להיות מספר שלם',
        ]);

        // Validate training types count based on subscription plan
        if ($trainer->subscription_plan_id) {
            $plan = $trainer->subscriptionPlan;
            if ($plan && $plan->max_training_types !== null) {
                $trainingTypesCount = count($validated['training_types'] ?? []);
                if ($trainingTypesCount > $plan->max_training_types) {
                    return redirect()->back()
                        ->withErrors(['training_types' => "תכנית המנוי מאפשרת עד {$plan->max_training_types} סוגי אימונים בלבד."])
                        ->withInput();
                }
            }
        }

        $trainer->update($validated);

        return redirect()->route('trainer.dashboard')
            ->with('success', 'הפרופיל עודכן בהצלחה.');
    }
}

