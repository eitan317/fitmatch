<?php
// Router for PHP built-in server (Railway)
// Routes static files directly, everything else to Laravel

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$file = __DIR__ . $path;

// ALWAYS route sitemap.xml to Laravel (before any file checks)
if ($path === '/sitemap.xml' || preg_match('#^/sitemap.*\.xml$#i', $path)) {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    chdir(__DIR__);
    require __DIR__ . '/index.php';
    exit; // CRITICAL: Stop execution after routing to Laravel
}

// Serve existing static files directly (css/js/images/etc.)
if ($path !== '/' && file_exists($file) && is_file($file)) {
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
    ];
    
    $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
    
    // Clear any output buffering
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Set headers and serve file
    header('Content-Type: ' . $mimeType, true);
    header('Content-Length: ' . filesize($file), true);
    header('Cache-Control: public, max-age=31536000', true);
    
    readfile($file);
    exit;
}

// Route everything else through Laravel
require __DIR__ . '/index.php';
