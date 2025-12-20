<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\TrainerSubscription;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * Show the plan selection page.
     */
    public function choosePlan()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
        return view('subscriptions.choose-plan', compact('plans'));
    }

    /**
     * Subscribe to a plan (creates pending subscription).
     */
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
        ]);

        // Check if plan is active
        $plan = SubscriptionPlan::where('id', $validated['subscription_plan_id'])
            ->where('is_active', true)
            ->firstOrFail();

        // Check if user has a trainer
        $trainer = Trainer::where('owner_email', Auth::user()->email)->first();

        if (!$trainer) {
            return redirect()->route('trainers.create')
                ->with('error', 'יש ליצור פרופיל מאמן קודם');
        }

        // Check if plan is active
        $plan = SubscriptionPlan::where('id', $validated['subscription_plan_id'])
            ->where('is_active', true)
            ->firstOrFail();

        // Create subscription with pending_payment status
        $subscription = TrainerSubscription::create([
            'trainer_id' => $trainer->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'pending_payment',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
            'auto_renew' => true,
        ]);

        return redirect()->route('subscriptions.payment', $subscription);
    }

    /**
     * Show my subscription management page.
     */
    public function mySubscription()
    {
        $trainer = Trainer::where('owner_email', Auth::user()->email)->first();

        if (!$trainer) {
            return redirect()->route('trainers.index')
                ->with('error', 'לא נמצא פרופיל מאמן');
        }

        $subscription = $trainer->activeSubscription();
        $allSubscriptions = $trainer->subscriptions()->orderBy('created_at', 'desc')->get();

        return view('subscriptions.my-subscription', compact('subscription', 'allSubscriptions', 'trainer'));
    }

    /**
     * Cancel subscription.
     */
    public function cancel(Request $request)
    {
        $validated = $request->validate([
            'subscription_id' => 'required|exists:trainer_subscriptions,id',
        ]);

        $trainer = Trainer::where('owner_email', Auth::user()->email)->first();

        if (!$trainer) {
            return redirect()->back()->with('error', 'לא נמצא פרופיל מאמן');
        }

        $subscription = TrainerSubscription::where('id', $validated['subscription_id'])
            ->where('trainer_id', $trainer->id)
            ->firstOrFail();

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'auto_renew' => false,
        ]);

        $trainer->update([
            'subscription_status' => 'cancelled',
        ]);

        return redirect()->route('subscriptions.my-subscription')
            ->with('success', 'המנוי בוטל בהצלחה');
    }

    /**
     * Renew subscription.
     */
    public function renew(Request $request)
    {
        $validated = $request->validate([
            'subscription_id' => 'required|exists:trainer_subscriptions,id',
        ]);

        $trainer = Trainer::where('owner_email', Auth::user()->email)->first();

        if (!$trainer) {
            return redirect()->back()->with('error', 'לא נמצא פרופיל מאמן');
        }

        $subscription = TrainerSubscription::where('id', $validated['subscription_id'])
            ->where('trainer_id', $trainer->id)
            ->firstOrFail();

        // Create new subscription
        $newSubscription = TrainerSubscription::create([
            'trainer_id' => $trainer->id,
            'subscription_plan_id' => $subscription->subscription_plan_id,
            'status' => 'pending_payment',
            'starts_at' => $subscription->expires_at,
            'expires_at' => $subscription->expires_at->addMonth(),
            'auto_renew' => true,
        ]);

        return redirect()->route('subscriptions.payment', $newSubscription)
            ->with('success', 'המנוי יוארך לאחר ביצוע התשלום');
    }
}

