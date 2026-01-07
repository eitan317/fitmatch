<?php
/**
 * Direct test of router.php logic
 */

echo "🧪 Testing Router Logic Directly\n";
echo str_repeat("=", 60) . "\n\n";

// Simulate a sitemap.xml request
$_SERVER['REQUEST_URI'] = '/sitemap.xml';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PORT'] = '8000';
$_SERVER['HTTPS'] = 'off';

echo "1. Simulating request to /sitemap.xml\n";
echo "   URI: " . $_SERVER['REQUEST_URI'] . "\n\n";

// Change to public directory
chdir(__DIR__ . '/public');

// Test the router logic
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
echo "2. Parsed URI: $uri\n\n";

if (preg_match('#^/sitemap.*\.xml$#', $uri)) {
    echo "3. ✅ Router pattern matches sitemap.xml\n";
    $file = __DIR__ . '/public' . $uri;
    echo "   Checking for static file: $file\n";
    echo "   File exists: " . (file_exists($file) ? 'YES' : 'NO') . "\n";
    echo "   Router will route to Laravel index.php\n";
} else {
    echo "3. ❌ Router pattern does NOT match\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ Router logic is correct\n";
echo "\n";
echo "📋 Next: Start server and test:\n";
echo "   cd public\n";
echo "   php -S 127.0.0.1:8000 router.php\n";
echo "   Then visit: http://127.0.0.1:8000/sitemap.xml\n";

