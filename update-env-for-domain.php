<?php
/**
 * Script to update .env file with new domain settings
 */

$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    die("ERROR: .env file not found at: $envFile\n");
}

echo "Reading .env file...\n";
$content = file_get_contents($envFile);

// Define the new values
$newAppUrl = 'APP_URL=https://www.fitmatch.org.il';
$newGoogleRedirect = 'GOOGLE_REDIRECT_URI=https://www.fitmatch.org.il/auth/google/callback';
$newSessionDomain = 'SESSION_DOMAIN=null';

// Read lines
$lines = explode("\n", $content);
$newLines = [];
$appUrlUpdated = false;
$googleRedirectUpdated = false;
$sessionDomainUpdated = false;

foreach ($lines as $line) {
    $trimmed = trim($line);
    
    // Update or skip existing entries
    if (strpos($trimmed, 'APP_URL=') === 0) {
        $newLines[] = $newAppUrl;
        $appUrlUpdated = true;
        continue;
    }
    
    if (strpos($trimmed, 'GOOGLE_REDIRECT_URI=') === 0) {
        $newLines[] = $newGoogleRedirect;
        $googleRedirectUpdated = true;
        continue;
    }
    
    if (strpos($trimmed, 'SESSION_DOMAIN=') === 0) {
        $newLines[] = $newSessionDomain;
        $sessionDomainUpdated = true;
        continue;
    }
    
    $newLines[] = $line;
}

// Add missing entries at the end if they weren't found
if (!$appUrlUpdated) {
    echo "Adding APP_URL...\n";
    $newLines[] = $newAppUrl;
}

if (!$googleRedirectUpdated) {
    echo "Adding GOOGLE_REDIRECT_URI...\n";
    $newLines[] = $newGoogleRedirect;
}

if (!$sessionDomainUpdated) {
    echo "Adding SESSION_DOMAIN...\n";
    $newLines[] = $newSessionDomain;
}

// Write back to file
file_put_contents($envFile, implode("\n", $newLines));

echo "\n✅ Successfully updated .env file!\n";
echo "\nUpdated values:\n";
echo "  $newAppUrl\n";
echo "  $newGoogleRedirect\n";
echo "  $newSessionDomain\n";
echo "\nNext step: Run 'php artisan config:clear' and 'php artisan cache:clear'\n";

