<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Handle sitemap.xml requests - ensure they go through Laravel routes
// php artisan serve serves static files before routes, so we need this check
if (isset($_SERVER['REQUEST_URI']) && preg_match('#^/sitemap.*\.xml$#', $_SERVER['REQUEST_URI'])) {
    // Log that we're handling sitemap request
    if (function_exists('error_log')) {
        error_log('Sitemap request detected in index.php: ' . $_SERVER['REQUEST_URI']);
    }
    // Continue to Laravel - don't serve static file
}

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
