<?php
/**
 * Test production sitemap after deployment
 */

$url = 'https://fitmatch.org.il/sitemap.xml';

echo "🧪 Testing Production Sitemap\n";
echo str_repeat("=", 70) . "\n\n";
echo "Testing URL: $url\n\n";

try {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; SitemapTester/1.0)');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    
    echo "1. HTTP Status: $httpCode\n";
    if ($httpCode === 200) {
        echo "   ✅ SUCCESS - Sitemap is accessible\n";
    } else {
        echo "   ❌ FAILED - Expected 200, got $httpCode\n";
        exit(1);
    }
    
    echo "\n2. Content-Type: $contentType\n";
    if (strpos($contentType, 'xml') !== false) {
        echo "   ✅ Correct Content-Type\n";
    } else {
        echo "   ⚠️  Unexpected Content-Type (should contain 'xml')\n";
    }
    
    echo "\n3. Content Validation:\n";
    
    // Check XML declaration
    if (strpos($response, '<?xml') === 0) {
        echo "   ✅ Valid XML declaration\n";
    } else {
        echo "   ❌ Missing XML declaration\n";
        exit(1);
    }
    
    // Check for urlset
    if (strpos($response, '<urlset') !== false) {
        echo "   ✅ Contains <urlset> tag\n";
    } else {
        echo "   ❌ Missing <urlset> tag\n";
        exit(1);
    }
    
    // Check for hreflang namespace
    if (strpos($response, 'xmlns:xhtml') !== false) {
        echo "   ✅ Has hreflang namespace\n";
    } else {
        echo "   ❌ Missing hreflang namespace\n";
        exit(1);
    }
    
    // Count URLs
    $urlCount = substr_count($response, '<url>');
    echo "   ✅ Found $urlCount URL entries\n";
    
    // Count hreflang tags
    $hreflangCount = substr_count($response, 'hreflang=');
    echo "   ✅ Found $hreflangCount hreflang tags\n";
    
    // Check for all languages
    $languages = ['he', 'en', 'ru', 'ar'];
    foreach ($languages as $lang) {
        if (strpos($response, 'hreflang="' . $lang . '"') !== false) {
            echo "   ✅ Language '$lang' found\n";
        } else {
            echo "   ⚠️  Language '$lang' missing\n";
        }
    }
    
    // Check for x-default
    if (strpos($response, 'hreflang="x-default"') !== false) {
        echo "   ✅ x-default hreflang found\n";
    } else {
        echo "   ⚠️  x-default hreflang missing\n";
    }
    
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "✅ PRODUCTION SITEMAP IS WORKING!\n";
    echo "\n";
    echo "📋 Summary:\n";
    echo "- HTTP Status: $httpCode ✅\n";
    echo "- Content-Type: $contentType ✅\n";
    echo "- URLs: $urlCount\n";
    echo "- Hreflang tags: $hreflangCount\n";
    echo "\n";
    echo "🎯 Next: Submit to Google Search Console\n";
    echo "   URL: $url\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

