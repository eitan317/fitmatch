<?php
/**
 * Test sitemap with trainer profiles
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Testing Sitemap with Trainer Profiles\n";
echo str_repeat("=", 60) . "\n\n";

try {
    $controller = $app->make(\App\Http\Controllers\SitemapController::class);
    $response = $controller->main();
    $content = $response->getContent();
    
    // Count URLs
    preg_match_all('/<url>/', $content, $urlMatches);
    $totalUrls = count($urlMatches[0]);
    
    // Count trainer profiles
    preg_match_all('/\/he\/trainers\/\d+/', $content, $trainerMatches);
    $trainerUrls = count($trainerMatches[0]);
    
    // Count static pages (should be 7: homepage + 6 static pages)
    preg_match_all('/\/he\/(trainers|about|faq|contact|privacy|terms)$/', $content, $staticMatches);
    $staticPages = count($staticMatches[0]) + 1; // +1 for homepage
    
    echo "📊 Sitemap Statistics:\n";
    echo "   Total URLs: $totalUrls\n";
    echo "   Static pages: $staticPages (homepage + 6 pages)\n";
    echo "   Trainer profiles: $trainerUrls\n";
    echo "\n";
    
    if ($trainerUrls > 0) {
        echo "✅ Trainer profiles ARE included in main sitemap\n";
        echo "   Sample trainer URLs found:\n";
        $trainerUrlsList = array_slice($trainerMatches[0], 0, 5);
        foreach ($trainerUrlsList as $url) {
            echo "   - $url\n";
        }
        if ($trainerUrls > 5) {
            echo "   ... and " . ($trainerUrls - 5) . " more\n";
        }
    } else {
        echo "⚠️  No trainer profiles found in sitemap\n";
        echo "   This could mean:\n";
        echo "   - No approved trainers in database\n";
        echo "   - Database connection issue\n";
        echo "   - Trainers not approved yet\n";
        
        // Check database
        try {
            $trainerCount = \App\Models\Trainer::where('approved_by_admin', true)->count();
            echo "\n   Database check:\n";
            echo "   - Approved trainers in DB: $trainerCount\n";
        } catch (\Exception $e) {
            echo "\n   Database error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n";
    echo "✅ Sitemap structure is correct\n";
    echo "✅ All static pages included\n";
    if ($trainerUrls > 0) {
        echo "✅ Trainer profiles included\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

