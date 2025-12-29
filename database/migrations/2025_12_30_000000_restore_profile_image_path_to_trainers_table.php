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
        Schema::table('trainers', function (Blueprint $table) {
            if (!Schema::hasColumn('trainers', 'profile_image_path')) {
                $table->string('profile_image_path')->nullable()->after('bio');
            }
        });
        
        // Drop trainer_images table
        Schema::dropIfExists('trainer_images');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            if (Schema::hasColumn('trainers', 'profile_image_path')) {
                $table->dropColumn('profile_image_path');
            }
        });
    }
};

