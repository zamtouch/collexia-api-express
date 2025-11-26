<?php

/**
 * Example test script for the Collexia API
 * 
 * This file demonstrates how to use the API endpoints.
 * Run this from command line: php api/test-examples.php
 * 
 * Note: Update the base URL to match your server configuration
 */

$baseUrl = 'http://localhost/api/v1';

function apiCall($method, $endpoint, $data = null) {
    global $baseUrl;
    
    $url = $baseUrl . $endpoint;
    $ch = curl_init($url);
    
    $headers = ['Content-Type: application/json'];
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

echo "=== Collexia API Test Examples ===\n\n";

// 1. Health Check
echo "1. Health Check\n";
$result = apiCall('GET', '/health');
echo "Status: {$result['code']}\n";
print_r($result['data']);
echo "\n";

// 2. Register a Student
echo "2. Register Student\n";
$student = [
    'student_id' => 'STU' . time(),
    'full_name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'phone' => '0812345678',
    'id_number' => '1234567890123',
    'id_type' => 1, // 1=RSA ID
    'account_number' => '123456789',
    'account_type' => 1, // 1=Current
    'bank_id' => 65 // 65=FNB Namibia
];
$result = apiCall('POST', '/students', $student);
echo "Status: {$result['code']}\n";
print_r($result['data']);
$studentId = $student['student_id'];
echo "\n";

// 3. Register a Property
echo "3. Register Property\n";
$property = [
    'property_code' => 'PROP' . time(),
    'property_name' => 'Student Residence Block A',
    'address' => '123 Main Street, Windhoek, Namibia',
    'monthly_rent' => 5000.00
];
$result = apiCall('POST', '/properties', $property);
echo "Status: {$result['code']}\n";
print_r($result['data']);
$propertyId = $result['data']['data']['property_id'] ?? 1;
echo "\n";

// 4. Get Student
echo "4. Get Student\n";
$result = apiCall('GET', "/students/{$studentId}");
echo "Status: {$result['code']}\n";
print_r($result['data']);
echo "\n";

// 5. List Properties
echo "5. List Properties\n";
$result = apiCall('GET', '/properties');
echo "Status: {$result['code']}\n";
echo "Properties found: " . count($result['data']['data'] ?? []) . "\n";
echo "\n";

// Note: To test mandate registration, you need valid Collexia credentials
// Uncomment the following section after configuring Collexia:

/*
// 6. Register Mandate (Rent Payment Setup)
echo "6. Register Mandate\n";
$mandate = [
    'student_id' => $studentId,
    'property_id' => $propertyId,
    'monthly_rent' => 5000.00,
    'start_date' => date('Ymd', strtotime('+1 month')),
    'frequency_code' => 4, // 4=Monthly
    'no_of_installments' => 12,
    'tracking_days' => 3,
    'mag_id' => 46 // 46=Endo
];
$result = apiCall('POST', '/mandates/register', $mandate);
echo "Status: {$result['code']}\n";
print_r($result['data']);
$contractRef = $result['data']['data']['contract_reference'] ?? null;
echo "\n";

if ($contractRef) {
    // 7. Check Mandate Status
    echo "7. Check Mandate Status\n";
    $result = apiCall('POST', '/mandates/status', [
        'contract_reference' => $contractRef
    ]);
    echo "Status: {$result['code']}\n";
    print_r($result['data']);
    echo "\n";
    
    // 8. Get Mandate Details
    echo "8. Get Mandate Details\n";
    $result = apiCall('GET', "/mandates/{$contractRef}");
    echo "Status: {$result['code']}\n";
    print_r($result['data']);
    echo "\n";
}
*/

echo "=== Test Complete ===\n";



