<?php

declare(strict_types=1);

namespace DDWB\Middleware;

use DDWB\Middleware;
use DDWB\Auth;
use DDWB\Session;
use DDWB\Response;

/**
 * Authentication Middleware
 * 
 * Ensures the user is authenticated
 */
final class AuthMiddleware extends Middleware
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

        // Check if user is authenticated
        if (!$auth->isAuthenticated()) {
            if ($this->isAjax()) {
                $response->json([
                    'error' => 'Unauthenticated',
                    'redirect' => $this->resolve(Router::class)->route('login'),
                ], 401);
                exit;
            }

            $router = $this->resolve(Router::class);
            $loginUrl = $router->route('login');
            
            // Preserve the current URL for redirect after login
            $currentUrl = urlencode($this->getCurrentUrl());
            $loginUrl .= (str_contains($loginUrl, '?') ? '&' : '?') . 'redirect=' . $currentUrl;

            $response->redirect($loginUrl);
            exit;
        }

        $next();
    }
}
