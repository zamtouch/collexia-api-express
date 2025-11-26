<?php

/**
 * Quick API Test Script
 */

echo "=== Collexia API Test ===\n\n";

// Test 1: Check PHP extensions
echo "1. Checking PHP Extensions:\n";
echo "   PDO: " . (extension_loaded('pdo') ? '✓ Available' : '✗ Missing') . "\n";
echo "   cURL: " . (extension_loaded('curl') ? '✓ Available' : '✗ Missing') . "\n";
echo "   PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✓ Available' : '✗ Missing') . "\n\n";

// Test 2: Check database connection
echo "2. Testing Database Connection:\n";
try {
    require_once 'api/database.php';
    $db = Database::getInstance()->getConnection();
    echo "   ✓ Database connection successful\n";
    
    // Try to initialize tables
    try {
        Database::getInstance()->initializeTables();
        echo "   ✓ Database tables initialized/verified\n";
    } catch (Exception $e) {
        echo "   ⚠ Table initialization: " . $e->getMessage() . "\n";
    }
} catch (Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n";
    echo "   Note: Make sure MySQL is running and database 'collexia_rentals' exists\n";
}
echo "\n";

// Test 3: Check Collexia Client
echo "3. Testing Collexia Client:\n";
try {
    require_once 'api/config.php';
    $client = getCollexiaClient();
    echo "   ✓ Collexia client initialized\n";
    echo "   Note: Actual API calls require valid credentials in config.php\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Check API files
echo "4. Checking API Files:\n";
$files = [
    'api/index.php',
    'api/router.php',
    'api/config.php',
    'api/database.php',
    'api/controllers/MandateController.php',
    'api/controllers/PaymentController.php',
    'api/controllers/StudentController.php',
    'api/controllers/PropertyController.php',
    'api/utils/Response.php',
    'api/utils/CORS.php',
    'api/utils/Validator.php',
    'api/utils/ContractReference.php'
];

foreach ($files as $file) {
    echo "   " . (file_exists($file) ? '✓' : '✗') . " $file\n";
}
echo "\n";

// Test 5: Test Router
echo "5. Testing Router:\n";
try {
    require_once 'api/router.php';
    $router = new Router();
    echo "   ✓ Router class loaded\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 6: Test Utility Classes
echo "6. Testing Utility Classes:\n";
try {
    require_once 'api/utils/ContractReference.php';
    $ref = ContractReference::generate(12345);
    echo "   ✓ ContractReference: Generated '$ref'\n";
    echo "   ✓ Validation: " . (ContractReference::validate($ref) ? 'Valid' : 'Invalid') . "\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== Test Complete ===\n";
echo "\nNext steps:\n";
echo "1. Ensure MySQL is running\n";
echo "2. Create database: CREATE DATABASE collexia_rentals;\n";
echo "3. Update config.php with your Collexia credentials\n";
echo "4. Test API endpoints using: curl http://localhost/api/v1/health\n";



