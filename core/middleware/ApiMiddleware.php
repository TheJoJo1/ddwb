<?php

declare(strict_types=1);

namespace DDWB\Middleware;

use DDWB\Middleware;
use DDWB\Auth;
use DDWB\Response;

/**
 * API Middleware
 * 
 * Sets JSON content type and handles API-specific logic
 */
final class ApiMiddleware extends Middleware
{
    /**
     * Handle the middleware
     * 
     * @param \Closure $next The next middleware/handler to call
     */
    public function handle(\Closure $next): void
    {
        $response = $this->resolve(Response::class);
        
        // Set JSON content type
        $response->header('Content-Type', 'application/json');
        
        // Handle OPTIONS requests for CORS
        if ($_SERVER['REQUEST_METHOD'] ?? '' === 'OPTIONS') {
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-Token');
            $response->header('Access-Control-Max-Age', '86400');
            http_response_code(204);
            exit;
        }

        // Set CORS headers for API responses
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-Token');

        $next();
    }
}
