<?php
/**
 * Router for PHP built-in server (used by Railway)
 * 
 * This ensures sitemap.xml requests always route to Laravel,
 * even if a static file exists (for dynamic generation with latest data).
 */

// Get the request URI
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));

// Normalize URI - remove query string and ensure leading slash
$uri = '/' . ltrim($uri, '/');

// CRITICAL: ALWAYS route sitemap.xml to Laravel FIRST (before checking for static files)
// This must be checked before any file existence checks
if (strtolower($uri) === '/sitemap.xml' || preg_match('#^/sitemap\.xml$#i', $uri)) {
    // Set up environment for Laravel
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['PHP_SELF'] = '/index.php';
    $_SERVER['REQUEST_URI'] = '/sitemap.xml';
    $_SERVER['PATH_INFO'] = '/sitemap.xml';
    
    // Change to public directory
    chdir(__DIR__);
    
    // Route to Laravel
    require __DIR__ . '/index.php';
    exit;
}

// For all other requests, check if file exists
$file = __DIR__ . $uri;

// If file exists and is not a directory, serve it
if ($uri !== '/' && file_exists($file) && is_file($file)) {
    return false; // Let PHP serve the static file
}

// Otherwise, route to Laravel
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['PATH_INFO'] = $uri;
chdir(__DIR__);
require __DIR__ . '/index.php';
exit;
