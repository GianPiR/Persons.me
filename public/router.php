<?php
// Router for PHP built-in server
// Routes all requests to Laravel

$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Handle static files (CSS, JS, images, etc.)
if ($path !== '/' && file_exists(__DIR__ . $path)) {
    return false; // Let the built-in server handle static files
}

// Handle API routes - pass to Laravel
if (strpos($path, '/api/') === 0) {
    require_once __DIR__ . '/index.php';
    return;
}

// Handle all other routes - pass to Laravel
require_once __DIR__ . '/index.php';
