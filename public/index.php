<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Handle static files when used as router script
// This allows PHP server to serve static files directly for performance
// But ensures sitemap.xml always goes to Laravel
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$file = __DIR__ . $uri;

// Serve static files directly (but NOT sitemap.xml - it must go to Laravel)
if ($uri !== '/' && $uri !== '/sitemap.xml' && file_exists($file) && is_file($file)) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $staticExtensions = ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot', 'pdf', 'txt', 'json'];
    
    // If it's a static file (not PHP), let PHP server serve it directly
    if (in_array($ext, $staticExtensions) && $ext !== 'php') {
        return false; // PHP server will serve the static file
    }
}

// All other requests (including /sitemap.xml) go to Laravel
// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
