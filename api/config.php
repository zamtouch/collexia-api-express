<?php

/**
 * API Configuration
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/CollexiaClient.php';

// Load Collexia configuration
$GLOBALS['collexiaConfig'] = require __DIR__ . '/../config.php';

// API Configuration
define('API_VERSION', 'v1');
define('API_BASE_PATH', '/api/' . API_VERSION);

// CORS Configuration
if (!defined('ALLOWED_ORIGINS')) {
    define('ALLOWED_ORIGINS', [
        'http://localhost:3000',      // Next.js dev
        'http://localhost:3001',      // Next.js alt port
        'exp://localhost:8081',       // Expo dev
        'exp://192.168.*.*:8081',     // Expo network
        'https://app.pozi.com.na',    // Production domain
        'https://www.pozi.com.na',    // Production domain with www
    ]);
}

// Error Reporting - Use environment variable for production
$isProduction = getenv('APP_ENV') === 'production';
error_reporting($isProduction ? E_ALL & ~E_DEPRECATED & ~E_STRICT : E_ALL);
ini_set('display_errors', $isProduction ? 0 : 1);
ini_set('log_errors', 1);

// Ensure logs directory exists and is writable
// Use /tmp for serverless environments (Vercel), otherwise use logs/
$isServerless = getenv('VERCEL') !== false || getenv('RAILWAY_ENVIRONMENT') !== false;
$logDir = $isServerless ? '/tmp' : (__DIR__ . '/../logs');

if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}
ini_set('error_log', $logDir . '/error.log');

// Timezone - Use default if Africa/Windhoek not available
if (function_exists('date_default_timezone_set')) {
    @date_default_timezone_set('Africa/Windhoek') || date_default_timezone_set('UTC');
}

// Initialize Collexia Client
function getCollexiaClient() {
    return new CollexiaClient($GLOBALS['collexiaConfig']);
}

