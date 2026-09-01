<?php

declare(strict_types=1);

namespace DDWB;

/**
 * Middleware Interface
 * 
 * All middleware classes must implement this interface
 */
interface MiddlewareInterface
{
    /**
     * Handle the middleware
     * 
     * @param \Closure $next The next middleware/handler to call
     */
    public function handle(\Closure $next): void;
}
