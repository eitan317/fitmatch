<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\Review;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TrainerController extends Controller
{
    /**
     * Display a listing of approved trainers.
     */
    public function index(Request $request)
    {
        // Show active trainers and trial trainers (trial is like a demo of paid subscription)
        // Only show trainers approved by admin
        $query = Trainer::whereIn('status', ['active', 'trial'])
            ->where('approved_by_admin', true)
            ->with(['reviews', 'subscriptionPlan'])
            ->orderBy('created_at', 'desc');

        // Filter by city
        if ($request->has('city') && $request->city) {
            $query->where('city', $request->city);
        }

        // Filter by specialization
        if ($request->has('specialization') && $request->specialization) {
            $query->where('main_specialization', $request->specialization);
        }

        // Filter by price range
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price_per_session', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('price_per_session', '<=', $request->max_price);
        }

        // Filter by training types
        if ($request->has('training_type') && $request->training_type) {
            $query->whereJsonContains('training_types', $request->training_type);
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('full_name', 'like', '%' . $request->search . '%');
        }

        $trainers = $query->get();

        // Sort by subscription priority (higher priority first)
        $trainers = $trainers->sortByDesc(function ($trainer) {
            if ($trainer->subscriptionPlan) {
                return $trainer->subscriptionPlan->priority;
            }
            return 0;
        })->values();

        // Calculate average ratings
        foreach ($trainers as $trainer) {
            $trainer->average_rating = $trainer->average_rating;
            $trainer->rating_count = $trainer->rating_count;
        }

        return view('trainers', compact('trainers'));
    }

    /**
     * Show the form for creating a new trainer.
     */
    public function create()
    {
        return view('register-trainer');
    }

    /**
     * Store a newly created trainer in storage.
     */
    public function store(Request $request)
    {
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
            'profile_image' => 'nullable|image|max:2048',
        ], [
            'age.min' => 'הגיל המינימלי המותר הוא 18',
            'age.integer' => 'הגיל חייב להיות מספר שלם',
        ]);

        // Note: Training types validation will be done after subscription is selected
        // This validation is handled in the subscription flow

        // Handle profile image upload
        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('trainers', 'public');
        }

        // Get owner email from authenticated user
        $ownerEmail = Auth::user()->email;

        $trainerData = [
            'owner_email' => $ownerEmail,
            'full_name' => $validated['full_name'],
            'age' => $validated['age'] ?? null,
            'city' => $validated['city'],
            'phone' => $validated['phone'] ?? null,
            'experience_years' => $validated['experience_years'] ?? null,
            'main_specialization' => $validated['main_specialization'] ?? null,
            'price_per_session' => $validated['price_per_session'] ?? null,
            'training_types' => $validated['training_types'] ?? [],
            'instagram' => $validated['instagram'] ?? null,
            'tiktok' => $validated['tiktok'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'profile_image_path' => $profileImagePath,
            'status' => 'pending',
            'approved_by_admin' => false,
        ];

        // Don't include 'name' field - we use 'full_name' instead
        // If 'name' column exists and is required, it should be made nullable via migration
        // or removed from the table entirely

        $trainer = Trainer::create($trainerData);

        // Redirect to choose plan page
        return redirect()->route('trainers.choose-plan')
            ->with('success', 'ההרשמה הושלמה בהצלחה! בחר את האופציה המתאימה לך');
    }

    /**
     * Display plan choice page (pay now or trial).
     */
    public function choosePlan()
    {
        $trainer = Trainer::where('owner_email', Auth::user()->email)->firstOrFail();
        
        // Check if trainer has already used trial period
        $hasUsedTrial = $trainer->trial_ends_at !== null;
        
        return view('trainer-choose-plan', compact('trainer', 'hasUsedTrial'));
    }

    /**
     * Store the trainer's plan choice (pay now or trial).
     */
    public function storePlanChoice(Request $request)
    {
        $validated = $request->validate([
            'choice' => 'required|in:pay_now,trial',
        ]);
        
        $trainer = Trainer::where('owner_email', Auth::user()->email)->firstOrFail();
        
        // Security check: if already used trial, don't allow trial again
        if ($validated['choice'] === 'trial' && $trainer->trial_ends_at !== null) {
            return redirect()->back()
                ->with('error', 'לא ניתן לבחור חודש ניסיון פעם נוספת. יש לשלם כדי להמשיך.');
        }
        
        if ($validated['choice'] === 'pay_now') {
            // Chose to pay now - save choice, status remains 'pending' until admin approval
            $trainer->update([
                'plan_choice' => 'pay_now',
                // status remains 'pending'
            ]);
            
            return redirect()->route('trainers.payment-info')
                ->with('success', 'יש לשלם 20₪ דרך Bit כדי להמשיך. הבקשה ממתינה לאישור מנהל.');
        } else {
            // Chose trial period - save choice, status remains 'pending' until admin approval
            $trainer->update([
                'plan_choice' => 'trial',
                // status remains 'pending' - will be changed to 'trial' only after admin approval
            ]);
            
            return redirect()->route('trainers.trial-info')
                ->with('success', 'הבחירה נשמרה. ממתין לאישור מנהל.');
        }
    }

    /**
     * Display payment info page for pending payment.
     */
    public function paymentInfo()
    {
        $trainer = Trainer::where('owner_email', Auth::user()->email)->firstOrFail();
        
        return view('trainer-payment-info', compact('trainer'));
    }

    /**
     * Display trial info and payment instructions for the authenticated trainer.
     */
    public function trialInfo()
    {
        $trainer = Trainer::where('owner_email', Auth::user()->email)->firstOrFail();
        
        return view('trainer-trial-info', compact('trainer'));
    }

    /**
     * Display the specified trainer.
     */
    public function show(Trainer $trainer)
    {
        // Allow viewing active and trial trainers (trial is like a demo of paid subscription)
        if (!in_array($trainer->status, ['active', 'trial'])) {
            abort(404);
        }

        $trainer->load('reviews');
        $trainer->average_rating = $trainer->average_rating;
        $trainer->rating_count = $trainer->rating_count;

        return view('trainer-profile', compact('trainer'));
    }

    /**
     * Show the form for editing the specified trainer.
     */
    public function edit(Trainer $trainer)
    {
        return view('edit-trainer', compact('trainer'));
    }

    /**
     * Update the specified trainer in storage.
     */
    public function update(Request $request, Trainer $trainer)
    {
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
            'profile_image' => 'nullable|image|max:2048',
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
                        ->withErrors(['training_types' => "תכנית המנוי שלך מאפשרת עד {$plan->max_training_types} סוגי אימונים בלבד."])
                        ->withInput();
                }
            }
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($trainer->profile_image_path) {
                Storage::disk('public')->delete($trainer->profile_image_path);
            }
            $profileImagePath = $request->file('profile_image')->store('trainers', 'public');
            $trainer->profile_image_path = $profileImagePath;
        }

        // Don't include 'name' field - we use 'full_name' instead
        // If 'name' column exists and is required, it should be made nullable via migration
        // or removed from the table entirely

        $trainer->update($validated);

        return redirect()->route('trainers.show', $trainer)
            ->with('success', 'המאמן עודכן בהצלחה.');
    }

    /**
     * Remove the specified trainer from storage.
     */
    public function destroy(Trainer $trainer)
    {
        // Delete profile image if exists
        if ($trainer->profile_image_path) {
            Storage::disk('public')->delete($trainer->profile_image_path);
        }

        $trainer->delete();

        return redirect()->route('admin.trainers.index')
            ->with('success', 'המאמן נמחק בהצלחה.');
    }
}
