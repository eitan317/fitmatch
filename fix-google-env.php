<?php
/**
 * Script to fix Google OAuth configuration in .env file
 * Run: php fix-google-env.php
 */

$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    die("ERROR: .env file not found at: $envFile\n");
}

echo "Reading .env file...\n";
$content = file_get_contents($envFile);

// Check if Google credentials already exist
$hasClientId = strpos($content, 'GOOGLE_CLIENT_ID=') !== false;
$hasClientSecret = strpos($content, 'GOOGLE_CLIENT_SECRET=') !== false;
$hasRedirectUri = strpos($content, 'GOOGLE_REDIRECT_URI=') !== false;

echo "\nCurrent status:\n";
echo "  GOOGLE_CLIENT_ID: " . ($hasClientId ? "EXISTS" : "MISSING") . "\n";
echo "  GOOGLE_CLIENT_SECRET: " . ($hasClientSecret ? "EXISTS" : "MISSING") . "\n";
echo "  GOOGLE_REDIRECT_URI: " . ($hasRedirectUri ? "EXISTS" : "MISSING") . "\n";

// Remove existing Google entries
$lines = explode("\n", $content);
$newLines = [];
$foundGoogle = false;

foreach ($lines as $line) {
    $trimmed = trim($line);
    if (strpos($trimmed, 'GOOGLE_CLIENT_ID=') === 0 || 
        strpos($trimmed, 'GOOGLE_CLIENT_SECRET=') === 0 || 
        strpos($trimmed, 'GOOGLE_REDIRECT_URI=') === 0) {
        $foundGoogle = true;
        continue; // Skip existing entries
    }
    $newLines[] = $line;
}

// Add Google credentials at the end
if (!$foundGoogle || !$hasClientId || !$hasClientSecret || !$hasRedirectUri) {
    echo "\nAdding Google OAuth credentials...\n";
    $newLines[] = "";
    $newLines[] = "# Google OAuth Configuration";
    $newLines[] = "GOOGLE_CLIENT_ID=1022366565072-8a9nrblkv480k4hl4f3140e1dqjsjec1.apps.googleusercontent.com";
    $newLines[] = "GOOGLE_CLIENT_SECRET=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";
    $newLines[] = "GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback";
    
    file_put_contents($envFile, implode("\n", $newLines));
    echo "✅ Successfully updated .env file!\n";
} else {
    echo "\n✅ All Google OAuth credentials are already set.\n";
}

echo "\nNext steps:\n";
echo "1. Run: php artisan config:clear\n";
echo "2. Run: php artisan cache:clear\n";
echo "3. Restart your Laravel server\n";

