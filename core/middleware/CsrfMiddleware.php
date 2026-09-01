<?php

declare(strict_types=1);

namespace DDWB\Middleware;

use DDWB\Middleware;
use DDWB\Csrf;
use DDWB\Session;
use DDWB\Response;

/**
 * CSRF Middleware
 * 
 * Adds CSRF token to session and validates it for POST requests
 */
final class CsrfMiddleware extends Middleware
{
    /**
     * Handle the middleware
     * 
     * @param \Closure $next The next middleware/handler to call
     */
    public function handle(\Closure $next): void
    {
        $csrf = $this->resolve(Csrf::class);
        $session = $this->resolve(Session::class);
        $response = $this->resolve(Response::class);

        // Start session if not already started
        if (!$session->isStarted()) {
            $session->start();
        }

        // Generate CSRF token if not exists
        if (!$session->has($csrf->getTokenName())) {
            $csrf->generateToken();
        }

        // Only validate for state-changing methods
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $safeMethods = ['GET', 'HEAD', 'OPTIONS'];

        if (!in_array($method, $safeMethods, true)) {
            // Validate CSRF token
            if (!$csrf->validateToken()) {
                $response->json([
                    'error' => 'CSRF token validation failed',
                ], 403);
                exit;
            }
        }

        $next();
    }
}
