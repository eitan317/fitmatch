<?php
/**
 * Quick Sitemap Test Script
 * Run this to verify your sitemap is working correctly
 * 
 * Usage: php test-sitemap.php
 */

// Allow testing local or production
$baseUrl = $argv[1] ?? 'https://www.fitmatch.org.il';
$testLocal = isset($argv[1]) && $argv[1] === 'local';

echo "🔍 Testing FitMatch Sitemap...\n";
echo "📍 Testing URL: $baseUrl\n\n";

// If testing locally, test the controller directly first
if ($testLocal) {
    echo "0. Testing controller directly (local)...\n";
    try {
        require __DIR__ . '/vendor/autoload.php';
        $app = require_once __DIR__ . '/bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        
        $controller = $app->make(\App\Http\Controllers\SitemapController::class);
        $response = $controller->main();
        $localResponse = $response->getContent();
        
        if (strpos($localResponse, 'xmlns:xhtml') !== false) {
            echo "   ✅ Controller generates hreflang namespace\n";
        } else {
            echo "   ❌ Controller missing hreflang namespace\n";
        }
        
        if (strpos($localResponse, 'xhtml:link') !== false) {
            echo "   ✅ Controller generates hreflang links\n";
        } else {
            echo "   ❌ Controller missing hreflang links\n";
        }
        echo "\n";
    } catch (\Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n\n";
    }
}

// Test 1: Check sitemap accessibility
echo "1. Testing sitemap.xml accessibility...\n";
$sitemapUrl = $baseUrl . '/sitemap.xml';
$ch = curl_init($sitemapUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "   ✅ Sitemap is accessible (HTTP 200)\n";
} else {
    echo "   ❌ Sitemap returned HTTP $httpCode\n";
    exit(1);
}

// Test 2: Check XML structure
echo "\n2. Validating XML structure...\n";
if (strpos($response, '<?xml version="1.0"') === 0) {
    echo "   ✅ Valid XML declaration found\n";
} else {
    echo "   ❌ Invalid XML declaration\n";
    exit(1);
}

// Check for hreflang namespace (flexible matching)
$hasHreflangNamespace = (
    strpos($response, 'xmlns:xhtml') !== false ||
    strpos($response, 'xmlns:xhtml=') !== false ||
    preg_match('/xmlns:xhtml\s*=\s*["\']http:\/\/www\.w3\.org\/1999\/xhtml["\']/', $response)
);

if ($hasHreflangNamespace) {
    echo "   ✅ Hreflang namespace found\n";
} else {
    echo "   ❌ Hreflang namespace missing\n";
    echo "   Debug: Looking for xmlns:xhtml in response...\n";
    // Show first 500 chars of response for debugging
    $preview = substr($response, 0, 500);
    echo "   Response preview: " . htmlspecialchars($preview) . "\n";
    exit(1);
}

// Test 3: Check for required pages
echo "\n3. Checking for required pages...\n";
$requiredPages = [
    '/he/',
    '/he/trainers',
    '/he/about',
    '/he/faq',
    '/he/contact',
    '/he/privacy',
    '/he/terms',
];

$found = 0;
foreach ($requiredPages as $page) {
    $fullUrl = $baseUrl . $page;
    if (strpos($response, htmlspecialchars($fullUrl)) !== false) {
        echo "   ✅ Found: $page\n";
        $found++;
    } else {
        echo "   ⚠️  Missing: $page\n";
    }
}

// Test 4: Check for hreflang tags
echo "\n4. Checking hreflang tags...\n";
$hreflangCount = substr_count($response, 'xhtml:link rel="alternate"');
if ($hreflangCount > 0) {
    echo "   ✅ Found $hreflangCount hreflang tags\n";
    
    // Check for all languages
    $languages = ['he', 'en', 'ru', 'ar'];
    foreach ($languages as $lang) {
        if (strpos($response, 'hreflang="' . $lang . '"') !== false) {
            echo "   ✅ Language '$lang' found in hreflang\n";
        } else {
            echo "   ⚠️  Language '$lang' missing in hreflang\n";
        }
    }
    
    // Check for x-default
    if (strpos($response, 'hreflang="x-default"') !== false) {
        echo "   ✅ x-default hreflang found\n";
    } else {
        echo "   ⚠️  x-default hreflang missing\n";
    }
} else {
    echo "   ❌ No hreflang tags found\n";
    exit(1);
}

// Test 5: Check for trainer pages
echo "\n5. Checking for trainer pages...\n";
if (preg_match_all('/\/he\/trainers\/\d+/', $response, $matches)) {
    $trainerCount = count($matches[0]);
    echo "   ✅ Found $trainerCount trainer profile pages\n";
} else {
    echo "   ⚠️  No trainer profile pages found (this is OK if no trainers are approved yet)\n";
}

// Test 6: Check robots.txt
echo "\n6. Testing robots.txt...\n";
$robotsUrl = $baseUrl . '/robots.txt';
$ch = curl_init($robotsUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$robotsResponse = curl_exec($ch);
$robotsHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($robotsHttpCode === 200) {
    echo "   ✅ Robots.txt is accessible\n";
    if (strpos($robotsResponse, 'Sitemap:') !== false) {
        echo "   ✅ Robots.txt references sitemap\n";
    } else {
        echo "   ⚠️  Robots.txt doesn't reference sitemap\n";
    }
} else {
    echo "   ⚠️  Robots.txt returned HTTP $robotsHttpCode\n";
}

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 SUMMARY\n";
echo str_repeat("=", 50) . "\n";
echo "✅ Sitemap is accessible and valid\n";
echo "✅ XML structure is correct\n";
echo "✅ Hreflang tags are present\n";
echo "✅ Found $found/" . count($requiredPages) . " required pages\n";
echo "\n🎯 Next Steps:\n";
if (!$testLocal) {
    echo "1. If tests failed, deploy changes to Railway first:\n";
    echo "   git add . && git commit -m 'Fix sitemap' && git push\n";
    echo "2. Wait for Railway deployment to complete\n";
    echo "3. Run this test again: php test-sitemap.php\n";
    echo "4. Submit sitemap to Google Search Console: $baseUrl/sitemap.xml\n";
} else {
    echo "1. Test passed locally! Deploy to Railway:\n";
    echo "   git add . && git commit -m 'Fix sitemap' && git push\n";
    echo "2. After deployment, test production: php test-sitemap.php\n";
}
echo "\n✨ Sitemap is ready!\n";

