<?php

/**
 * Quick script to check mail configuration
 * Run: php check-mail-config.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Mail Configuration Check ===\n\n";

echo "Mail Driver: " . config('mail.default') . "\n";
echo "Mail Host: " . config('mail.mailers.smtp.host') . "\n";
echo "Mail Port: " . config('mail.mailers.smtp.port') . "\n";
echo "Mail Username: " . (config('mail.mailers.smtp.username') ?: 'NOT SET') . "\n";
echo "Mail Password: " . (config('mail.mailers.smtp.password') ? 'SET' : 'NOT SET') . "\n";
echo "Mail Encryption: " . (config('mail.mailers.smtp.encryption') ?: 'NOT SET') . "\n";
echo "Mail From Address: " . config('mail.from.address') . "\n";
echo "Mail From Name: " . config('mail.from.name') . "\n";

echo "\n=== Status ===\n";

if (config('mail.default') === 'log') {
    echo "⚠️  WARNING: Mail driver is set to 'log' - emails will be saved to log file, not sent!\n";
    echo "   To fix: Set MAIL_MAILER=smtp in .env file\n";
} elseif (config('mail.default') === 'smtp') {
    if (!config('mail.mailers.smtp.username') || !config('mail.mailers.smtp.password')) {
        echo "⚠️  WARNING: SMTP username or password not set!\n";
        echo "   To fix: Set MAIL_USERNAME and MAIL_PASSWORD in .env file\n";
    } else {
        echo "✅ Mail configuration looks good!\n";
    }
} else {
    echo "ℹ️  Mail driver: " . config('mail.default') . "\n";
}

echo "\n=== Recent Email Logs ===\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    $recentLines = array_slice($lines, -20);
    foreach ($recentLines as $line) {
        if (stripos($line, 'verification') !== false || stripos($line, 'email') !== false || stripos($line, 'mail') !== false) {
            echo $line;
        }
    }
} else {
    echo "Log file not found.\n";
}

echo "\n";

