<?php
/**
 * Script to run the plan_choice migration
 * Run: php run-plan-choice-migration.php
 * This script can be run directly on Railway production server
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Running Migration: Add plan_choice to Trainers Table ===\n\n";

try {
    // Check if column already exists
    $schema = \Illuminate\Support\Facades\Schema::getConnection()->getDoctrineSchemaManager();
    $table = $schema->listTableDetails('trainers');
    
    $hasPlanChoice = $table->hasColumn('plan_choice');
    
    if ($hasPlanChoice) {
        echo "✅ Column 'plan_choice' already exists in 'trainers' table.\n";
        echo "No migration needed.\n";
        exit(0);
    }
    
    echo "Column status:\n";
    echo "  - plan_choice: " . ($hasPlanChoice ? "EXISTS" : "MISSING") . "\n\n";
    
    // Run the migration
    echo "Running migration...\n";
    \Illuminate\Support\Facades\Artisan::call('migrate', [
        '--path' => 'database/migrations/2025_12_22_221144_add_plan_choice_to_trainers_table.php',
        '--force' => true,
    ]);
    
    echo \Illuminate\Support\Facades\Artisan::output();
    echo "\n✅ Migration completed successfully!\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

