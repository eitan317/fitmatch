<?php
/**
 * Deep Sitemap Diagnostics
 * This will check everything and show exactly what's wrong
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Deep Sitemap Diagnostics\n";
echo str_repeat("=", 70) . "\n\n";

// Test 1: Check if route exists
echo "1. Checking route registration...\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $sitemapRoute = null;
    foreach ($routes as $route) {
        if ($route->uri() === 'sitemap.xml' || $route->getName() === 'sitemap.main') {
            $sitemapRoute = $route;
            break;
        }
    }
    
    if ($sitemapRoute) {
        echo "   ✅ Route found: " . $sitemapRoute->uri() . "\n";
        echo "   ✅ Route name: " . $sitemapRoute->getName() . "\n";
        echo "   ✅ Controller: " . $sitemapRoute->getActionName() . "\n";
    } else {
        echo "   ❌ Route NOT FOUND!\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error checking routes: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 2: Check if static file exists
echo "2. Checking for static sitemap.xml file...\n";
$staticFile = public_path('sitemap.xml');
if (file_exists($staticFile)) {
    echo "   ⚠️  WARNING: Static file exists at: $staticFile\n";
    echo "   File size: " . filesize($staticFile) . " bytes\n";
    echo "   Last modified: " . date('Y-m-d H:i:s', filemtime($staticFile)) . "\n";
    echo "   This will block the route!\n";
} else {
    echo "   ✅ No static sitemap.xml file found\n";
}
echo "\n";

// Test 3: Test controller directly
echo "3. Testing SitemapController directly...\n";
try {
    $controller = $app->make(\App\Http\Controllers\SitemapController::class);
    echo "   ✅ Controller instantiated successfully\n";
    
    $response = $controller->main();
    echo "   ✅ Controller->main() executed\n";
    echo "   Status Code: " . $response->getStatusCode() . "\n";
    echo "   Content Length: " . strlen($response->getContent()) . " bytes\n";
    echo "   Content-Type: " . $response->headers->get('Content-Type') . "\n";
    
    // Check content
    $content = $response->getContent();
    if (strpos($content, '<?xml') === 0) {
        echo "   ✅ Valid XML content\n";
    } else {
        echo "   ❌ Invalid XML content\n";
        echo "   First 200 chars: " . substr($content, 0, 200) . "\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n";
    echo "   " . str_replace("\n", "\n   ", $e->getTraceAsString()) . "\n";
}
echo "\n";

// Test 4: Simulate HTTP request
echo "4. Simulating HTTP request to /sitemap.xml...\n";
try {
    $request = \Illuminate\Http\Request::create('/sitemap.xml', 'GET');
    $response = $app->handle($request);
    
    echo "   Status Code: " . $response->getStatusCode() . "\n";
    echo "   Content-Type: " . $response->headers->get('Content-Type') . "\n";
    
    if ($response->getStatusCode() === 200) {
        echo "   ✅ Route works via HTTP simulation\n";
    } else {
        echo "   ❌ Route returned status " . $response->getStatusCode() . "\n";
        echo "   Content: " . substr($response->getContent(), 0, 500) . "\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n";
    echo "   " . str_replace("\n", "\n   ", $e->getTraceAsString()) . "\n";
}
echo "\n";

// Test 5: Check APP_URL configuration
echo "5. Checking configuration...\n";
echo "   APP_URL: " . config('app.url') . "\n";
echo "   APP_ENV: " . config('app.env') . "\n";
echo "   APP_DEBUG: " . (config('app.debug') ? 'true' : 'false') . "\n";
echo "\n";

// Test 6: Check if sitemap.php exists
echo "6. Checking for sitemap.php fallback...\n";
$sitemapPhp = public_path('sitemap.php');
if (file_exists($sitemapPhp)) {
    echo "   ✅ sitemap.php exists (can be used as fallback)\n";
} else {
    echo "   ⚠️  sitemap.php not found\n";
}
echo "\n";

// Test 7: Check Procfile
echo "7. Checking Procfile...\n";
$procfile = base_path('Procfile');
if (file_exists($procfile)) {
    $procfileContent = file_get_contents($procfile);
    if (strpos($procfileContent, 'rm -f public/sitemap.xml') !== false) {
        echo "   ✅ Procfile contains sitemap.xml removal command\n";
    } else {
        echo "   ⚠️  Procfile does NOT contain sitemap.xml removal\n";
        echo "   Procfile content:\n";
        echo "   " . str_replace("\n", "\n   ", $procfileContent) . "\n";
    }
} else {
    echo "   ⚠️  Procfile not found\n";
}
echo "\n";

// Test 8: Check .htaccess
echo "8. Checking .htaccess...\n";
$htaccess = public_path('.htaccess');
if (file_exists($htaccess)) {
    $htaccessContent = file_get_contents($htaccess);
    if (strpos($htaccessContent, 'sitemap') !== false) {
        echo "   ✅ .htaccess contains sitemap rules\n";
        preg_match_all('/sitemap.*/i', $htaccessContent, $matches);
        foreach ($matches[0] as $match) {
            echo "   Rule: " . trim($match) . "\n";
        }
    } else {
        echo "   ⚠️  .htaccess does not contain sitemap rules\n";
    }
} else {
    echo "   ⚠️  .htaccess not found\n";
}
echo "\n";

// Summary
echo str_repeat("=", 70) . "\n";
echo "📊 DIAGNOSIS SUMMARY\n";
echo str_repeat("=", 70) . "\n";
echo "If you see 404 errors, check:\n";
echo "1. Is static sitemap.xml file blocking the route?\n";
echo "2. Is the route properly registered?\n";
echo "3. Does the controller work when called directly?\n";
echo "4. Is Procfile removing static files?\n";
echo "\n";

