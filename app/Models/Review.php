<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'trainer_id',
        'author_name',
        'rating',
        'text',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Get the trainer that owns the review.
     */
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }
}

