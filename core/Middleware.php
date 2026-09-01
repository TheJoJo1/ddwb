<?php

declare(strict_types=1);

namespace DDWB;

/**
 * Base Middleware Class
 * 
 * Provides common middleware functionality
 */
abstract class Middleware implements MiddlewareInterface
{
    protected Container $container;

    /**
     * Create a new Middleware instance
     * 
     * @param Container $container The dependency injection container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get the container
     * 
     * @return Container The container
     */
    protected function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Get a service from the container
     * 
     * @template T
     * @param string $service The service name or class
     * @return T The resolved service
     */
    protected function resolve(string $service): mixed
    {
        return $this->container->resolve($service);
    }

    /**
     * Check if the current request is an AJAX request
     * 
     * @return bool True if it's an AJAX request
     */
    protected function isAjax(): bool
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
               (isset($_SERVER['HTTP_ACCEPT']) && 
                str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));
    }

    /**
     * Check if the current request is a POST request
     * 
     * @return bool True if it's a POST request
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] ?? '' === 'POST';
    }

    /**
     * Check if the current request is a GET request
     * 
     * @return bool True if it's a GET request
     */
    protected function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] ?? '' === 'GET';
    }

    /**
     * Get the current URL
     * 
     * @return string The current URL
     */
    protected function getCurrentUrl(): string
    {
        $router = $this->resolve(Router::class);
        return $router->getRequestUri();
    }

    /**
     * Redirect to a URL
     * 
     * @param string $url The URL to redirect to
     * @param int $statusCode The HTTP status code
     */
    protected function redirect(string $url, int $statusCode = 302): void
    {
        $response = $this->resolve(Response::class);
        $response->redirect($url, $statusCode);
    }

    /**
     * Get the session
     * 
     * @return Session The session instance
     */
    protected function session(): Session
    {
        return $this->resolve(Session::class);
    }

    /**
     * Get the auth service
     * 
     * @return Auth The auth instance
     */
    protected function auth(): Auth
    {
        return $this->resolve(Auth::class);
    }
}
