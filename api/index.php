<?php

/**
 * API Entry Point
 */

// Enable CORS
require_once __DIR__ . '/utils/CORS.php';
CORS::handle();

// Load configuration and dependencies
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/router.php';
require_once __DIR__ . '/controllers/MandateController.php';
require_once __DIR__ . '/controllers/PaymentController.php';
require_once __DIR__ . '/controllers/StudentController.php';
require_once __DIR__ . '/controllers/PropertyController.php';

// Initialize database tables (run once, can be moved to migration script)
try {
    Database::getInstance()->initializeTables();
} catch (Exception $e) {
    // Tables might already exist, or database connection failed
    // Log error but don't stop execution - let the endpoint handle it
    error_log("Database initialization: " . $e->getMessage());
    // If it's a connection error, we'll handle it in the controllers
}

// Create router
$router = new Router();

// Student routes
$router->add('GET', '/students', [StudentController::class, 'list']);
$router->add('POST', '/students', [StudentController::class, 'create']);
$router->add('GET', '/students/:student_id', [StudentController::class, 'get']);

// Property routes
$router->add('GET', '/properties', [PropertyController::class, 'list']);
$router->add('POST', '/properties', [PropertyController::class, 'create']);
$router->add('GET', '/properties/:property_code', [PropertyController::class, 'get']);

// Mandate routes
$router->add('POST', '/mandates/register', [MandateController::class, 'register']);
$router->add('POST', '/mandates/status', [MandateController::class, 'status']);
$router->add('POST', '/mandates/cancel', [MandateController::class, 'cancel']);
$router->add('GET', '/mandates/:contract_reference', [MandateController::class, 'get']);

// Payment routes
$router->add('POST', '/payments/download', [PaymentController::class, 'download']);
$router->add('GET', '/payments/student/:student_id', [PaymentController::class, 'getByStudent']);
$router->add('GET', '/payments/contract/:contract_reference', [PaymentController::class, 'getByContract']);

// Health check
$router->add('GET', '/health', function() {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});

// Dispatch request
$router->dispatch();



