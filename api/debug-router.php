<?php

/**
 * Debug router to see what's happening
 */

echo "=== Router Debug ===\n\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "\n";
echo "QUERY_STRING: " . ($_SERVER['QUERY_STRING'] ?? 'NOT SET') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "\n";
echo "PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'NOT SET') . "\n";

if (isset($_GET['path'])) {
    echo "GET['path']: " . $_GET['path'] . "\n";
}

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
echo "Parsed URI: " . $uri . "\n";

// Test router logic
if (isset($_GET['path'])) {
    $uri = '/' . $_GET['path'];
    echo "Using GET['path']: " . $uri . "\n";
}

// Remove /collexia prefix if present
if (strpos($uri, '/collexia') === 0) {
    $uri = substr($uri, strlen('/collexia'));
    echo "After removing /collexia: " . $uri . "\n";
}

// Remove base path if present
$basePath = '/api/v1';
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
    echo "After removing /api/v1: " . $uri . "\n";
}

// Also handle if path starts with /v1/
if (strpos($uri, '/v1/') === 0) {
    $uri = substr($uri, 4);
    echo "After removing /v1/: " . $uri . "\n";
} elseif ($uri === '/v1') {
    $uri = '/';
    echo "After removing /v1 (root): " . $uri . "\n";
}

$uri = rtrim($uri, '/') ?: '/';
echo "Final URI: " . $uri . "\n";




