<?php
/**
 * Script to update .env file with new domain settings
 */

$envFile = __DIR__ . DIRECTORY_SEPARATOR . '.env';

if (!file_exists($envFile)) {
    die("ERROR: .env file not found at: $envFile\n");
}

echo "Reading .env file...\n";
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$newLines = [];
$appUrlFound = false;
$googleRedirectFound = false;
$sessionDomainFound = false;

foreach ($lines as $line) {
    $trimmed = trim($line);
    
    // Skip comments
    if (strpos($trimmed, '#') === 0) {
        $newLines[] = $line;
        continue;
    }
    
    // Update APP_URL
    if (preg_match('/^APP_URL\s*=/i', $trimmed)) {
        $newLines[] = 'APP_URL=https://www.fitmatch.org.il';
        $appUrlFound = true;
        echo "Updated APP_URL\n";
        continue;
    }
    
    // Update GOOGLE_REDIRECT_URI
    if (preg_match('/^GOOGLE_REDIRECT_URI\s*=/i', $trimmed)) {
        $newLines[] = 'GOOGLE_REDIRECT_URI=https://www.fitmatch.org.il/auth/google/callback';
        $googleRedirectFound = true;
        echo "Updated GOOGLE_REDIRECT_URI\n";
        continue;
    }
    
    // Update SESSION_DOMAIN
    if (preg_match('/^SESSION_DOMAIN\s*=/i', $trimmed)) {
        $newLines[] = 'SESSION_DOMAIN=null';
        $sessionDomainFound = true;
        echo "Updated SESSION_DOMAIN\n";
        continue;
    }
    
    $newLines[] = $line;
}

// Add missing entries
if (!$appUrlFound) {
    $newLines[] = 'APP_URL=https://www.fitmatch.org.il';
    echo "Added APP_URL\n";
}

if (!$googleRedirectFound) {
    $newLines[] = 'GOOGLE_REDIRECT_URI=https://www.fitmatch.org.il/auth/google/callback';
    echo "Added GOOGLE_REDIRECT_URI\n";
}

if (!$sessionDomainFound) {
    $newLines[] = 'SESSION_DOMAIN=null';
    echo "Added SESSION_DOMAIN\n";
}

// Write back to file
file_put_contents($envFile, implode("\n", $newLines) . "\n");

echo "\n✅ Successfully updated .env file!\n";
echo "\nUpdated values:\n";
echo "  APP_URL=https://www.fitmatch.org.il\n";
echo "  GOOGLE_REDIRECT_URI=https://www.fitmatch.org.il/auth/google/callback\n";
echo "  SESSION_DOMAIN=null\n";
echo "\n✅ Done! Now run: php artisan config:clear && php artisan cache:clear\n";

