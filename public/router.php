<?php
/**
 * Router for PHP built-in server (Railway)
 * 
 * This router ensures /sitemap.xml always routes to Laravel
 * and serves static files efficiently
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$uri = '/' . ltrim($uri, '/');
$file = __DIR__ . $uri;

// CRITICAL: ALWAYS route sitemap.xml to Laravel FIRST
// This must be checked before any file existence checks
if (strtolower($uri) === '/sitemap.xml' || preg_match('#^/sitemap\.xml$#i', $uri)) {
    // Set up environment for Laravel
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['PHP_SELF'] = '/index.php';
    $_SERVER['REQUEST_URI'] = '/sitemap.xml';
    $_SERVER['PATH_INFO'] = '/sitemap.xml';
    chdir(__DIR__);
    require __DIR__ . '/index.php';
    exit;
}

// Serve static files directly (CSS, JS, images, etc.)
// EXCLUDE XML files (including sitemap.xml) - they should always go through Laravel
if ($uri !== '/' && file_exists($file) && is_file($file)) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    
    // Explicitly exclude XML files from static serving (must go through Laravel)
    if ($ext === 'xml') {
        // Route XML files to Laravel (fall through to Laravel routing)
    } else {
        $staticExtensions = ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot', 'pdf', 'txt', 'json'];
        
        // If it's a static file (not PHP), let PHP server serve it directly
        if (in_array($ext, $staticExtensions) && $ext !== 'php') {
            return false; // PHP server will serve the static file
        }
    }
}

// Everything else goes to Laravel
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['PATH_INFO'] = $uri;
chdir(__DIR__);
require __DIR__ . '/index.php';
exit;
