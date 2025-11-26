<?php

/**
 * Test API Endpoint via HTTP
 * This simulates what a frontend app would do
 */

// Simulate a request to the health endpoint
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/api/v1/health';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['HTTPS'] = '';

// Capture output
ob_start();

try {
    // Change to the project directory
    chdir(__DIR__);
    
    // Include the API
    require_once 'api/index.php';
    
    $output = ob_get_clean();
    
    echo "=== API Endpoint Test ===\n\n";
    echo "Response:\n";
    echo $output . "\n";
    
    // Try to parse as JSON
    $json = json_decode($output, true);
    if ($json) {
        echo "\nParsed JSON:\n";
        print_r($json);
        echo "\n";
        
        if (isset($json['success']) && $json['success']) {
            echo "✓ API is working correctly!\n";
        } else {
            echo "⚠ API returned but success=false\n";
        }
    } else {
        echo "⚠ Response is not valid JSON\n";
    }
    
} catch (Exception $e) {
    ob_end_clean();
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}



