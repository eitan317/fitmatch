<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, Trainer $trainer)
    {
        $validated = $request->validate([
            'author_name' => 'nullable|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'text' => 'required|string|max:2000',
        ]);

        $review = Review::create([
            'trainer_id' => $trainer->id,
            'author_name' => $validated['author_name'] ?? Auth::user()->name,
            'rating' => $validated['rating'],
            'text' => $validated['text'],
        ]);

        return redirect()->route('trainers.show', $trainer)
            ->with('success', 'הביקורת נוספה בהצלחה.');
    }

    /**
     * Update review rating (admin only).
     */
    public function updateRating(Request $request, Review $review)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'רק מנהל יכול לערוך דירוגים');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $review->update([
            'rating' => $validated['rating'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'הדירוג עודכן בהצלחה',
            'rating' => $review->rating,
        ]);
    }

    /**
     * Delete a review (admin only).
     */
    public function destroy(Review $review)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'רק מנהל יכול למחוק ביקורות');
        }

        $trainerId = $review->trainer_id;
        $review->delete();

        return redirect()->route('trainers.show', $trainerId)
            ->with('success', 'הביקורת נמחקה בהצלחה.');
    }
}

