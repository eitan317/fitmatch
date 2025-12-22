<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainerController extends Controller
{
    /**
     * Display a listing of all trainers with new status system.
     */
    public function index()
    {
        // Platform Statistics
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_trainers' => Trainer::count(),
            'trial_trainers' => Trainer::where('status', 'trial')->count(),
            'pending_payment_trainers' => Trainer::where('status', 'pending_payment')->count(),
            'active_trainers' => Trainer::where('status', 'active')->count(),
            'blocked_trainers' => Trainer::where('status', 'blocked')->count(),
            'pending_trainers' => Trainer::where('status', 'pending')->count(),
            'total_reviews' => \App\Models\Review::count(),
            'average_rating' => \App\Models\Review::avg('rating') ?? 0,
            'trainers_this_month' => Trainer::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'trainers_last_7_days' => Trainer::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        // Get trainers by status
        $trialTrainers = Trainer::where('status', 'trial')
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingPaymentTrainers = Trainer::where('status', 'pending_payment')
            ->orderBy('created_at', 'desc')
            ->get();

        $activeTrainers = Trainer::where('status', 'active')
            ->with('reviews')
            ->orderBy('created_at', 'desc')
            ->limit(50) // Limit for performance
            ->get();

        $pendingTrainers = Trainer::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin', compact('stats', 'trialTrainers', 'pendingPaymentTrainers', 'activeTrainers', 'pendingTrainers'));
    }

    /**
     * Approve the specified trainer.
     */
    public function approve(Trainer $trainer)
    {
        $trainer->update([
            'status' => 'active',
            'approved_by_admin' => true,
            'last_payment_at' => now(),
        ]);

        return redirect()->route('admin.trainers.index')
            ->with('success', 'המאמן אושר בהצלחה.');
    }

    /**
     * Approve payment and activate trainer (for pending_payment status).
     */
    public function approvePayment(Trainer $trainer)
    {
        if ($trainer->status !== 'pending_payment') {
            return redirect()->route('admin.trainers.index')
                ->with('error', 'ניתן לאשר תשלום רק למאמנים בסטטוס pending_payment.');
        }

        $trainer->update([
            'status' => 'active',
            'approved_by_admin' => true,
            'last_payment_at' => now(),
        ]);

        return redirect()->route('admin.trainers.index')
            ->with('success', 'התשלום אושר והמאמן הופעל בהצלחה.');
    }

    /**
     * Reject the specified trainer.
     */
    public function reject(Trainer $trainer)
    {
        try {
            $trainerName = $trainer->full_name;
            $trainerId = $trainer->id;

            // Use DB transaction to ensure all deletions succeed
            DB::transaction(function () use ($trainer) {
                // Delete related reviews first
                $trainer->reviews()->delete();

                // Delete profile image if exists
                if ($trainer->profile_image_path) {
                    try {
                        \Storage::disk('public')->delete($trainer->profile_image_path);
                    } catch (\Exception $e) {
                        \Log::warning('Failed to delete trainer profile image: ' . $e->getMessage());
                    }
                }

                // Force delete the trainer (will work even if soft deletes are enabled)
                $trainer->forceDelete();
            });

            \Log::info("Trainer rejected and deleted successfully: ID {$trainerId}, Name: {$trainerName}");

            return redirect()->route('admin.trainers.index')
                ->with('success', 'המאמן נדחה ונמחק.');
        } catch (\Exception $e) {
            \Log::error('Error rejecting trainer: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->route('admin.trainers.index')
                ->with('error', 'אירעה שגיאה בדחיית המאמן. אנא נסה שוב.');
        }
    }

    /**
     * Delete an approved trainer from the system.
     */
    public function destroy(Trainer $trainer)
    {
        try {
            $trainerName = $trainer->full_name;
            $trainerId = $trainer->id;

            // Use DB transaction to ensure all deletions succeed
            DB::transaction(function () use ($trainer) {
                // Delete related reviews first
                $trainer->reviews()->delete();

                // Delete profile image if exists
                if ($trainer->profile_image_path) {
                    try {
                        \Storage::disk('public')->delete($trainer->profile_image_path);
                    } catch (\Exception $e) {
                        \Log::warning('Failed to delete trainer profile image: ' . $e->getMessage());
                    }
                }

                // Force delete the trainer (will work even if soft deletes are enabled)
                $trainer->forceDelete();
            });

            \Log::info("Trainer deleted successfully: ID {$trainerId}, Name: {$trainerName}");

            return redirect()->route('admin.trainers.index')
                ->with('success', 'המאמן "' . $trainerName . '" נמחק בהצלחה מהמערכת.');
        } catch (\Exception $e) {
            \Log::error('Error deleting trainer: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->route('admin.trainers.index')
                ->with('error', 'אירעה שגיאה במחיקת המאמן. אנא נסה שוב.');
        }
    }
}

