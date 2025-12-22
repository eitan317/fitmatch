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
        if (Schema::hasTable('trainers')) {
            if (!Schema::hasColumn('trainers', 'owner_email')) {
                try {
                    Schema::table('trainers', function (Blueprint $table) {
                        $table->string('owner_email')->nullable()->after('id');
                    });
                } catch (\Exception $e) {
                    // If 'after' clause fails, try without it
                    Schema::table('trainers', function (Blueprint $table) {
                        $table->string('owner_email')->nullable();
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('trainers', 'owner_email')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->dropColumn('owner_email');
            });
        }
    }
};

