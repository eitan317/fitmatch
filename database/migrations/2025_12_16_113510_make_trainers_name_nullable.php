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
        if (Schema::hasColumn('trainers', 'name')) {
            // First, update any existing NULL values to empty string to avoid constraint issues
            try {
                DB::statement("UPDATE trainers SET name = '' WHERE name IS NULL");
            } catch (\Exception $e) {
                // Ignore if update fails
            }
            
            // Make the column nullable
            Schema::table('trainers', function (Blueprint $table) {
                $table->string('name')->nullable()->change();
            });
        } else {
            // If column doesn't exist, add it as nullable
            Schema::table('trainers', function (Blueprint $table) {
                $table->string('name')->nullable()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('trainers', 'name')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->string('name')->nullable(false)->change();
            });
        }
    }
};
