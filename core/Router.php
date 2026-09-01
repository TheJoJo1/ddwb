<?php

declare(strict_types=1);

namespace DDWB;

use DDWB\Container;

/**
 * Router
 * 
 * Handles routing and dispatching of HTTP requests
 */
final class Router
{
    private Container $container;
    
    /** @var array<array{method: string, path: string, handler: string, middleware: array, name: ?string}> */
    private array $routes = [];
    
    /** @var array<string, array{method: string, path: string, handler: string, middleware: array}> */
    private array $namedRoutes = [];

    private string $basePath;
    private string $requestUri;
    private string $requestMethod;

    /**
     * Create a new Router instance
     * 
     * @param Container $container The dependency injection container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->basePath = dirname(__DIR__, 2);
        $this->detectRequest();
    }

    /**
     * Detect the current request method and URI
     */
    private function detectRequest(): void
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        // Handle CLI requests
        if (php_sapi_name() === 'cli') {
            $this->requestUri = '/';
            return;
        }

        // Get the request URI
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Remove query string
        if (($pos = strpos($requestUri, '?')) !== false) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        // Remove base URL from request URI
        $baseUrl = $this->getBaseUrl();
        if ($baseUrl !== '/' && str_starts_with($requestUri, $baseUrl)) {
            $requestUri = substr($requestUri, strlen($baseUrl));
        }

