<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerProfileView extends Model
{
    protected $fillable = [
        'trainer_id',
        'ip_address',
        'user_agent',
        'user_id',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    /**
     * Get the trainer that was viewed.
     */
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    /**
     * Get the user who viewed (if logged in).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

