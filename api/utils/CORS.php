<?php

/**
 * CORS Handler
 */
class CORS {
    
    public static function handle() {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        
        // Use constant if defined, otherwise use default
        $allowedOrigins = defined('ALLOWED_ORIGINS') ? ALLOWED_ORIGINS : [
            'http://localhost:3000',
            'http://localhost:3001',
            'exp://localhost:8081',
            'exp://192.168.*.*:8081',
        ];
        
        // Check if origin is allowed
        $allowed = false;
        foreach ($allowedOrigins as $allowedOrigin) {
            if (self::matchOrigin($origin, $allowedOrigin)) {
                $allowed = true;
                break;
            }
        }
        
        if ($allowed) {
            header("Access-Control-Allow-Origin: {$origin}");
        }
        
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');
        
        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
    
    private static function matchOrigin($origin, $pattern) {
        // Simple wildcard matching for IP addresses
        $regexPattern = str_replace(['*', '.'], ['[0-9]+', '\.'], $pattern);
        // Escape forward slashes in the pattern
        $regexPattern = str_replace('/', '\/', $regexPattern);
        return preg_match("#^{$regexPattern}$#", $origin) || $origin === $pattern;
    }
}

