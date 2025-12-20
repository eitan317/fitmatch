<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use Illuminate\Http\Request;

class TrainerController extends Controller
{
    /**
     * Display a listing of all trainers (pending and approved).
     */
    public function index()
    {
        // Platform Statistics
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_trainers' => Trainer::count(),
            'pending_trainers' => Trainer::where('status', 'pending')->count(),
            'approved_trainers' => Trainer::where('status', 'approved')->count(),
            'total_reviews' => \App\Models\Review::count(),
            'average_rating' => \App\Models\Review::avg('rating') ?? 0,
            'trainers_this_month' => Trainer::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'trainers_last_7_days' => Trainer::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        // Get trainers
        $pendingTrainers = Trainer::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedTrainers = Trainer::where('status', 'approved')
            ->with('reviews')
            ->orderBy('created_at', 'desc')
            ->limit(50) // Limit for performance
            ->get();

        return view('admin', compact('stats', 'pendingTrainers', 'approvedTrainers'));
    }

    /**
     * Approve the specified trainer.
     */
    public function approve(Trainer $trainer)
    {
        $trainer->update(['status' => 'approved']);

        return redirect()->route('admin.trainers.index')
            ->with('success', 'המאמן אושר בהצלחה.');
    }

    /**
     * Reject the specified trainer.
     */
    public function reject(Trainer $trainer)
    {
        // Delete profile image if exists
        if ($trainer->profile_image_path) {
            \Storage::disk('public')->delete($trainer->profile_image_path);
        }

        $trainer->delete();

        return redirect()->route('admin.trainers.index')
            ->with('success', 'המאמן נדחה ונמחק.');
    }

    /**
     * Delete an approved trainer from the system.
     */
    public function destroy(Trainer $trainer)
    {
        try {
            $trainerName = $trainer->full_name;

            // Delete profile image if exists
            if ($trainer->profile_image_path) {
                try {
                    \Storage::disk('public')->delete($trainer->profile_image_path);
                } catch (\Exception $e) {
                    // Log error but continue with deletion
                    \Log::warning('Failed to delete trainer profile image: ' . $e->getMessage());
                }
            }

            // Delete related reviews (cascade should handle this, but we'll be explicit)
            $trainer->reviews()->delete();

            // Delete the trainer record
            $trainer->delete();

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

