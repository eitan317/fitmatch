<?php

namespace App\Http\Controllers;

use App\Models\TrainerSubscription;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Show payment page.
     */
    public function payment(TrainerSubscription $subscription)
    {
        // Verify ownership
        $trainer = Trainer::where('owner_email', Auth::user()->email)->first();

        if (!$trainer || $subscription->trainer_id !== $trainer->id) {
            return redirect()->route('trainers.index')
                ->with('error', 'אין לך הרשאה לגשת לתשלום זה');
        }

        return view('subscriptions.payment', compact('subscription'));
    }

    /**
     * Process payment (simulated).
     */
    public function processPayment(Request $request, TrainerSubscription $subscription)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|in:credit_card,paypal,bit',
        ]);

        // Verify ownership
        $trainer = Trainer::where('owner_email', Auth::user()->email)->first();

        if (!$trainer || $subscription->trainer_id !== $trainer->id) {
            return redirect()->route('trainers.index')
                ->with('error', 'אין לך הרשאה לגשת לתשלום זה');
        }

        // Simulate payment processing
        // In real implementation, this would integrate with payment gateway
        $paymentId = 'MOCK_' . time() . '_' . rand(1000, 9999);

        // Validate training types count based on subscription plan
        $plan = $subscription->plan;
        if ($plan && $plan->max_training_types !== null) {
            $trainingTypesCount = count($trainer->training_types ?? []);
            if ($trainingTypesCount > $plan->max_training_types) {
                // Limit training types to max allowed
                $trainer->training_types = array_slice($trainer->training_types ?? [], 0, $plan->max_training_types);
                $trainer->save();
            }
        }

        // Update subscription
        $subscription->update([
            'status' => 'active',
            'payment_provider' => 'mock',
            'payment_id' => $paymentId,
            'payment_method' => $validated['payment_method'],
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        // Update trainer
        $trainer->update([
            'subscription_plan_id' => $subscription->subscription_plan_id,
            'subscription_status' => 'active',
        ]);

        return redirect()->route('subscriptions.payment-success', $subscription)
            ->with('success', 'התשלום בוצע בהצלחה!');
    }

    /**
     * Payment success page.
     */
    public function paymentSuccess(TrainerSubscription $subscription)
    {
        $trainer = Trainer::where('owner_email', Auth::user()->email)->first();

        if (!$trainer || $subscription->trainer_id !== $trainer->id) {
            return redirect()->route('trainers.index')
                ->with('error', 'אין לך הרשאה לגשת לדף זה');
        }

        return view('subscriptions.payment-success', compact('subscription'));
    }

    /**
     * Payment failed page.
     */
    public function paymentFailed(TrainerSubscription $subscription)
    {
        $trainer = Trainer::where('owner_email', Auth::user()->email)->first();

        if (!$trainer || $subscription->trainer_id !== $trainer->id) {
            return redirect()->route('trainers.index')
                ->with('error', 'אין לך הרשאה לגשת לדף זה');
        }

        return view('subscriptions.payment-failed', compact('subscription'));
    }

    /**
     * Webhook for payment providers (for future implementation).
     */
    public function webhook(Request $request)
    {
        // This would handle webhooks from payment providers
        // For now, just log the request
        \Log::info('Payment webhook received', $request->all());

        return response()->json(['status' => 'received']);
    }
}

