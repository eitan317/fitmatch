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
}

