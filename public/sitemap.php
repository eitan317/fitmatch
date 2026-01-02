<?php
/**
 * Dynamic Sitemap Generator
 * This PHP file generates the sitemap.xml dynamically
 * It bypasses php artisan serve's static file serving
 */

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get the sitemap controller
$controller = $app->make(\App\Http\Controllers\SitemapController::class);
$response = $controller->main();

// Output with proper headers
header('Content-Type: application/xml; charset=utf-8');
echo $response->getContent();

