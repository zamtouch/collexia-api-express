<?php

/**
 * Simple Router for API endpoints
 */
class Router {
    
    private $routes = [];
    
    public function add($method, $path, $handler) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }
    
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Handle .htaccess rewrite - get path from query string if available
        if (isset($_GET['path'])) {
            $uri = '/' . ltrim($_GET['path'], '/');
        }
        
        // Remove base path if present (works for both /api/v1 and /v1)
        $basePath = '/api/v1';
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Also handle if path starts with /v1/ (after api/ was removed by .htaccess)
        if (strpos($uri, '/v1/') === 0) {
            $uri = substr($uri, 4); // Remove '/v1/'
        } elseif ($uri === '/v1') {
            $uri = '/';
        }
        
        // Ensure leading slash
        if (strpos($uri, '/') !== 0) {
            $uri = '/' . $uri;
        }
        
        $uri = rtrim($uri, '/') ?: '/';
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            $pattern = $this->convertToRegex($route['path']);
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match
                
                // Extract only numeric-indexed values (ignore named keys)
                $params = array_values($matches);
                
                // Call handler
                $handler = $route['handler'];
                if (is_array($handler) && count($handler) === 2) {
                    $controller = new $handler[0]();
                    call_user_func_array([$controller, $handler[1]], $params);
                } else if (is_callable($handler)) {
                    call_user_func_array($handler, $params);
                }
                return;
            }
        }
        
        // No route found
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Endpoint not found',
            'path' => $uri
        ]);
    }
    
    private function convertToRegex($path) {
        // Convert :param to named capture group
        $pattern = preg_replace('/:(\w+)/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
}



