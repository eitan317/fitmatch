<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if 'name' column exists
        if (Schema::hasColumn('trainers', 'name')) {
            // First, update any existing NULL values to empty string to avoid constraint issues
            DB::statement("UPDATE trainers SET name = '' WHERE name IS NULL");
            
            // Then make it nullable
            Schema::table('trainers', function (Blueprint $table) {
                $table->string('name')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // If we need to revert, we can make it not nullable again
        // But we'll leave it as is for now
    }
};
