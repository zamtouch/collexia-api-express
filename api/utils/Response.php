<?php

/**
 * Standardized API Response Helper
 */
class Response {
    
    /**
     * Send JSON response
     */
    public static function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Send success response
     */
    public static function success($data = null, $message = 'Success', $statusCode = 200) {
        self::json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ], $statusCode);
    }
    
    /**
     * Send error response
     */
    public static function error($message = 'An error occurred', $statusCode = 400, $errors = null) {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        self::json($response, $statusCode);
    }
    
    /**
     * Handle Collexia API response
     */
    public static function handleCollexiaResponse($result) {
        if ($result['ok']) {
            return self::success($result['data'], 'Operation successful');
        } else {
            $errorMessage = 'Collexia API error';
            $errors = null;
            
            if (isset($result['error'])) {
                if (is_array($result['error']) && isset($result['error']['errors'])) {
                    $errors = $result['error']['errors'];
                    $errorMessage = $result['error']['summary'] ?? $errorMessage;
                } else {
                    $errorMessage = is_string($result['error']) ? $result['error'] : json_encode($result['error']);
                }
            }
            
            $statusCode = $result['status'] ?? 500;
            return self::error($errorMessage, $statusCode, $errors);
        }
    }
}



