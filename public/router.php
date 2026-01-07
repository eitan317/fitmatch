<?php
/**
 * Router for PHP built-in server (used by Railway)
 * This ensures sitemap.xml requests route to Laravel even if static file doesn't exist
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Handle sitemap.xml requests - route to Laravel if file doesn't exist
if (preg_match('#^/sitemap.*\.xml$#', $uri)) {
    $file = __DIR__ . $uri;
    
    // If static file doesn't exist, route to Laravel
    if (!file_exists($file) || !is_file($file)) {
        // Route to Laravel index.php
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        chdir(__DIR__);
        require __DIR__ . '/index.php';
        return true;
    }
    
    // If file exists, serve it normally
    return false;
}

// For all other requests, check if file exists
$file = __DIR__ . $uri;

// If file exists and is not a directory, serve it
if (file_exists($file) && is_file($file) && $uri !== '/') {
    return false; // Let PHP serve the file
}

// Otherwise, route to Laravel
$_SERVER['SCRIPT_NAME'] = '/index.php';
chdir(__DIR__);
require __DIR__ . '/index.php';
return true;

