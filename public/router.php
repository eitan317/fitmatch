<?php
/**
 * Router for PHP built-in server (used by Railway)
 * 
 * This router ensures:
 * 1. /sitemap.xml always routes to Laravel (never serves static file)
 * 2. Static files (CSS, JS, images) are served directly by PHP server
 * 3. All other requests route to Laravel
 */

// Get the request URI
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));

// Normalize URI
$uri = '/' . ltrim($uri, '/');

// Get the file path
$file = __DIR__ . $uri;

// CRITICAL: ALWAYS route sitemap.xml to Laravel FIRST
// This must be checked before any file existence checks
if (strtolower($uri) === '/sitemap.xml' || preg_match('#^/sitemap\.xml$#i', $uri)) {
    // Route to Laravel
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['PHP_SELF'] = '/index.php';
    $_SERVER['REQUEST_URI'] = '/sitemap.xml';
    $_SERVER['PATH_INFO'] = '/sitemap.xml';
    chdir(__DIR__);
    require __DIR__ . '/index.php';
    exit;
}

// For static files (CSS, JS, images, etc.), let PHP server serve them directly
// This improves performance for static assets
if ($uri !== '/' && file_exists($file) && is_file($file)) {
    // Check if it's a static file (not PHP)
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $staticExtensions = ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot', 'pdf', 'txt', 'xml', 'json'];
    
    if (in_array($extension, $staticExtensions)) {
        return false; // Let PHP server serve the static file
    }
}

// For all other requests (including PHP files), route to Laravel
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['PATH_INFO'] = $uri;
chdir(__DIR__);
require __DIR__ . '/index.php';
exit;
