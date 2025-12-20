<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('trainers', 'specialty')) {
            // First, update any existing NULL values to empty string to avoid constraint issues
            try {
                DB::statement("UPDATE trainers SET specialty = '' WHERE specialty IS NULL");
            } catch (\Exception $e) {
                // Ignore if update fails
            }
            
            // Make the column nullable
            Schema::table('trainers', function (Blueprint $table) {
                $table->string('specialty')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('trainers', 'specialty')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->string('specialty')->nullable(false)->change();
            });
        }
    }
};
