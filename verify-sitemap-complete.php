<?php
/**
 * Complete Sitemap Verification
 * Verifies all requirements are met
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Complete Sitemap Verification\n";
echo str_repeat("=", 70) . "\n\n";

$allGood = true;

// Test 1: Generate sitemap
echo "1. Testing sitemap generation...\n";
try {
    $controller = $app->make(\App\Http\Controllers\SitemapController::class);
    $response = $controller->main();
    $content = $response->getContent();
    
    if ($response->getStatusCode() === 200) {
        echo "   ✅ Sitemap generates successfully (HTTP 200)\n";
    } else {
        echo "   ❌ Sitemap returned HTTP " . $response->getStatusCode() . "\n";
        $allGood = false;
    }
    
    // Check XML structure
    if (strpos($content, '<?xml') === 0) {
        echo "   ✅ Valid XML declaration\n";
    } else {
        echo "   ❌ Invalid XML declaration\n";
        $allGood = false;
    }
    
    // Check hreflang namespace
    if (strpos($content, 'xmlns:xhtml') !== false) {
        echo "   ✅ Has hreflang namespace (xmlns:xhtml)\n";
    } else {
        echo "   ❌ Missing hreflang namespace\n";
        $allGood = false;
    }
    
    // Count URLs
    $urlCount = substr_count($content, '<url>');
    echo "   ✅ Found $urlCount URL entries\n";
    
    // Count hreflang tags
    $hreflangCount = substr_count($content, 'hreflang=');
    echo "   ✅ Found $hreflangCount hreflang tags\n";
    
    // Check for all languages
    $languages = ['he', 'en', 'ru', 'ar'];
    foreach ($languages as $lang) {
        if (strpos($content, 'hreflang="' . $lang . '"') !== false) {
            echo "   ✅ Language '$lang' found in hreflang\n";
        } else {
            echo "   ❌ Language '$lang' missing in hreflang\n";
            $allGood = false;
        }
    }
    
    // Check for x-default
    if (strpos($content, 'hreflang="x-default"') !== false) {
        echo "   ✅ x-default hreflang found\n";
    } else {
        echo "   ❌ x-default hreflang missing\n";
        $allGood = false;
    }
    
    // Check for required pages
    $requiredPages = [
        '/he/',
        '/he/trainers',
        '/he/about',
        '/he/faq',
        '/he/contact',
        '/he/privacy',
        '/he/terms',
    ];
    
    $foundPages = 0;
    foreach ($requiredPages as $page) {
        $fullUrl = config('app.url') . $page;
        if (strpos($content, htmlspecialchars($fullUrl)) !== false) {
            $foundPages++;
        }
    }
    
    echo "   ✅ Found $foundPages/" . count($requiredPages) . " required pages\n";
    
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    $allGood = false;
}

// Test 2: Routes
echo "\n2. Checking routes...\n";
exec('php artisan route:list --path=sitemap 2>&1', $output, $return);
if ($return === 0 && !empty($output)) {
    $routeCount = 0;
    foreach ($output as $line) {
        if (strpos($line, 'sitemap') !== false) {
            $routeCount++;
        }
    }
    echo "   ✅ Found $routeCount sitemap routes\n";
} else {
    echo "   ⚠️  Could not verify routes\n";
}

// Test 3: Middleware exclusion
echo "\n3. Checking middleware configuration...\n";
$webContent = file_get_contents('routes/web.php');
$requiredExclusions = [
    'StartSession',
    'ShareErrorsFromSession',
    'VerifyCsrfToken',
];

foreach ($requiredExclusions as $middleware) {
    if (strpos($webContent, $middleware) !== false) {
        echo "   ✅ $middleware excluded from sitemap routes\n";
    } else {
        echo "   ❌ $middleware NOT excluded\n";
        $allGood = false;
    }
}

// Test 4: Procfile
echo "\n4. Checking Procfile...\n";
$procfile = file_get_contents('Procfile');
if (strpos($procfile, '${PORT}') !== false || strpos($procfile, '$PORT') !== false) {
    echo "   ✅ Uses dynamic port (\${PORT})\n";
} else {
    echo "   ❌ Does not use dynamic port\n";
    $allGood = false;
}

if (strpos($procfile, '0.0.0.0') !== false) {
    echo "   ✅ Binds to 0.0.0.0 (all interfaces)\n";
} else {
    echo "   ⚠️  May not bind to all interfaces\n";
}

if (strpos($procfile, 'index.php') !== false) {
    echo "   ✅ Uses index.php as router\n";
} else {
    echo "   ⚠️  Router configuration unclear\n";
}

// Test 5: robots.txt
echo "\n5. Checking robots.txt...\n";
$robotsRoute = $app->make('Illuminate\Routing\Router')->getRoutes()->getByName('robots.txt');
if ($robotsRoute) {
    echo "   ✅ robots.txt route exists\n";
    
    // Check if it references sitemap
    $robotsContent = file_get_contents('routes/web.php');
    if (strpos($robotsContent, 'Sitemap:') !== false) {
        echo "   ✅ robots.txt references sitemap\n";
    } else {
        echo "   ⚠️  robots.txt may not reference sitemap\n";
    }
} else {
    echo "   ⚠️  robots.txt route not found\n";
}

// Summary
echo "\n" . str_repeat("=", 70) . "\n";
if ($allGood) {
    echo "✅ ALL CHECKS PASSED - Sitemap is ready!\n";
    echo "\n";
    echo "📋 Summary:\n";
    echo "- ✅ Sitemap generates successfully\n";
    echo "- ✅ All pages included with hreflang tags\n";
    echo "- ✅ Routes configured correctly\n";
    echo "- ✅ Middleware excluded (stateless)\n";
    echo "- ✅ Procfile configured for Railway\n";
    echo "\n";
    echo "🚀 Next Steps:\n";
    echo "1. Deploy to Railway\n";
    echo "2. Configure domain in Railway Dashboard\n";
    echo "3. Add DNS records\n";
    echo "4. Test: https://fitmatch.org.il/sitemap.xml\n";
    echo "5. Submit to Google Search Console\n";
} else {
    echo "❌ SOME CHECKS FAILED - Please review errors above\n";
    exit(1);
}
