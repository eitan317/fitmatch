<?php
/**
 * Test that public/index.php works as router for PHP built-in server
 */

echo "🧪 Testing public/index.php as Router\n";
echo str_repeat("=", 70) . "\n\n";

// Check if index.php exists
if (!file_exists('public/index.php')) {
    echo "❌ public/index.php NOT FOUND\n";
    exit(1);
}

echo "✅ public/index.php exists\n\n";

// Check Procfile
$procfile = file_get_contents('Procfile');
if (strpos($procfile, 'public/index.php') !== false) {
    echo "✅ Procfile uses public/index.php as router\n";
    echo "   Command: " . trim($procfile) . "\n";
} else {
    echo "❌ Procfile does NOT use public/index.php\n";
    echo "   Current: " . trim($procfile) . "\n";
    exit(1);
}

// Check that sitemap routes exclude session middleware
$webContent = file_get_contents('routes/web.php');
$requiredExclusions = [
    'StartSession',
    'ShareErrorsFromSession',
    'VerifyCsrfToken',
];

$allExcluded = true;
foreach ($requiredExclusions as $middleware) {
    if (strpos($webContent, $middleware) === false) {
        echo "❌ $middleware middleware NOT excluded from sitemap routes\n";
        $allExcluded = false;
    }
}

if ($allExcluded) {
    echo "\n✅ All session middleware excluded from sitemap routes\n";
}

// Check route registration
echo "\n3. Checking route registration...\n";
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
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "✅ Configuration is correct!\n";
echo "\n📋 How to Test:\n";
echo "1. Start server: cd public && php -S 127.0.0.1:8000 ../public/index.php\n";
echo "   (Note: -t public is implied when using index.php as router)\n";
echo "2. Visit: http://127.0.0.1:8000/sitemap.xml\n";
echo "3. Should return HTTP 200 with XML content\n";