        // Ensure we have at least a slash
        $this->requestUri = $requestUri ?: '/';
    }

    /**
     * Get the base URL of the application
     * 
     * @return string The base URL
     */
    public function getBaseUrl(): string
    {
        // Try to get from configuration first
        $app = Application::getInstance();
        $appUrl = $app->getConfigValue('app.url', '');
        
        if (!empty($appUrl)) {
            return rtrim($appUrl, '/');
        }

        // Auto-detect base URL
        if (php_sapi_name() === 'cli') {
            return '';
        }

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        
        // Remove index.php from script name if present
        $scriptName = str_replace('/index.php', '', $scriptName);
        
        // Build base URL
        $baseUrl = $protocol . '://' . $host . $scriptName;
        
        // Remove trailing slash
        return rtrim($baseUrl, '/');
    }

    /**
     * Add a route
     * 
     * @param string|array $methods The HTTP method(s)
     * @param string $path The route path
     * @param string|callable $handler The route handler
     * @param array $middleware The middleware stack
     * @param string|null $name The route name
     */
    public function addRoute(
        string|array $methods,
        string $path,
        string|callable $handler,
        array $middleware = [],
        ?string $name = null
    ): void {
        if (is_array($methods)) {
            foreach ($methods as $method) {
                $this->addRoute($method, $path, $handler, $middleware, $name);
            }
            return;
        }

        $method = strtoupper($methods);
        
        // Normalize path
        $path = '/' . trim($path, '/');
        
        $route = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware,
            'name' => $name,
        ];

        $this->routes[] = $route;
        
        if ($name !== null) {
            $this->namedRoutes[$name] = $route;
        }
    }

    /**
     * Add a GET route
     * 
     * @param string $path The route path
     * @param string|callable $handler The route handler
     * @param array $middleware The middleware stack
     * @param string|null $name The route name
     */
    public function get(string $path, string|callable $handler, array $middleware = [], ?string $name = null): void
    {
        $this->addRoute('GET', $path, $handler, $middleware, $name);
    }

    /**
     * Add a POST route
     * 
     * @param string $path The route path
     * @param string|callable $handler The route handler
     * @param array $middleware The middleware stack
     * @param string|null $name The route name
     */
    public function post(string $path, string|callable $handler, array $middleware = [], ?string $name = null): void
    {
        $this->addRoute('POST', $path, $handler, $middleware, $name);
    }

    /**
     * Add a PUT route
     * 
     * @param string $path The route path
     * @param string|callable $handler The route handler
     * @param array $middleware The middleware stack
     * @param string|null $name The route name
     */
    public function put(string $path, string|callable $handler, array $middleware = [], ?string $name = null): void
    {
        $this->addRoute('PUT', $path, $handler, $middleware, $name);
    }

    /**
     * Add a PATCH route
     * 
     * @param string $path The route path
     * @param string|callable $handler The route handler
     * @param array $middleware The middleware stack
     * @param string|null $name The route name
     */
    public function patch(string $path, string|callable $handler, array $middleware = [], ?string $name = null): void
    {
        $this->addRoute('PATCH', $path, $handler, $middleware, $name);
    }

    /**
     * Add a DELETE route
     * 
     * @param string $path The route path
     * @param string|callable $handler The route handler
     * @param array $middleware The middleware stack
     * @param string|null $name The route name
     */
    public function delete(string $path, string|callable $handler, array $middleware = [], ?string $name = null): void
    {
        $this->addRoute('DELETE', $path, $handler, $middleware, $name);
    }

    /**
     * Add a route for multiple methods
     * 
     * @param array $methods The HTTP methods
     * @param string $path The route path
     * @param string|callable $handler The route handler
     * @param array $middleware The middleware stack
     * @param string|null $name The route name
     */
    public function match(array $methods, string $path, string|callable $handler, array $middleware = [], ?string $name = null): void
    {
        $this->addRoute($methods, $path, $handler, $middleware, $name);
    }

    /**
     * Add a route for all methods
     * 
     * @param string $path The route path
     * @param string|callable $handler The route handler
     * @param array $middleware The middleware stack
     * @param string|null $name The route name
     */
    public function any(string $path, string|callable $handler, array $middleware = [], ?string $name = null): void
    {
        $this->addRoute(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], $path, $handler, $middleware, $name);
    }

    /**
     * Get a route by name
     * 
     * @param string $name The route name
     * @return array|null The route or null if not found
     */
    public function getRouteByName(string $name): ?array
    {
        return $this->namedRoutes[$name] ?? null;
    }

    /**
     * Generate a URL for a named route
     * 
     * @param string $name The route name
     * @param array $parameters The route parameters
     * @return string The generated URL
     */
    public function route(string $name, array $parameters = []): string
    {
        $route = $this->namedRoutes[$name] ?? null;
        
        if ($route === null) {
            return '#';
        }

        $path = $route['path'];
        
        // Replace route parameters
        foreach ($parameters as $key => $value) {
            $path = str_replace('{' . $key . '}', (string)$value, $path);
        }

        // Remove any remaining optional parameters
        $path = preg_replace('/\{[^}]+\?\}/', '', $path);
        
        return $this->getBaseUrl() . $path;
    }

    /**
     * Dispatch the current request
     */
    public function dispatch(): void
    {
        // Find matching route
        $route = $this->findMatchingRoute();

        if ($route === null) {
            $this->handleNotFound();
            return;
        }

        // Extract route parameters
        $params = $this->extractRouteParams($route['path']);

        // Build middleware stack
        $middlewareStack = $this->buildMiddlewareStack($route['middleware']);

        // Execute middleware and handler
        $this->executeStack($middlewareStack, $route['handler'], $params);
    }

    /**
     * Find the matching route for the current request
     * 
     * @return array|null The matching route or null if not found
     */
    private function findMatchingRoute(): ?array
    {
        foreach ($this->routes as $route) {
            if ($this->matchRoute($route)) {
                return $route;
            }
        }

        return null;
    }

    /**
     * Check if a route matches the current request
     * 
     * @param array $route The route to check
     * @return bool True if the route matches
     */
    private function matchRoute(array $route): bool
    {
        // Check method
        if ($route['method'] !== $this->requestMethod) {
            return false;
        }

        // Check path pattern
        $pattern = $this->routeToRegex($route['path']);
        
        return preg_match($pattern, $this->requestUri) === 1;
    }

    /**
     * Convert a route path to a regex pattern
     * 
     * @param string $path The route path
     * @return string The regex pattern
     */
    private function routeToRegex(string $path): string
    {
        // Escape special regex characters except for our placeholders
        $pattern = preg_quote($path, '#');
        
        // Replace {param} with regex group
        $pattern = preg_replace('/\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\}/', '(?P<$1>[^/]+)', $pattern);
        
        // Replace {param?} with optional regex group
        $pattern = preg_replace('/\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\?\\\}/', '(?P<$1>[^/]*)?', $pattern);

        // Anchor the pattern
        return '#^' . $pattern . '$#';
    }

    /**
     * Extract route parameters from the current request
     * 
     * @param string $path The route path
     * @return array The extracted parameters
     */
    private function extractRouteParams(string $path): array
    {
        $pattern = $this->routeToRegex($path);
        $matches = [];
        
        preg_match($pattern, $this->requestUri, $matches);
        
        $params = [];
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    /**
     * Build the middleware stack
     * 
     * @param array $middleware The middleware classes
     * @return array The middleware stack
     */
    private function buildMiddlewareStack(array $middleware): array
    {
        $stack = [];
        
        foreach ($middleware as $middlewareClass) {
            if (is_string($middlewareClass) && class_exists($middlewareClass)) {
                $stack[] = $this->container->resolve($middlewareClass);
            } elseif (is_object($middlewareClass) && $middlewareClass instanceof MiddlewareInterface) {
                $stack[] = $middlewareClass;
            }
        }

        return $stack;
    }

    /**
     * Execute the middleware stack and handler
     * 
     * @param array $middlewareStack The middleware stack
     * @param string|callable $handler The route handler
     * @param array $params The route parameters
     */
    private function executeStack(array $middlewareStack, string|callable $handler, array $params): void
    {
        $next = function () use ($handler, $params) {
            $this->executeHandler($handler, $params);
        };

        // Execute middleware in reverse order (last middleware runs first)
        foreach (array_reverse($middlewareStack) as $middleware) {
            $next = function () use ($middleware, $next) {
                $middleware->handle($next);
            };
        }

        $next();
    }

    /**
     * Execute the route handler
     * 
     * @param string|callable $handler The route handler
     * @param array $params The route parameters
     */
    private function executeHandler(string|callable $handler, array $params): void
    {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }

        // Parse handler string (Controller@method or just Controller)
        if (str_contains($handler, '@')) {
            [$controllerClass, $method] = explode('@', $handler, 2);
        } else {
            $controllerClass = $handler;
            $method = 'index';
        }

        // Add Controller suffix if not present
        if (!str_ends_with($controllerClass, 'Controller')) {
            $controllerClass .= 'Controller';
        }

        // Add namespace prefix if not present
        if (!str_contains($controllerClass, '\\')) {
            // Try to find the controller in modules
            $controllerClass = $this->findControllerClass($controllerClass);
        }

        if (!class_exists($controllerClass)) {
            $this->handleNotFound();
            return;
        }

        $controller = $this->container->resolve($controllerClass);

        if (!method_exists($controller, $method)) {
            $this->handleNotFound();
            return;
        }

        call_user_func_array([$controller, $method], $params);
    }

    /**
     * Find the controller class in modules
     * 
     * @param string $controller The controller name
     * @return string The full controller class name
     */
    private function findControllerClass(string $controller): string
    {
        $app = Application::getInstance();
        $modules = $app->getConfigValue('modules', []);
        
        foreach ($modules as $module) {
            $className = 'DDWB\\Modules\\' . ucfirst($module) . '\\Controllers\\' . $controller;
            
            if (class_exists($className)) {
                return $className;
            }
        }

        // Try core controllers
        $className = 'DDWB\\Controllers\\' . $controller;
        if (class_exists($className)) {
            return $className;
        }

        return $controller;
    }

    /**
     * Handle 404 Not Found
     */
    private function handleNotFound(): void
    {
        http_response_code(404);
        
        $app = Application::getInstance();
        $errorHandler = $app->getConfigValue('errors.404', null);
        
        if ($errorHandler !== null && is_callable($errorHandler)) {
            call_user_func($errorHandler);
            return;
        }

        // Default 404 handler
        $controller = new \DDWB\Controllers\ErrorController();
        $controller->notFound();
    }

    /**
     * Get all registered routes
     * 
     * @return array The registered routes
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Get the current request URI
     * 
     * @return string The request URI
     */
    public function getRequestUri(): string
    {
        return $this->requestUri;
    }

    /**
     * Get the current request method
     * 
     * @return string The request method
     */
    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }
}
