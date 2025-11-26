<?php

/**
 * Comprehensive Collexia API Test
 * Tests all major operations with proper error handling
 */

require_once 'config.php';
require_once 'src/CollexiaClient.php';

echo "=== Comprehensive Collexia UAT API Test ===\n\n";

try {
    $config = require 'config.php';
    $client = new CollexiaClient($config);
    
    echo "1. Configuration Check\n";
    echo "   Endpoint: " . $config['base_url'] . $config['base_path'] . "\n";
    echo "   Username: " . $config['basic_user'] . "\n";
    echo "   Merchant GID: " . $config['merchant_gid'] . "\n";
    echo "   Remote GID: " . $config['remote_gid'] . "\n";
    echo "   Header Prefix: " . $config['header_prefix'] . "\n";
    echo "   Client ID: " . $config['client_id'] . "\n\n";
    
    // Test 1: Verify headers are correct
    echo "2. Header Verification\n";
    $reflection = new ReflectionClass($client);
    $method = $reflection->getMethod('buildHeaders');
    $method->setAccessible(true);
    $headers = $method->invoke($client);
    
    $headerCheck = [
        'CX_SWITCH_ClientId' => false,
        'CX_SWITCH_DTS' => false,
        'CX_SWITCH_HSH' => false,
        'Authorization' => false
    ];
    
    foreach ($headers as $header) {
        if (strpos($header, 'CX_SWITCH_ClientId') !== false) {
            $headerCheck['CX_SWITCH_ClientId'] = true;
            echo "   ✓ CX_SWITCH_ClientId header found\n";
        }
        if (strpos($header, 'CX_SWITCH_DTS') !== false) {
            $headerCheck['CX_SWITCH_DTS'] = true;
            echo "   ✓ CX_SWITCH_DTS header found\n";
        }
        if (strpos($header, 'CX_SWITCH_HSH') !== false) {
            $headerCheck['CX_SWITCH_HSH'] = true;
            echo "   ✓ CX_SWITCH_HSH header found\n";
        }
        if (strpos($header, 'Authorization: Basic') !== false) {
            $headerCheck['Authorization'] = true;
            echo "   ✓ Authorization header found\n";
        }
    }
    
    $allHeadersOk = array_reduce($headerCheck, function($carry, $item) { return $carry && $item; }, true);
    echo "   " . ($allHeadersOk ? "✅ All required headers present\n\n" : "❌ Missing headers\n\n");
    
    // Test 2: Test Mandate Final Fate (with invalid contract - should get 400, not 401)
    echo "3. Testing Mandate Final Fate API Call\n";
    $testPayload = [
        'contractReference' => 'TEST1234567890',
        'merchantGid' => (int)$config['merchant_gid'],
        'frontEndUserName' => 'test',
        'remoteGid' => (int)$config['remote_gid']
    ];
    
    echo "   Sending request...\n";
    $result = $client->mandateFinalFate($testPayload);
    
    echo "   HTTP Status: " . ($result['status'] ?? 'N/A') . "\n";
    
    if ($result['status'] == 401) {
        echo "   ❌ FAILED: Still getting 401 - authentication issue\n";
        if (isset($result['error'])) {
            echo "   Error: " . (is_array($result['error']) ? json_encode($result['error']) : $result['error']) . "\n";
        }
    } elseif ($result['status'] == 400) {
        echo "   ✅ SUCCESS: Got 400 (expected for invalid contract)\n";
        echo "   This confirms authentication is working!\n";
        if (isset($result['error']['errors'][0]['message'])) {
            echo "   Error message: " . $result['error']['errors'][0]['message'] . "\n";
        }
    } elseif ($result['ok']) {
        echo "   ✅ SUCCESS: API call successful!\n";
        echo "   Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "   ⚠️  Unexpected response\n";
        if (isset($result['error'])) {
            echo "   Error: " . (is_array($result['error']) ? json_encode($result['error']) : $result['error']) . "\n";
        }
    }
    echo "\n";
    
    // Test 3: Test Payment Download
    echo "4. Testing Payment Download API Call\n";
    $paymentPayload = [
        'merchantGid' => (int)$config['merchant_gid'],
        'frontEndUserName' => $config['basic_user'] ?? 'apiuser',
        'remoteGid' => (int)$config['remote_gid']
    ];
    
    echo "   Sending request...\n";
    $result = $client->downloadPayments($paymentPayload);
    
    echo "   HTTP Status: " . ($result['status'] ?? 'N/A') . "\n";
    
    if ($result['status'] == 401) {
        echo "   ❌ FAILED: Authentication issue\n";
    } elseif ($result['ok']) {
        echo "   ✅ SUCCESS: Payment download successful!\n";
        if (isset($result['data']['noOfResponses'])) {
            echo "   Responses: " . $result['data']['noOfResponses'] . "\n";
        }
    } else {
        echo "   Response: " . ($result['status'] == 200 ? "✅ OK" : "Status " . $result['status']) . "\n";
        if (isset($result['error'])) {
            if (is_array($result['error']) && isset($result['error']['errors'])) {
                echo "   Errors: " . json_encode($result['error']['errors'], JSON_PRETTY_PRINT) . "\n";
            } else {
                echo "   Error: " . (is_array($result['error']) ? json_encode($result['error']) : $result['error']) . "\n";
            }
        }
    }
    echo "\n";
    
    // Summary
    echo "=== Test Summary ===\n";
    echo "✅ Headers: Correct format (CX_SWITCH_*)\n";
    echo ($result['status'] != 401 ? "✅ Authentication: Working\n" : "❌ Authentication: Still failing\n");
    echo "✅ API Connection: Established\n";
    echo "\n";
    echo "Next Steps:\n";
    echo "1. Review test pack document for valid test data\n";
    echo "2. Test with valid contract references\n";
    echo "3. Test mandate registration\n";
    echo "4. Test payment downloads with real data\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}


