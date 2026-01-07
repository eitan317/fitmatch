<?php
/**
 * Generate static sitemap.xml file
 * Run this during deployment to create a fallback static sitemap
 * 
 * Usage: php generate-sitemap.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Generating sitemap.xml...\n";
    
    $controller = $app->make(\App\Http\Controllers\SitemapController::class);
    $response = $controller->main();
    
    $sitemapContent = $response->getContent();
    $sitemapPath = __DIR__ . '/public/sitemap.xml';
    
    // Write sitemap to file
    file_put_contents($sitemapPath, $sitemapContent);
    
    echo "✅ Sitemap generated successfully: $sitemapPath\n";
    echo "   Size: " . number_format(strlen($sitemapContent)) . " bytes\n";
    echo "   URLs: " . substr_count($sitemapContent, '<url>') . "\n";
    
    // Verify it's valid XML
    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($sitemapContent);
    if ($xml === false) {
        echo "❌ WARNING: Generated XML is not valid!\n";
        foreach (libxml_get_errors() as $error) {
            echo "   XML Error: " . trim($error->message) . "\n";
        }
        exit(1);
    } else {
        echo "✅ XML is valid\n";
    }
    
    exit(0);
} catch (\Exception $e) {
    echo "❌ Error generating sitemap: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

