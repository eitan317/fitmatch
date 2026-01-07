<?php
/**
 * Router for PHP built-in server (used by Railway)
 * This ensures sitemap.xml requests route to Laravel even if static file doesn't exist
 */

// Get the request URI
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));

// Handle sitemap.xml requests - ALWAYS route to Laravel (never serve static file)
// Even if static file exists, route to Laravel for dynamic generation
if (preg_match('#^/sitemap.*\.xml$#', $uri)) {
    // Always route to Laravel for sitemap requests, regardless of static file existence
    // This ensures we always get the latest data from the database
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['PHP_SELF'] = '/index.php';
    $_SERVER['REQUEST_URI'] = $uri; // Preserve original URI
    chdir(__DIR__);
    require __DIR__ . '/index.php';
    exit; // Stop execution after routing to Laravel
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
chdir(__DIR__);
require __DIR__ . '/index.php';
exit; // Stop execution after routing to Laravel

