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
        if (!Schema::hasTable('trainers')) {
            return;
        }

        // Change status column from enum to string to support new statuses
        if (Schema::hasColumn('trainers', 'status')) {
            // Convert enum to string
            DB::statement("ALTER TABLE `trainers` MODIFY COLUMN `status` VARCHAR(255) NOT NULL DEFAULT 'pending'");
        } else {
            // If status column doesn't exist, create it
            Schema::table('trainers', function (Blueprint $table) {
                $table->string('status')->default('pending')->index();
            });
        }

        // Add new columns
        if (!Schema::hasColumn('trainers', 'trial_started_at')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->timestamp('trial_started_at')->nullable()->after('status');
            });
        }

        if (!Schema::hasColumn('trainers', 'trial_ends_at')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->timestamp('trial_ends_at')->nullable()->after('trial_started_at');
            });
        }

        if (!Schema::hasColumn('trainers', 'last_payment_at')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->timestamp('last_payment_at')->nullable()->after('trial_ends_at');
            });
        }

        if (!Schema::hasColumn('trainers', 'approved_by_admin')) {
            Schema::table('trainers', function (Blueprint $table) {
                $table->boolean('approved_by_admin')->default(false)->after('last_payment_at');
            });
        }

        // Migrate existing trainers
        // Convert 'approved' status to 'active' with approved_by_admin=true and last_payment_at=now()
        DB::table('trainers')
            ->where('status', 'approved')
            ->update([
                'status' => 'active',
                'approved_by_admin' => true,
                'last_payment_at' => now(),
            ]);

        // Convert 'pending' status to 'trial' if they don't have trial dates set
        // (only for new registrations, existing pending might stay as pending)
        // We'll leave pending as is for now, new registrations will be trial
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('trainers')) {
            return;
        }

        // Convert back 'active' to 'approved' for rollback
        DB::table('trainers')
            ->where('status', 'active')
            ->update(['status' => 'approved']);

        // Drop new columns
        Schema::table('trainers', function (Blueprint $table) {
            if (Schema::hasColumn('trainers', 'approved_by_admin')) {
                $table->dropColumn('approved_by_admin');
            }
            if (Schema::hasColumn('trainers', 'last_payment_at')) {
                $table->dropColumn('last_payment_at');
            }
            if (Schema::hasColumn('trainers', 'trial_ends_at')) {
                $table->dropColumn('trial_ends_at');
            }
            if (Schema::hasColumn('trainers', 'trial_started_at')) {
                $table->dropColumn('trial_started_at');
            }
        });

        // Convert status back to enum (this might fail if there are new status values)
        // We'll leave it as string for safety
    }
};

