<?php
/**
 * Quick script to check PHP upload configuration
 * Run: php check-upload-config.php
 */

echo "=== PHP Upload Configuration Check ===\n\n";

echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n\n";

echo "=== Storage Check ===\n\n";
$storagePath = __DIR__ . '/storage/app/public';
$trainersPath = $storagePath . '/trainers';

echo "Storage path exists: " . (is_dir($storagePath) ? 'YES' : 'NO') . "\n";
echo "Storage path writable: " . (is_writable($storagePath) ? 'YES' : 'NO') . "\n";
echo "Trainers directory exists: " . (is_dir($trainersPath) ? 'YES' : 'NO') . "\n";

if (is_dir($trainersPath)) {
    echo "Trainers directory writable: " . (is_writable($trainersPath) ? 'YES' : 'NO') . "\n";
    $files = array_diff(scandir($trainersPath), ['.', '..']);
    echo "Files in trainers directory: " . count($files) . "\n";
}

echo "\n=== Recommendations ===\n";
$uploadMax = ini_get('upload_max_filesize');
$postMax = ini_get('post_max_size');

if (str_replace(['M', 'K'], '', $uploadMax) < 20) {
    echo "⚠️  upload_max_filesize is less than 20MB. Consider increasing it.\n";
}

if (str_replace(['M', 'K'], '', $postMax) < 25) {
    echo "⚠️  post_max_size is less than 25MB. Should be larger than upload_max_filesize.\n";
}

echo "\nDone!\n";
