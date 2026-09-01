<?php

declare(strict_types=1);

namespace DDWB\Middleware;

use DDWB\Middleware;
use DDWB\Session;

/**
 * Session Middleware
 * 
 * Starts the PHP session
 */
final class SessionMiddleware extends Middleware
{
    /**
     * Handle the middleware
     * 
     * @param \Closure $next The next middleware/handler to call
     */
    public function handle(\Closure $next): void
    {
        $session = $this->resolve(Session::class);
        $session->start();
        
        $next();
    }
}
