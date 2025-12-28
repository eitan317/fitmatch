<?php
/**
 * Create trainers directory if it doesn't exist
 * Run: php create-trainers-directory.php
 */

$trainersPath = __DIR__ . '/storage/app/public/trainers';

if (!is_dir($trainersPath)) {
    if (mkdir($trainersPath, 0755, true)) {
        echo "✅ Created trainers directory: $trainersPath\n";
        echo "✅ Directory is writable: " . (is_writable($trainersPath) ? 'YES' : 'NO') . "\n";
    } else {
        echo "❌ Failed to create trainers directory\n";
        echo "Please create it manually: mkdir $trainersPath\n";
    }
} else {
    echo "✅ Trainers directory already exists: $trainersPath\n";
    echo "✅ Directory is writable: " . (is_writable($trainersPath) ? 'YES' : 'NO') . "\n";
}

// Also create .gitkeep to ensure directory is tracked in git
$gitkeepPath = $trainersPath . '/.gitkeep';
if (!file_exists($gitkeepPath)) {
    file_put_contents($gitkeepPath, '');
    echo "✅ Created .gitkeep file\n";
}

echo "\nDone!\n";
