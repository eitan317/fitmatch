<?php
/**
 * Script to setup Mail configuration in .env file
 */

$envFile = __DIR__ . '/.env';

// Check if .env exists, if not create from template
if (!file_exists($envFile)) {
    if (file_exists(__DIR__ . '/env.example.template')) {
        copy(__DIR__ . '/env.example.template', $envFile);
        echo "Created .env file from template\n";
    } else {
        die("ERROR: .env file not found and no template available\n");
    }
}

echo "Reading .env file...\n";
$content = file_get_contents($envFile);
$lines = explode("\n", $content);
$newLines = [];
$mailConfigUpdated = false;
$mailSectionFound = false;

foreach ($lines as $line) {
    $trimmed = trim($line);
    
    // Check if we're in Mail section
    if (stripos($trimmed, '# Mail') !== false) {
        $mailSectionFound = true;
        $newLines[] = $line;
        continue;
    }
    
    // Update existing Mail settings
    if (preg_match('/^MAIL_MAILER\s*=/i', $trimmed)) {
        $newLines[] = 'MAIL_MAILER=log';
        $mailConfigUpdated = true;
        continue;
    }
    
    if (preg_match('/^MAIL_HOST\s*=/i', $trimmed)) {
        $newLines[] = 'MAIL_HOST=smtp.gmail.com';
        $mailConfigUpdated = true;
        continue;
    }
    
    if (preg_match('/^MAIL_PORT\s*=/i', $trimmed)) {
        $newLines[] = 'MAIL_PORT=587';
        $mailConfigUpdated = true;
        continue;
    }
    
    if (preg_match('/^MAIL_USERNAME\s*=/i', $trimmed)) {
        $newLines[] = 'MAIL_USERNAME=fitmatchcoil@gmail.com';
        $mailConfigUpdated = true;
        continue;
    }
    
    if (preg_match('/^MAIL_PASSWORD\s*=/i', $trimmed)) {
        $newLines[] = 'MAIL_PASSWORD=';
        $newLines[] = '# TODO: Add Gmail App Password here (get from https://myaccount.google.com/apppasswords)';
        $mailConfigUpdated = true;
        continue;
    }
    
    if (preg_match('/^MAIL_ENCRYPTION\s*=/i', $trimmed)) {
        $newLines[] = 'MAIL_ENCRYPTION=tls';
        $mailConfigUpdated = true;
        continue;
    }
    
    if (preg_match('/^MAIL_FROM_ADDRESS\s*=/i', $trimmed)) {
        $newLines[] = 'MAIL_FROM_ADDRESS=fitmatchcoil@gmail.com';
        $mailConfigUpdated = true;
        continue;
    }
    
    if (preg_match('/^MAIL_FROM_NAME\s*=/i', $trimmed)) {
        $newLines[] = 'MAIL_FROM_NAME="FitMatch"';
        $mailConfigUpdated = true;
        continue;
    }
    
    $newLines[] = $line;
}

// If Mail section exists but config wasn't updated, add it after the section
if ($mailSectionFound && !$mailConfigUpdated) {
    // Find where to insert
    $insertIndex = -1;
    foreach ($newLines as $i => $line) {
        if (stripos($line, '# Mail') !== false) {
            $insertIndex = $i + 1;
            break;
        }
    }
    
    if ($insertIndex > 0) {
        array_splice($newLines, $insertIndex, 0, [
            'MAIL_MAILER=log',
            'MAIL_HOST=smtp.gmail.com',
            'MAIL_PORT=587',
            'MAIL_USERNAME=fitmatchcoil@gmail.com',
            'MAIL_PASSWORD=',
            '# TODO: Add Gmail App Password here',
            'MAIL_ENCRYPTION=tls',
            'MAIL_FROM_ADDRESS=fitmatchcoil@gmail.com',
            'MAIL_FROM_NAME="FitMatch"',
        ]);
    }
}

// If no Mail section found, add it at the end
if (!$mailSectionFound) {
    $newLines[] = '';
    $newLines[] = '# Mail Configuration';
    $newLines[] = 'MAIL_MAILER=log';
    $newLines[] = 'MAIL_HOST=smtp.gmail.com';
    $newLines[] = 'MAIL_PORT=587';
    $newLines[] = 'MAIL_USERNAME=fitmatchcoil@gmail.com';
    $newLines[] = 'MAIL_PASSWORD=';
    $newLines[] = '# TODO: Add Gmail App Password here (get from https://myaccount.google.com/apppasswords)';
    $newLines[] = 'MAIL_ENCRYPTION=tls';
    $newLines[] = 'MAIL_FROM_ADDRESS=fitmatchcoil@gmail.com';
    $newLines[] = 'MAIL_FROM_NAME="FitMatch"';
}

// Write back
file_put_contents($envFile, implode("\n", $newLines));

echo "\n✅ Successfully updated .env file with Mail configuration!\n";
echo "\n⚠️  IMPORTANT: Mail driver is set to 'log' for testing.\n";
echo "   This means emails will be saved to storage/logs/laravel.log\n";
echo "   To send real emails, change MAIL_MAILER=smtp and add MAIL_PASSWORD\n";
echo "\nNext steps:\n";
echo "1. Get Gmail App Password from: https://myaccount.google.com/apppasswords\n";
echo "2. Add it to MAIL_PASSWORD in .env\n";
echo "3. Change MAIL_MAILER from 'log' to 'smtp'\n";
echo "4. Run: php artisan config:clear\n";
echo "5. Run: php artisan cache:clear\n";

