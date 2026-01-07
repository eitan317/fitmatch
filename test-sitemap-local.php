<?php
/**
 * Local Sitemap Test - Tests the controller directly
 * Usage: php test-sitemap-local.php
 */

echo "🔍 Testing Sitemap Controller Locally...\n\n";

try {
    // Bootstrap Laravel
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    echo "1. Testing SitemapController::main()...\n";
    $controller = $app->make(\App\Http\Controllers\SitemapController::class);
    $response = $controller->main();
    
    $content = $response->getContent();
    $statusCode = $response->getStatusCode();
    
    echo "   Status Code: $statusCode\n";
    echo "   Content Length: " . strlen($content) . " bytes\n\n";
    
    // Check for XML declaration
    echo "2. Checking XML structure...\n";
    if (strpos($content, '<?xml version="1.0"') === 0) {
        echo "   ✅ Valid XML declaration\n";
    } else {
        echo "   ❌ Invalid XML declaration\n";
    }
    
    // Check for hreflang namespace
    if (strpos($content, 'xmlns:xhtml') !== false) {
        echo "   ✅ Hreflang namespace found\n";
    } else {
        echo "   ❌ Hreflang namespace missing\n";
        echo "   First 500 chars: " . substr($content, 0, 500) . "\n";
    }
    
    // Check for hreflang links
    $hreflangCount = substr_count($content, 'xhtml:link');
    echo "   Found $hreflangCount hreflang links\n";
    
    if ($hreflangCount > 0) {
        echo "   ✅ Hreflang links present\n";
    } else {
        echo "   ❌ No hreflang links found\n";
    }
    
    // Check for language URLs
    echo "\n3. Checking language URLs...\n";
    $languages = ['/he/', '/en/', '/ru/', '/ar/'];
    foreach ($languages as $lang) {
        if (strpos($content, $lang) !== false) {
            echo "   ✅ Found $lang URLs\n";
        } else {
            echo "   ⚠️  Missing $lang URLs\n";
        }
    }
    
    // Show sample of content
    echo "\n4. Sample content (first 1000 chars):\n";
    echo str_repeat("-", 60) . "\n";
    echo htmlspecialchars(substr($content, 0, 1000)) . "\n";
    echo str_repeat("-", 60) . "\n";
    
    echo "\n✅ Local test complete!\n";
    echo "If all checks passed, deploy to Railway and test production URL.\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

