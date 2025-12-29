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
        Schema::create('trainer_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('trainers')->onDelete('cascade');
            $table->string('image_path'); // נתיב התמונה
            $table->string('image_type')->default('profile'); // profile, gallery
            $table->integer('sort_order')->default(0); // לסדר תמונות
            $table->boolean('is_primary')->default(false); // תמונת פרופיל ראשית
            $table->timestamps();
            
            $table->index(['trainer_id', 'is_primary']);
            $table->index(['trainer_id', 'image_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainer_images');
    }
};

