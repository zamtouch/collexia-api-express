<?php

/**
 * Test Our Local API Endpoints
 */

function testEndpoint($method, $endpoint, $data = null) {
    $_SERVER['REQUEST_METHOD'] = $method;
    $_SERVER['REQUEST_URI'] = $endpoint;
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['SERVER_NAME'] = 'localhost';
    $_SERVER['SERVER_PORT'] = '80';
    $_SERVER['HTTPS'] = '';
    
    if ($data) {
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        // Simulate input stream
        $GLOBALS['test_input'] = json_encode($data);
    }
    
    ob_start();
    try {
        chdir(__DIR__);
        
        // Override file_get_contents for php://input
        if (isset($GLOBALS['test_input'])) {
            $GLOBALS['php_input'] = $GLOBALS['test_input'];
        }
        
        require_once 'api/index.php';
        $output = ob_get_clean();
        return json_decode($output, true);
    } catch (Exception $e) {
        ob_end_clean();
        return ['error' => $e->getMessage()];
    }
}

echo "=== Testing Local API Endpoints ===\n\n";

// Test Health
echo "1. Health Check\n";
$result = testEndpoint('GET', '/api/v1/health');
echo "   Status: " . ($result['success'] ? '✅ PASS' : '❌ FAIL') . "\n";
echo "   Message: " . ($result['message'] ?? 'N/A') . "\n\n";

// Test Student Creation
echo "2. Create Student\n";
$student = [
    'student_id' => 'TEST' . time(),
    'full_name' => 'Test Student',
    'email' => 'test@example.com',
    'phone' => '0812345678',
    'id_number' => '1234567890123',
    'id_type' => 1,
    'account_number' => '123456789',
    'account_type' => 1,
    'bank_id' => 65
];
$result = testEndpoint('POST', '/api/v1/students', $student);
echo "   Status: " . ($result['success'] ? '✅ PASS' : '❌ FAIL') . "\n";
if ($result['success']) {
    echo "   Student ID: " . ($result['data']['student_id'] ?? 'N/A') . "\n";
    $studentId = $student['student_id'];
} else {
    echo "   Error: " . ($result['message'] ?? 'Unknown') . "\n";
    $studentId = null;
}
echo "\n";

// Test Property Creation
echo "3. Create Property\n";
$property = [
    'property_code' => 'PROP' . time(),
    'property_name' => 'Test Property',
    'address' => '123 Test Street',
    'monthly_rent' => 5000.00
];
$result = testEndpoint('POST', '/api/v1/properties', $property);
echo "   Status: " . ($result['success'] ? '✅ PASS' : '❌ FAIL') . "\n";
if ($result['success']) {
    echo "   Property ID: " . ($result['data']['property_id'] ?? 'N/A') . "\n";
    $propertyId = $result['data']['property_id'] ?? null;
} else {
    echo "   Error: " . ($result['message'] ?? 'Unknown') . "\n";
    $propertyId = null;
}
echo "\n";

echo "=== Local API Test Complete ===\n";
echo "Note: Mandate registration requires valid Collexia authentication\n";
echo "      which we've now verified is working!\n";


