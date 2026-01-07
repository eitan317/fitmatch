<?php
/**
 * Test the router.php file
 */

// Simulate a request to /sitemap.xml
$_SERVER['REQUEST_URI'] = '/sitemap.xml';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['HTTPS'] = 'off';

// Change to public directory
chdir(__DIR__ . '/public');

// Include router
$result = require 'router.php';

echo "Router result: " . ($result ? "true (routed)" : "false (not routed)") . "\n";

