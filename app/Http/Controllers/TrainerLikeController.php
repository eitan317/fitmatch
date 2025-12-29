<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\TrainerLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainerLikeController extends Controller
{
    /**
     * Toggle like for a trainer.
     */
    public function toggle(Trainer $trainer)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'יש להתחבר כדי לעשות לייק',
            ], 401);
        }

        $existingLike = TrainerLike::where('trainer_id', $trainer->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingLike) {
            // Unlike - remove the like
            $existingLike->delete();
            $liked = false;
        } else {
            // Like - create new like
            TrainerLike::create([
                'trainer_id' => $trainer->id,
                'user_id' => $user->id,
            ]);
            $liked = true;
        }

        // Get updated count
        $count = $trainer->fresh()->likes_count;

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'count' => $count,
        ]);
    }
}

