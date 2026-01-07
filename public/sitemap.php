<?php
/**
 * Dynamic Sitemap Generator
 * This PHP file generates the sitemap.xml dynamically
 * It bypasses php artisan serve's static file serving
 * 
 * Access via: /sitemap.php (works with php artisan serve)
 * Or: /sitemap.xml (if route works)
 */

// Log that sitemap.php is being used
if (function_exists('error_log')) {
    error_log('Sitemap.php file accessed: ' . ($_SERVER['REQUEST_URI'] ?? 'unknown'));
}

try {
    // Bootstrap Laravel
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    // Get the sitemap controller
    $controller = $app->make(\App\Http\Controllers\SitemapController::class);
    $response = $controller->main();

    // Output with proper headers
    header('Content-Type: application/xml; charset=utf-8');
    header('Cache-Control: public, max-age=3600');
    http_response_code($response->getStatusCode());
    echo $response->getContent();
    
} catch (\Exception $e) {
    // Error handling - return valid XML with error message
    header('Content-Type: application/xml; charset=utf-8');
    http_response_code(500);
    
    $errorMessage = htmlspecialchars($e->getMessage(), ENT_XML1, 'UTF-8');
    $errorTrace = htmlspecialchars(substr($e->getTraceAsString(), 0, 500), ENT_XML1, 'UTF-8');
    
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    echo '  <error>' . "\n";
    echo '    <message>' . $errorMessage . '</message>' . "\n";
    if (defined('APP_DEBUG') && APP_DEBUG) {
        echo '    <trace>' . $errorTrace . '</trace>' . "\n";
    }
    echo '  </error>' . "\n";
    echo '</urlset>';
    
    // Log error for debugging
    if (function_exists('error_log')) {
        error_log('Sitemap.php error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
    }
}

