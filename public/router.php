<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

// Log router.php execution for sitemap requests
if (strpos($path, 'sitemap') !== false) {
    error_log("router.php: Request for {$path}, file exists: " . (file_exists($file) ? 'yes' : 'no'));
}

if ($path !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}
require __DIR__ . '/index.php';
