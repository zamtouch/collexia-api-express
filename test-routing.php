<?php

/**
 * Test routing to see what URI is being received
 */

echo "=== Routing Debug Test ===\n\n";

echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "\n";
echo "QUERY_STRING: " . ($_SERVER['QUERY_STRING'] ?? 'NOT SET') . "\n";
echo "PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'NOT SET') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "\n";

if (isset($_GET['path'])) {
    echo "GET['path']: " . $_GET['path'] . "\n";
}

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
echo "Parsed URI: " . $uri . "\n";

// Test router logic
$basePath = '/api/v1';
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
    echo "After removing /api/v1: " . $uri . "\n";
}

if (isset($_GET['path'])) {
    $uri = '/' . $_GET['path'];
    echo "Using GET['path']: " . $uri . "\n";
}

if (strpos($uri, '/v1/') === 0) {
    $uri = substr($uri, 4);
    echo "After removing /v1/: " . $uri . "\n";
}

$uri = rtrim($uri, '/') ?: '/';
echo "Final URI: " . $uri . "\n";




