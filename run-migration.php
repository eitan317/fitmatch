<?php
/**
 * Script to run the Google fields migration
 * Run: php run-migration.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Running Migration: Add Google Fields to Users Table ===\n\n";

try {
    // Check if columns already exist
    $schema = \Illuminate\Support\Facades\Schema::getConnection()->getDoctrineSchemaManager();
    $table = $schema->listTableDetails('users');
    
    $hasGoogleId = $table->hasColumn('google_id');
    $hasAvatar = $table->hasColumn('avatar');
    
    if ($hasGoogleId && $hasAvatar) {
        echo "✅ Columns 'google_id' and 'avatar' already exist in 'users' table.\n";
        echo "No migration needed.\n";
        exit(0);
    }
    
    echo "Columns status:\n";
    echo "  - google_id: " . ($hasGoogleId ? "EXISTS" : "MISSING") . "\n";
    echo "  - avatar: " . ($hasAvatar ? "EXISTS" : "MISSING") . "\n\n";
    
    // Run the migration
    echo "Running migration...\n";
    \Illuminate\Support\Facades\Artisan::call('migrate', [
        '--path' => 'database/migrations/2025_12_13_190000_add_google_fields_to_users_table.php',
        '--force' => true,
    ]);
    
    echo \Illuminate\Support\Facades\Artisan::output();
    echo "\n✅ Migration completed successfully!\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nTrying alternative method...\n";
    
    // Alternative: Add columns directly using DB
    try {
        echo "Adding columns directly to database...\n";
        
        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'google_id')) {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE `users` ADD COLUMN `google_id` VARCHAR(255) NULL UNIQUE AFTER `email`');
            echo "✅ Added 'google_id' column\n";
        } else {
            echo "ℹ️  'google_id' column already exists\n";
        }
        
        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'avatar')) {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE `users` ADD COLUMN `avatar` VARCHAR(255) NULL AFTER `google_id`');
            echo "✅ Added 'avatar' column\n";
        } else {
            echo "ℹ️  'avatar' column already exists\n";
        }
        
        echo "\n✅ Columns added successfully!\n";
    } catch (\Exception $e2) {
        echo "❌ Alternative method also failed: " . $e2->getMessage() . "\n";
        echo "\nPlease run manually:\n";
        echo "  php artisan migrate --path=database/migrations/2025_12_13_190000_add_google_fields_to_users_table.php\n";
        exit(1);
    }
}

