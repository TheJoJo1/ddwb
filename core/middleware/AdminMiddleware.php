<?php

declare(strict_types=1);

namespace DDWB\Middleware;

use DDWB\Middleware;
use DDWB\Auth;
use DDWB\Session;
use DDWB\Response;

/**
 * Admin Middleware
 * 
 * Ensures the user is authenticated and has admin role
 */
final class AdminMiddleware extends Middleware
{
    /**
     * Handle the middleware
     * 
     * @param \Closure $next The next middleware/handler to call
     */
    public function handle(\Closure $next): void
    {
        $auth = $this->resolve(Auth::class);
        $response = $this->resolve(Response::class);
        $session = $this->resolve(Session::class);

        // Start session if not already started
        if (!$session->isStarted()) {
            $session->start();
        }

        // First check authentication
        if (!$auth->isAuthenticated()) {
            if ($this->isAjax()) {
                $response->json([
                    'error' => 'Unauthenticated',
                ], 401);
                exit;
            }

            $router = $this->resolve(Router::class);
            $loginUrl = $router->route('login');
            
            $currentUrl = urlencode($this->getCurrentUrl());
            $loginUrl .= (str_contains($loginUrl, '?') ? '&' : '?') . 'redirect=' . $currentUrl;

            $response->redirect($loginUrl);
            exit;
        }

        // Check admin role
        if (!$auth->isAdmin()) {
            if ($this->isAjax()) {
                $response->json([
                    'error' => 'Insufficient permissions',
                ], 403);
                exit;
            }

            // Redirect to dashboard or show 403
            $router = $this->resolve(Router::class);
            $response->redirect($router->route('dashboard'));
            exit;
        }

        $next();
    }
}
