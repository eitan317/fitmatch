<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "Checking trainers table...\n";

// Check if column exists
$hasColumn = Schema::hasColumn('trainers', 'profile_image_path');
echo "Has profile_image_path column: " . ($hasColumn ? 'YES' : 'NO') . "\n";

if (!$hasColumn) {
    echo "Adding profile_image_path column...\n";
    try {
        Schema::table('trainers', function ($table) {
            $table->string('profile_image_path')->nullable()->after('bio');
        });
        echo "✅ Column added successfully!\n";
    } catch (\Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "✅ Column already exists!\n";
}

// Check if trainer_images table exists
$trainerImagesExists = Schema::hasTable('trainer_images');
echo "Has trainer_images table: " . ($trainerImagesExists ? 'YES' : 'NO') . "\n";

if ($trainerImagesExists) {
    echo "Dropping trainer_images table...\n";
    try {
        Schema::dropIfExists('trainer_images');
        echo "✅ Table dropped successfully!\n";
    } catch (\Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "✅ Table doesn't exist (already dropped)!\n";
}

echo "\nDone!\n";

