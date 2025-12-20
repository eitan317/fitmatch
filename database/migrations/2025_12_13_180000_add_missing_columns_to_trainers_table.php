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
        // Check if table exists
        if (!Schema::hasTable('trainers')) {
            // Table doesn't exist, the original migration will create it
            return;
        }

        // Table exists, add missing columns one by one
        // We'll add them without 'after' clause to avoid issues
        if (!Schema::hasColumn('trainers', 'owner_email')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->string('owner_email')->nullable();
            });
        }
        
        if (!Schema::hasColumn('trainers', 'full_name')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->string('full_name')->nullable();
            });
        }
        
        if (!Schema::hasColumn('trainers', 'age')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->integer('age')->nullable();
            });
        }
        
        if (!Schema::hasColumn('trainers', 'city')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->string('city')->nullable();
            });
        }
        
        if (!Schema::hasColumn('trainers', 'phone')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->string('phone')->nullable();
            });
        }
        
        if (!Schema::hasColumn('trainers', 'experience_years')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->integer('experience_years')->nullable();
            });
        }
        
        if (!Schema::hasColumn('trainers', 'main_specialization')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->string('main_specialization')->nullable();
            });
        }
        
        if (!Schema::hasColumn('trainers', 'price_per_session')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->decimal('price_per_session', 10, 2)->nullable();
            });
        }
        
        if (!Schema::hasColumn('trainers', 'training_types')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->json('training_types')->nullable();
            });
        }
        
        if (!Schema::hasColumn('trainers', 'instagram')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->string('instagram')->nullable();
            });
        }
        
        if (!Schema::hasColumn('trainers', 'tiktok')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->string('tiktok')->nullable();
            });
        }
        
        if (!Schema::hasColumn('trainers', 'profile_image_path')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->string('profile_image_path')->nullable();
            });
        }
        
        if (!Schema::hasColumn('trainers', 'status')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->enum('status', ['pending', 'approved'])->default('pending');
            });
        }

        // Set default status for existing records
        if (Schema::hasColumn('trainers', 'status')) {
            try {
                DB::statement("UPDATE trainers SET status = 'approved' WHERE status IS NULL OR status = ''");
            } catch (\Exception $e) {
                // Ignore if update fails
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop columns to preserve data
    }
};
