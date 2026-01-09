<?php
/**
 * Router for PHP built-in server (used by Railway)
 * 
 * This ensures sitemap.xml requests always route to Laravel,
 * even if a static file exists (for dynamic generation with latest data).
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));

// ALWAYS route sitemap.xml to Laravel (never serve static file)
// This ensures we get the latest data from the database
if (preg_match('#^/sitemap\.xml$#i', $uri)) {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['PHP_SELF'] = '/index.php';
    $_SERVER['REQUEST_URI'] = $uri;
    chdir(__DIR__);
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
chdir(__DIR__);
require __DIR__ . '/index.php';
exit;
