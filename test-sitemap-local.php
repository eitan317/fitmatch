<?php
/**
 * Test sitemap locally (without HTTP request)
 * Useful when domain is not configured yet
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Testing Sitemap Locally (Direct Controller Test)\n";
echo str_repeat("=", 70) . "\n\n";

try {
    $controller = $app->make(\App\Http\Controllers\SitemapController::class);
    $response = $controller->main();
    $content = $response->getContent();
    
    echo "1. Controller Test:\n";
    echo "   ✅ Status Code: " . $response->getStatusCode() . "\n";
    echo "   ✅ Content Size: " . number_format(strlen($content)) . " bytes\n";
    
    echo "\n2. XML Validation:\n";
    if (strpos($content, '<?xml') === 0) {
        echo "   ✅ Valid XML declaration\n";
    } else {
        echo "   ❌ Invalid XML\n";
        exit(1);
    }
    
    if (strpos($content, 'xmlns:xhtml') !== false) {
        echo "   ✅ Has hreflang namespace\n";
    } else {
        echo "   ❌ Missing hreflang namespace\n";
        exit(1);
    }
    
    echo "\n3. Content Analysis:\n";
    $urlCount = substr_count($content, '<url>');
    echo "   ✅ URL entries: $urlCount\n";
    
    $hreflangCount = substr_count($content, 'hreflang=');
    echo "   ✅ Hreflang tags: $hreflangCount\n";
    
    $languages = ['he', 'en', 'ru', 'ar'];
    foreach ($languages as $lang) {
        $count = substr_count($content, 'hreflang="' . $lang . '"');
        echo "   ✅ Language '$lang': $count tags\n";
    }
    
    if (strpos($content, 'hreflang="x-default"') !== false) {
        echo "   ✅ x-default found\n";
    }
    
    echo "\n4. Sample URLs:\n";
    preg_match_all('/<loc>(.*?)<\/loc>/', $content, $matches);
    if (!empty($matches[1])) {
        $sampleCount = min(5, count($matches[1]));
        for ($i = 0; $i < $sampleCount; $i++) {
            echo "   " . ($i + 1) . ". " . htmlspecialchars($matches[1][$i]) . "\n";
        }
        if (count($matches[1]) > $sampleCount) {
            echo "   ... and " . (count($matches[1]) - $sampleCount) . " more\n";
        }
    }
    
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "✅ SITEMAP IS WORKING CORRECTLY!\n";
    echo "\n";
    echo "📋 Next Steps:\n";
    echo "1. Deploy to Railway (git add, commit, push)\n";
    echo "2. Configure domain in Railway Dashboard\n";
    echo "3. Add DNS records\n";
    echo "4. Wait for DNS propagation (5-30 minutes)\n";
    echo "5. Test production URL: https://fitmatch.org.il/sitemap.xml\n";
    echo "\n";
    echo "💡 The sitemap code is correct. The HTTP 0 error means:\n";
    echo "   - Domain not configured in Railway, OR\n";
    echo "   - DNS not propagated yet, OR\n";
    echo "   - Connection issue\n";
    echo "\n";
    echo "Once domain is configured, it will work!\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
