<?php
/**
 * Deployment Diagnostic Script
 * Run this to check if your server is configured correctly
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Collexia API Deployment Diagnostic</h1>\n";
echo "<pre>\n";

$errors = [];
$warnings = [];
$success = [];

// 1. Check PHP Version
echo "1. PHP Version: " . PHP_VERSION . "\n";
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    $success[] = "PHP version is OK";
} else {
    $errors[] = "PHP 7.4+ required. Current: " . PHP_VERSION;
}

// 2. Check required extensions
echo "\n2. Checking PHP Extensions:\n";
$required = ['pdo', 'pdo_mysql', 'curl', 'json', 'mbstring'];
foreach ($required as $ext) {
    if (extension_loaded($ext)) {
        echo "   ✓ $ext\n";
        $success[] = "Extension $ext loaded";
    } else {
        echo "   ✗ $ext MISSING\n";
        $errors[] = "Required extension missing: $ext";
    }
}

// 3. Check file structure
echo "\n3. Checking File Structure:\n";
$files = [
    __DIR__ . '/../config.php',
    __DIR__ . '/config.php',
    __DIR__ . '/database.php',
    __DIR__ . '/router.php',
    __DIR__ . '/index.php',
    __DIR__ . '/../src/CollexiaClient.php',
];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "   ✓ " . basename($file) . "\n";
        $success[] = "File exists: " . basename($file);
    } else {
        echo "   ✗ " . basename($file) . " MISSING\n";
        $errors[] = "Required file missing: " . $file;
    }
}

// 4. Check directory permissions
echo "\n4. Checking Directory Permissions:\n";
$logDir = __DIR__ . '/../logs';
if (is_dir($logDir)) {
    if (is_writable($logDir)) {
        echo "   ✓ logs/ directory is writable\n";
        $success[] = "Logs directory writable";
    } else {
        echo "   ✗ logs/ directory NOT writable\n";
        $warnings[] = "Logs directory not writable (chmod 755)";
    }
} else {
    echo "   ⚠ logs/ directory doesn't exist (will be created)\n";
    $warnings[] = "Logs directory doesn't exist";
}

// 5. Check environment variables
echo "\n5. Checking Environment Variables:\n";
$envVars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'APP_ENV'];
foreach ($envVars as $var) {
    $value = getenv($var);
    if ($value !== false) {
        echo "   ✓ $var = " . ($var === 'DB_PASS' ? '***' : $value) . "\n";
        $success[] = "Environment variable set: $var";
    } else {
        echo "   ⚠ $var not set (using defaults)\n";
        $warnings[] = "Environment variable not set: $var";
    }
}

// 6. Test database connection
echo "\n6. Testing Database Connection:\n";
try {
    require_once __DIR__ . '/database.php';
    $db = Database::getInstance()->getConnection();
    echo "   ✓ Database connection successful\n";
    $success[] = "Database connection OK";
} catch (Exception $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n";
    $errors[] = "Database connection failed: " . $e->getMessage();
}

// 7. Test config loading
echo "\n7. Testing Configuration Loading:\n";
try {
    require_once __DIR__ . '/../config.php';
    $config = require __DIR__ . '/../config.php';
    if (is_array($config) && !empty($config)) {
        echo "   ✓ Configuration loaded successfully\n";
        $success[] = "Configuration loaded";
    } else {
        echo "   ✗ Configuration is empty or invalid\n";
        $errors[] = "Configuration invalid";
    }
} catch (Exception $e) {
    echo "   ✗ Configuration loading failed: " . $e->getMessage() . "\n";
    $errors[] = "Configuration loading failed: " . $e->getMessage();
}

// 8. Test CollexiaClient
echo "\n8. Testing CollexiaClient:\n";
try {
    require_once __DIR__ . '/../src/CollexiaClient.php';
    if (class_exists('CollexiaClient')) {
        echo "   ✓ CollexiaClient class loaded\n";
        $success[] = "CollexiaClient loaded";
    } else {
        echo "   ✗ CollexiaClient class not found\n";
        $errors[] = "CollexiaClient class missing";
    }
} catch (Exception $e) {
    echo "   ✗ CollexiaClient loading failed: " . $e->getMessage() . "\n";
    $errors[] = "CollexiaClient loading failed: " . $e->getMessage();
}

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "SUMMARY:\n";
echo str_repeat("=", 50) . "\n";
echo "✓ Success: " . count($success) . "\n";
echo "⚠ Warnings: " . count($warnings) . "\n";
echo "✗ Errors: " . count($errors) . "\n\n";

if (count($errors) > 0) {
    echo "ERRORS (must fix):\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}

if (count($warnings) > 0) {
    echo "\nWARNINGS (should fix):\n";
    foreach ($warnings as $warning) {
        echo "  - $warning\n";
    }
}

if (count($errors) === 0) {
    echo "\n✅ All critical checks passed! Your API should work.\n";
    echo "\nTest the API: " . (isset($_SERVER['HTTP_HOST']) ? "https://" . $_SERVER['HTTP_HOST'] : "") . "/api/v1/health\n";
}

echo "</pre>\n";

