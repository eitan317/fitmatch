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
       Schema::create('trainers', function (Blueprint $table) {
    $table->id();
            $table->string('owner_email')->nullable();
            $table->string('full_name');
            $table->integer('age')->nullable();
            $table->string('city');
            $table->string('phone')->nullable();
            $table->integer('experience_years')->nullable();
            $table->string('main_specialization')->nullable();
            $table->decimal('price_per_session', 10, 2)->nullable();
            $table->json('training_types')->nullable();
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();
    $table->text('bio')->nullable();
            $table->string('profile_image_path')->nullable();
            $table->enum('status', ['pending', 'approved'])->default('pending');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};
