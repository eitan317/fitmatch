<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trainer extends Model
{
    protected $fillable = [
        'owner_email',
        'full_name',
        'name', // Added to allow null values for backward compatibility
        'specialty', // Added to allow null values for backward compatibility
        'age',
        'city',
        'phone',
        'experience_years',
        'main_specialization',
        'price_per_session',
        'training_types',
        'instagram',
        'tiktok',
        'bio',
        'profile_image_path',
        'status',
        'subscription_plan_id',
        'subscription_status',
        'trial_started_at',
        'trial_ends_at',
        'last_payment_at',
        'approved_by_admin',
        'plan_choice',
    ];

    protected $casts = [
        'training_types' => 'array',
        'age' => 'integer',
        'experience_years' => 'integer',
        'price_per_session' => 'decimal:2',
        'trial_started_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'last_payment_at' => 'datetime',
        'approved_by_admin' => 'boolean',
    ];

    /**
     * Get the reviews for the trainer.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the subscription plan for this trainer.
     */
    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Get all subscriptions for this trainer.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(TrainerSubscription::class);
    }

    /**
     * Get the active subscription for this trainer.
     */
    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    /**
     * Get the average rating for the trainer.
     */
    public function getAverageRatingAttribute(): ?float
    {
        $reviews = $this->reviews;
        if ($reviews->isEmpty()) {
            return null;
        }
        return round($reviews->avg('rating'), 2);
    }

    /**
     * Get the rating count for the trainer.
     */
    public function getRatingCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    /**
     * Get training type labels in Hebrew.
     */
    public static function getTrainingTypeLabels(): array
    {
        return [
            'gym_basic' => 'חדר כושר בסיסי',
            'hypertrophy' => 'מסת שריר',
            'powerlifting' => 'פאוורליפטינג',
            'crossfit' => 'קרוספיט',
            'street_workout' => 'סטריט וורקאאוט / מתח מקבילים',
            'weightloss' => 'חיטוב / ירידה במשקל',
            'hiit' => 'אימוני HIIT',
            'intervals' => 'אינטרוולים עצימים',
            'mobility' => 'מוביליטי וגמישות',
            'yoga' => 'יוגה',
            'pilates' => 'פילאטיס',
            'physio_rehab' => 'שיקום / פיזיותרפיה',
            'back_pain' => 'אימונים לכאבי גב',
            'postnatal' => 'נשים אחרי לידה',
            'home_bodyweight' => 'אימוני בית (משקל גוף)',
            'trx' => 'אימוני TRX',
            'short20' => 'אימונים קצרים (20 דק׳)',
            'running' => 'ריצה',
            'sprints' => 'ספרינטים',
            'marathon' => 'הכנה למרתון / חצי מרתון',
            'cycling' => 'רכיבה על אופניים',
            'swimming' => 'שחייה',
            'boxing' => 'אגרוף',
            'kickboxing' => 'קיקבוקס',
            'mma' => 'MMA',
            'kravmaga' => 'קרב מגע',
            'couple' => 'אימונים זוגיים',
            'group' => 'אימונים קבוצתיים',
            'online' => 'אימונים אונליין (זום)',
            'outdoor' => 'אימונים בחוץ / בפארק',
            'bootcamp' => 'בוטקמפ',
            'women_only' => 'נשים בלבד',
            'men_only' => 'גברים בלבד',
            'teens' => 'נוער',
            'kids' => 'ילדים',
            'seniors' => 'גיל שלישי',
        ];
    }

    /**
     * Get training types with Hebrew labels.
     */
    public function getTrainingTypesWithLabels(): array
    {
        if (!$this->training_types || !is_array($this->training_types)) {
            return [];
        }

        $labels = self::getTrainingTypeLabels();
        $result = [];

        foreach ($this->training_types as $type) {
            $result[] = [
                'value' => $type,
                'label' => $labels[$type] ?? $type,
            ];
        }

        return $result;
    }

    /**
     * Check if trainer is in trial period.
     */
    public function isTrial(): bool
    {
        return $this->status === 'trial';
    }

    /**
     * Check if trainer is pending payment.
     */
    public function isPendingPayment(): bool
    {
        return $this->status === 'pending_payment';
    }

    /**
     * Check if trainer is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if trial period has expired.
     */
    public function isTrialExpired(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    /**
     * Get status message in Hebrew.
     */
    public function getStatusMessage(): string
    {
        return match ($this->status) {
            'trial' => 'אתה בחודש ניסיון – לאחר סיום החודש יש לשלם 20₪ בביט',
            'pending_payment' => 'יש לשלם 20₪ בביט. מספר Bit: 0527020113',
            'active' => 'החשבון פעיל',
            'blocked' => 'חשבון חסום',
            'pending' => 'ממתין לאישור',
            default => 'סטטוס לא מוגדר',
        };
    }
}
