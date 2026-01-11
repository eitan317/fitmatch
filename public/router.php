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
    // Get file extension for MIME type detection
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    
    // MIME type mapping
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject',
        'pdf' => 'application/pdf',
        'txt' => 'text/plain',
        'xml' => 'application/xml',
    ];
    
    $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
    
    // Set headers and serve file
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: public, max-age=31536000');
    
    readfile($file);
    exit;
}

// Route everything else to Laravel
$_SERVER['SCRIPT_NAME'] = '/index.php';
chdir(__DIR__);
require __DIR__ . '/index.php';
