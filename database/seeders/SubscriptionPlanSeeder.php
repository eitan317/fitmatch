<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // תכנית בסיסית - 20 ש"ח לחודש
        SubscriptionPlan::create([
            'name' => 'בסיסית',
            'name_en' => 'Basic',
            'price' => 20.00,
            'features' => [
                'פרופיל בסיסי',
                'עד 3 סוגי אימונים',
                'הצגה ברשימת המאמנים',
            ],
            'max_training_types' => 3,
            'priority' => 1,
            'badge_text' => null,
            'is_active' => true,
        ]);

        // תכנית בינונית - 50 ש"ח לחודש
        SubscriptionPlan::create([
            'name' => 'בינונית',
            'name_en' => 'Standard',
            'price' => 50.00,
            'features' => [
                'כל מה שבבסיסית',
                'עד 10 סוגי אימונים',
                'עדיפות בחיפוש',
                'תג "מומלץ"',
            ],
            'max_training_types' => 10,
            'priority' => 2,
            'badge_text' => 'מומלץ',
            'is_active' => true,
        ]);

        // תכנית פרימיום - 100 ש"ח לחודש
        SubscriptionPlan::create([
            'name' => 'פרימיום',
            'name_en' => 'Premium',
            'price' => 100.00,
            'features' => [
                'כל מה שבבינונית',
                'מספר בלתי מוגבל של סוגי אימונים',
                'עדיפות מקסימלית בחיפוש',
                'תג "פרימיום"',
                'סטטיסטיקות מתקדמות',
            ],
            'max_training_types' => null, // בלתי מוגבל
            'priority' => 3,
            'badge_text' => 'פרימיום',
            'is_active' => true,
        ]);
    }
}
