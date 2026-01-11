<?php
// Router for PHP built-in server (Railway)
// Routes static files directly, everything else to Laravel

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$file = __DIR__ . $path;

// Debug logging (temporary - remove after verification)
if (strpos($path, 'site/') !== false || strpos($path, '.css') !== false || strpos($path, '.js') !== false) {
    error_log("router.php: Request for {$path}, file exists: " . (file_exists($file) ? 'yes' : 'no') . ", is_file: " . (is_file($file) ? 'yes' : 'no'));
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
    
    error_log("router.php: Serving static file {$path} with MIME type {$mimeType}");
    
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
error_log("router.php: Routing {$path} to Laravel");
require __DIR__ . '/index.php';
