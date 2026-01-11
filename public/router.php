<?php
/**
 * Router for PHP built-in server (Railway)
 * Routes all requests to Laravel's index.php
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$file = __DIR__ . $uri;

// Always route sitemap.xml to Laravel (before any file checks)
if ($uri === '/sitemap.xml' || preg_match('#^/sitemap.*\.xml$#i', $uri)) {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    chdir(__DIR__);
    require __DIR__ . '/index.php';
    exit; // CRITICAL: Stop execution after routing to Laravel
}

// Serve static files if they exist (CSS, JS, images, etc.)
if ($uri !== '/' && file_exists($file) && is_file($file)) {
    return false; // Let PHP server serve the static file
}

// Route everything else to Laravel
$_SERVER['SCRIPT_NAME'] = '/index.php';
chdir(__DIR__);
require __DIR__ . '/index.php';
