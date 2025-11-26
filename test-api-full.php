<?php

/**
 * Full API Test - Tests multiple endpoints
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
        file_put_contents('php://input', json_encode($data));
    }
    
    ob_start();
    try {
        chdir(__DIR__);
        require_once 'api/index.php';
        $output = ob_get_clean();
        return json_decode($output, true);
    } catch (Exception $e) {
        ob_end_clean();
        return ['error' => $e->getMessage()];
    }
}

echo "=== Full API Test ===\n\n";

// Test 1: Health Check
echo "1. Health Check\n";
$result = testEndpoint('GET', '/api/v1/health');
echo "   " . ($result['success'] ? '✓' : '✗') . " " . ($result['message'] ?? 'Unknown') . "\n\n";

// Test 2: Create Student
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
if ($result['success']) {
    echo "   ✓ Student created: " . ($result['data']['student_id'] ?? 'N/A') . "\n";
    $studentId = $student['student_id'];
} else {
    echo "   ✗ Error: " . ($result['message'] ?? 'Unknown error') . "\n";
    $studentId = null;
}
echo "\n";

// Test 3: Get Student
if ($studentId) {
    echo "3. Get Student\n";
    $result = testEndpoint('GET', "/api/v1/students/{$studentId}");
    if ($result['success']) {
        echo "   ✓ Student found: " . ($result['data']['full_name'] ?? 'N/A') . "\n";
    } else {
        echo "   ✗ Error: " . ($result['message'] ?? 'Unknown error') . "\n";
    }
    echo "\n";
}

// Test 4: List Students
echo "4. List Students\n";
$result = testEndpoint('GET', '/api/v1/students');
if ($result['success']) {
    $count = count($result['data'] ?? []);
    echo "   ✓ Found {$count} student(s)\n";
} else {
    echo "   ✗ Error: " . ($result['message'] ?? 'Unknown error') . "\n";
}
echo "\n";

// Test 5: Create Property
echo "5. Create Property\n";
$property = [
    'property_code' => 'PROP' . time(),
    'property_name' => 'Test Property',
    'address' => '123 Test Street',
    'monthly_rent' => 5000.00
];
$result = testEndpoint('POST', '/api/v1/properties', $property);
if ($result['success']) {
    echo "   ✓ Property created: " . ($result['data']['property_id'] ?? 'N/A') . "\n";
    $propertyId = $result['data']['property_id'] ?? null;
} else {
    echo "   ✗ Error: " . ($result['message'] ?? 'Unknown error') . "\n";
    $propertyId = null;
}
echo "\n";

// Test 6: List Properties
echo "6. List Properties\n";
$result = testEndpoint('GET', '/api/v1/properties');
if ($result['success']) {
    $count = count($result['data'] ?? []);
    echo "   ✓ Found {$count} property(ies)\n";
} else {
    echo "   ✗ Error: " . ($result['message'] ?? 'Unknown error') . "\n";
}
echo "\n";

// Test 7: Invalid Endpoint (should return 404)
echo "7. Test Invalid Endpoint (404)\n";
$result = testEndpoint('GET', '/api/v1/nonexistent');
if (isset($result['success']) && !$result['success']) {
    echo "   ✓ Correctly returned error for invalid endpoint\n";
} else {
    echo "   ⚠ Unexpected response\n";
}
echo "\n";

echo "=== Test Complete ===\n";
echo "\nNote: Mandate and payment endpoints require valid Collexia credentials\n";
echo "      Update config.php with your Collexia API credentials to test those endpoints.\n";



