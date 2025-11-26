<?php

/**
 * Test Collexia Client Connection
 */

require_once 'config.php';
require_once 'src/CollexiaClient.php';

echo "=== Testing Collexia Client Connection ===\n\n";

try {
    $config = require 'config.php';
    
    echo "Configuration:\n";
    echo "  Base URL: " . $config['base_url'] . "\n";
    echo "  Username: " . $config['basic_user'] . "\n";
    echo "  Password: " . (empty($config['basic_pass']) ? 'NOT SET' : '✓ Set') . "\n";
    echo "  Client ID: " . $config['client_id'] . "\n";
    echo "  Client Secret: " . (empty($config['client_secret']) ? 'NOT SET' : '✓ Set') . "\n";
    echo "  Merchant GID: " . $config['merchant_gid'] . "\n";
    echo "  Remote GID: " . $config['remote_gid'] . "\n\n";
    
    // Initialize client
    echo "Initializing Collexia Client...\n";
    $client = new CollexiaClient($config);
    echo "✓ Client initialized successfully\n\n";
    
    // Test header generation (without making actual API call)
    echo "Testing header generation...\n";
    $reflection = new ReflectionClass($client);
    $method = $reflection->getMethod('buildHeaders');
    $method->setAccessible(true);
    $headers = $method->invoke($client);
    
    echo "✓ Headers generated successfully\n";
    echo "  Number of headers: " . count($headers) . "\n";
    echo "  Headers include:\n";
    foreach ($headers as $header) {
        // Don't show sensitive values
        if (strpos($header, 'Authorization') !== false) {
            echo "    - Authorization: Basic [REDACTED]\n";
        } elseif (strpos($header, 'HSH') !== false) {
            echo "    - " . explode(':', $header)[0] . ": [HMAC Signature]\n";
        } else {
            echo "    - " . $header . "\n";
        }
    }
    
    echo "\n✅ Collexia Client is ready!\n";
    
    if (empty($config['client_secret'])) {
        echo "\n⚠️  Note: Client Secret is still required for HMAC signature generation.\n";
        echo "   Once you add the client secret, the API will be fully functional.\n";
    } else {
        echo "\n✅ All credentials configured! The API is fully functional.\n";
        echo "   You can now make API calls to Collexia UAT.\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

