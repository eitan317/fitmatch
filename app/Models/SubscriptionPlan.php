<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'price',
        'features',
        'max_training_types',
        'priority',
        'badge_text',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'max_training_types' => 'integer',
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get all subscriptions for this plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(TrainerSubscription::class);
    }

    /**
     * Get all trainers with this plan.
     */
    public function trainers(): HasMany
    {
        return $this->hasMany(Trainer::class);
    }
}
