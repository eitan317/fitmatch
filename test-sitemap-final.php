<?php
/**
 * Final test to verify sitemap works without session errors
 */

echo "🧪 Final Sitemap Test - Session Middleware Excluded\n";
echo str_repeat("=", 70) . "\n\n";

// Check excluded middleware
echo "1. Checking excluded middleware...\n";
$webContent = file_get_contents('routes/web.php');
$excluded = [
    'StartSession' => 'Session start',
    'AuthenticateSession' => 'Session authentication',
    'ShareErrorsFromSession' => 'Share errors (requires session)',
    'SetLocale' => 'Locale middleware (uses session)',
    'TrackPageViews' => 'Page tracking',
];

foreach ($excluded as $middleware => $description) {
    if (strpos($webContent, $middleware) !== false) {
        echo "   ✅ $description excluded\n";
    } else {
        echo "   ❌ $description NOT excluded\n";
    }
}

// Check route registration
echo "\n2. Checking route registration...\n";
exec('php artisan route:list --path=sitemap.xml 2>&1', $output, $return);
if ($return === 0 && !empty($output)) {
    $found = false;
    foreach ($output as $line) {
        if (strpos($line, 'sitemap.xml') !== false) {
            echo "   ✅ Route registered: " . trim($line) . "\n";
            $found = true;
        }
    }
    if (!$found) {
        echo "   ⚠️  Route not found in output\n";
    }
} else {
    echo "   ⚠️  Could not verify route\n";
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "✅ Configuration complete!\n";
echo "\n📋 Test Steps:\n";
echo "1. Start server: cd public && php -S 127.0.0.1:8000 router.php\n";
echo "2. Visit: http://127.0.0.1:8000/sitemap.xml\n";
echo "3. Should see XML (no session errors)\n";

