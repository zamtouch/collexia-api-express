<?php
/**
 * Root Entry Point for Railway/Serverless Hosting
 * Routes requests to the API
 */

// Get the request path
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Route API requests to api/index.php
if (strpos($path, '/api/') === 0) {
    // Extract the path after /api/
    $apiPath = substr($path, 5); // Remove '/api/'
    
    // Handle /api/v1/ paths
    if (strpos($apiPath, 'v1/') === 0) {
        $apiPath = substr($apiPath, 3); // Remove 'v1/'
    } elseif ($apiPath === 'v1') {
        $apiPath = '';
    }
    
    // Set path parameter for router
    $_GET['path'] = $apiPath;
    
    // Route to API
    require __DIR__ . '/api/index.php';
    exit;
}

// Root endpoint - show API info
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Collexia Rental Payment API',
    'version' => '1.0.0',
    'endpoints' => [
        'health' => '/api/v1/health',
        'students' => '/api/v1/students',
        'properties' => '/api/v1/properties',
        'mandates' => '/api/v1/mandates',
        'payments' => '/api/v1/payments'
    ],
    'documentation' => '/api/README.md'
]);

