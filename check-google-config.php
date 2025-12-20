<?php
/**
 * Temporary script to check Google OAuth configuration
 * Run: php check-google-config.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Google OAuth Configuration Check ===\n\n";

// Check .env file
$envFile = __DIR__ . '/.env';
echo "1. Checking .env file: " . ($envFile ? "EXISTS" : "NOT FOUND") . "\n";
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    echo "   - GOOGLE_CLIENT_ID: " . (strpos($envContent, 'GOOGLE_CLIENT_ID') !== false ? "FOUND" : "NOT FOUND") . "\n";
    echo "   - GOOGLE_CLIENT_SECRET: " . (strpos($envContent, 'GOOGLE_CLIENT_SECRET') !== false ? "FOUND" : "NOT FOUND") . "\n";
    echo "   - GOOGLE_REDIRECT_URI: " . (strpos($envContent, 'GOOGLE_REDIRECT_URI') !== false ? "FOUND" : "NOT FOUND") . "\n";
}

// Check config
echo "\n2. Checking config/services.php:\n";
$clientId = config('services.google.client_id');
$clientSecret = config('services.google.client_secret');
$redirect = config('services.google.redirect');

echo "   - client_id: " . ($clientId ? "SET (" . substr($clientId, 0, 20) . "...)" : "NOT SET") . "\n";
echo "   - client_secret: " . ($clientSecret ? "SET (" . substr($clientSecret, 0, 10) . "...)" : "NOT SET") . "\n";
echo "   - redirect: " . ($redirect ? "SET ($redirect)" : "NOT SET") . "\n";

// Check environment variables directly
echo "\n3. Checking environment variables directly:\n";
echo "   - GOOGLE_CLIENT_ID: " . (env('GOOGLE_CLIENT_ID') ? "SET" : "NOT SET") . "\n";
echo "   - GOOGLE_CLIENT_SECRET: " . (env('GOOGLE_CLIENT_SECRET') ? "SET" : "NOT SET") . "\n";
echo "   - GOOGLE_REDIRECT_URI: " . (env('GOOGLE_REDIRECT_URI') ? "SET" : "NOT SET") . "\n";

echo "\n=== Recommendations ===\n";
if (!$clientId) {
    echo "❌ GOOGLE_CLIENT_ID is missing!\n";
    echo "   Add to .env: GOOGLE_CLIENT_ID=1022366565072-8a9nrblkv480k4hl4f3140e1dqjsjec1.apps.googleusercontent.com\n";
    echo "   Then run: php artisan config:clear\n";
} else {
    echo "✅ GOOGLE_CLIENT_ID is set\n";
}

if (!$clientSecret) {
    echo "❌ GOOGLE_CLIENT_SECRET is missing!\n";
    echo "   Add to .env: GOOGLE_CLIENT_SECRET=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\n";
    echo "   Then run: php artisan config:clear\n";
} else {
    echo "✅ GOOGLE_CLIENT_SECRET is set\n";
}

if (!$redirect) {
    echo "❌ GOOGLE_REDIRECT_URI is missing!\n";
    echo "   Add to .env: GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback\n";
    echo "   Then run: php artisan config:clear\n";
} else {
    echo "✅ GOOGLE_REDIRECT_URI is set\n";
}

