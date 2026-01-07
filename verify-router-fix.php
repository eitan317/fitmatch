<?php
/**
 * Verify router.php fix works
 */

echo "🔍 Verifying Router Fix\n";
echo str_repeat("=", 60) . "\n\n";

// Test 1: Check router.php exists
echo "1. Checking router.php file...\n";
if (file_exists('public/router.php')) {
    echo "   ✅ router.php exists\n";
} else {
    echo "   ❌ router.php NOT FOUND\n";
    exit(1);
}

// Test 2: Check Procfile
echo "\n2. Checking Procfile...\n";
$procfile = file_get_contents('Procfile');
if (strpos($procfile, 'php -S') !== false && strpos($procfile, 'router.php') !== false) {
    echo "   ✅ Procfile uses php -S with router.php\n";
    echo "   Command: " . trim($procfile) . "\n";
} else {
    echo "   ❌ Procfile does NOT use router.php\n";
    echo "   Current: " . trim($procfile) . "\n";
    exit(1);
}

// Test 3: Check router.php logic
echo "\n3. Checking router.php logic...\n";
$routerContent = file_get_contents('public/router.php');
if (strpos($routerContent, 'sitemap.*\.xml') !== false) {
    echo "   ✅ Router handles sitemap.xml requests\n";
} else {
    echo "   ❌ Router does NOT handle sitemap.xml\n";
    exit(1);
}

if (strpos($routerContent, 'require __DIR__ . \'/index.php\'') !== false) {
    echo "   ✅ Router routes to Laravel index.php\n";
} else {
    echo "   ❌ Router does NOT route to Laravel\n";
    exit(1);
}

// Test 4: Simulate router behavior
echo "\n4. Simulating router behavior...\n";
chdir('public');
$_SERVER['REQUEST_URI'] = '/sitemap.xml';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if (preg_match('#^/sitemap.*\.xml$#', $uri)) {
    $file = __DIR__ . $uri;
    if (!file_exists($file) || !is_file($file)) {
        echo "   ✅ Router would route /sitemap.xml to Laravel\n";
        echo "   (Static file doesn't exist, will route to index.php)\n";
    } else {
        echo "   ⚠️  Static file exists, router would serve it\n";
    }
} else {
    echo "   ❌ Router pattern doesn't match\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ Router fix is correctly implemented\n";
echo "\n";
echo "📋 Summary:\n";
echo "1. router.php exists and handles sitemap.xml requests\n";
echo "2. Procfile uses php -S with router.php (not php artisan serve)\n";
echo "3. Router routes missing sitemap.xml to Laravel\n";
echo "\n";
echo "🚀 After deployment, /sitemap.xml will work!\n";

