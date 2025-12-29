<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerImage extends Model
{
    protected $fillable = [
        'trainer_id',
        'image_path',
        'image_type',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the trainer that owns the image.
     */
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }
}

