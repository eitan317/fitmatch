<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Trainer;

echo "=== Checking Trainer Images ===\n\n";

// Get all trainers with profile_image_path
$trainers = Trainer::whereNotNull('profile_image_path')->get();

echo "Total trainers with image path: " . $trainers->count() . "\n\n";

foreach ($trainers as $trainer) {
    echo "Trainer ID: {$trainer->id}\n";
    echo "Name: {$trainer->full_name}\n";
    echo "Image Path (DB): {$trainer->profile_image_path}\n";
    
    // Check multiple possible locations
    $possiblePaths = [
        storage_path('app/public/' . $trainer->profile_image_path),
        public_path('storage/' . $trainer->profile_image_path),
    ];
    
    $found = false;
    foreach ($possiblePaths as $fullPath) {
        if (file_exists($fullPath) && is_file($fullPath) && filesize($fullPath) > 0) {
            echo "✓ File found at: $fullPath\n";
            echo "  Size: " . filesize($fullPath) . " bytes\n";
            echo "  URL: " . url('/storage/' . $trainer->profile_image_path) . "\n";
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        echo "✗ File NOT FOUND in any location\n";
        echo "  Checked paths:\n";
        foreach ($possiblePaths as $path) {
            echo "    - $path (exists: " . (file_exists($path) ? 'yes' : 'no') . ")\n";
        }
    }
    
    echo "\n" . str_repeat('-', 50) . "\n\n";
}

// Check directory contents
$trainersDir = storage_path('app/public/trainers');
echo "=== Directory Contents ===\n";
echo "Directory: $trainersDir\n";
if (is_dir($trainersDir)) {
    $files = array_diff(scandir($trainersDir), ['.', '..']);
    echo "Files found: " . count($files) . "\n";
    if (count($files) > 0) {
        echo "First 10 files:\n";
        foreach (array_slice($files, 0, 10) as $file) {
            $filePath = $trainersDir . '/' . $file;
            echo "  - $file (" . filesize($filePath) . " bytes)\n";
        }
    }
} else {
    echo "Directory does not exist!\n";
}

echo "\nDone!\n";

