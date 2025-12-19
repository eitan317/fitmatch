<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // שם התכנית בעברית
            $table->string('name_en'); // שם באנגלית
            $table->decimal('price', 10, 2); // מחיר חודשי
            $table->json('features')->nullable(); // רשימת תכונות
            $table->integer('max_training_types')->nullable(); // מקסימום סוגי אימונים (null = בלתי מוגבל)
            $table->integer('priority')->default(1); // עדיפות בחיפוש (1-3, 3 = הכי גבוה)
            $table->string('badge_text')->nullable(); // טקסט התג (null = אין תג)
            $table->boolean('is_active')->default(true); // האם התכנית פעילה
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
