<?php
/**
 * Generate static sitemap.xml file
 * Run this during Railway deployment
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $controller = $app->make(\App\Http\Controllers\SitemapController::class);
    $response = $controller->index();
    
    $sitemapContent = $response->getContent();
    $sitemapPath = __DIR__ . '/public/sitemap.xml';
    
    file_put_contents($sitemapPath, $sitemapContent);
    
    echo "✅ Sitemap generated: $sitemapPath\n";
    echo "   Size: " . number_format(strlen($sitemapContent)) . " bytes\n";
    echo "   URLs: " . substr_count($sitemapContent, '<url>') . "\n";
    
    exit(0);
} catch (\Exception $e) {
    echo "❌ Error generating sitemap: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
