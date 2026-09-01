<?php

declare(strict_types=1);

namespace DDWB;

/**
 * Base Controller
 * 
 * All controllers should extend this base class
 */
abstract class Controller
{
    protected Container $container;
    protected Router $router;
    protected Session $session;
    protected Auth $auth;
    protected Csrf $csrf;
    protected Database $database;
    protected Logger $logger;
    protected Validator $validator;
    protected Response $response;

    /**
     * Create a new Controller instance
     * 
     * @param Container $container The dependency injection container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->router = $container->resolve(Router::class);
        $this->session = $container->resolve(Session::class);
        $this->auth = $container->resolve(Auth::class);
        $this->csrf = $container->resolve(Csrf::class);
        $this->database = $container->resolve(Database::class);
        $this->logger = $container->resolve(Logger::class);
        $this->validator = $container->resolve(Validator::class);
        $this->response = $container->resolve(Response::class);

        // Start session if not already started
        if (!$this->session->isStarted()) {
            $this->session->start();
        }
    }

    /**
     * Get the container
     * 
     * @return Container The dependency injection container
     */
    protected function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Get the router
     * 
     * @return Router The router instance
     */
    protected function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * Get the session
     * 
     * @return Session The session instance
     */
    protected function getSession(): Session
    {
        return $this->session;
    }

    /**
     * Get the auth service
     * 
     * @return Auth The authentication service
     */
    protected function getAuth(): Auth
    {
        return $this->auth;
    }

    /**
     * Get the CSRF service
     * 
     * @return Csrf The CSRF protection service
     */
    protected function getCsrf(): Csrf
    {
        return $this->csrf;
    }

    /**
     * Get the database
     * 
     * @return Database The database instance
     */
    protected function getDatabase(): Database
    {
        return $this->database;
    }

    /**
     * Get the logger
     * 
     * @return Logger The logger instance
     */
    protected function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * Get the validator
     * 
     * @return Validator The validator instance
     */
    protected function getValidator(): Validator
    {
        return $this->validator;
    }

    /**
     * Get the response
     * 
     * @return Response The response instance
     */
    protected function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Render a view
     * 
     * @param string $view The view name (without .php extension)
     * @param array $data The data to pass to the view
     * @param string|null $layout The layout to use (default: 'layout')
     */
    protected function view(string $view, array $data = [], ?string $layout = 'layout'): void
    {
        // Extract data for the view
        extract($data, EXTR_SKIP);

        // Store old input data in session
        if (isset($data['old'])) {
            $this->session->flash('old', $data['old']);
        }

        // Store errors in session
        if (isset($data['errors'])) {
            $this->session->flash('errors', $data['errors']);
        }

        // Store success message in session
        if (isset($data['success'])) {
            $this->session->flash('success', $data['success']);
        }

        // Store error message in session
        if (isset($data['error'])) {
            $this->session->flash('error', $data['error']);
        }

        // Build the view path
        $viewPath = $this->getViewPath($view);

        if ($viewPath === null) {
            throw new \RuntimeException("View [{$view}] not found.");
        }

        // Start output buffering
        ob_start();

        // Include the view
        include $viewPath;

        // Get the view content
        $content = ob_get_clean();

        // Render with layout if specified
        if ($layout !== null) {
            $layoutPath = $this->getLayoutPath($layout);
            
            if ($layoutPath !== null) {
                // Pass content to layout
                $viewContent = $content;
                include $layoutPath;
                return;
            }
        }

        // Output the content directly if no layout
        echo $content;
    }

    /**
     * Get the path to a view file
     * 
     * @param string $view The view name
     * @return string|null The view path or null if not found
     */
    private function getViewPath(string $view): ?string
    {
        $app = Application::getInstance();
        $basePath = $app->getBasePath();

        // Try different view locations
        $locations = [
            // Module views
            $basePath . '/modules/*/views/' . $view . '.php',
            // Core views
            $basePath . '/templates/' . $view . '.php',
            // Public views
            $basePath . '/public/views/' . $view . '.php',
        ];

        foreach ($locations as $pattern) {
            if (str_contains($pattern, '*')) {
                // Handle module views
                $modules = $app->getConfigValue('modules', []);
                $viewPattern = str_replace('*', '', $pattern);

                foreach ($modules as $module) {
                    $path = str_replace('*', $module, $pattern);
                    if (file_exists($path)) {
                        return $path;
                    }
                }
            } elseif (file_exists($pattern)) {
                return $pattern;
            }
        }

        return null;
    }

    /**
     * Get the path to a layout file
     * 
     * @param string $layout The layout name
     * @return string|null The layout path or null if not found
     */
    private function getLayoutPath(string $layout): ?string
    {
        $app = Application::getInstance();
        $basePath = $app->getBasePath();

        $path = $basePath . '/templates/' . $layout . '.php';

        if (file_exists($path)) {
            return $path;
        }

        return null;
    }

    /**
     * Redirect to a URL
     * 
     * @param string $url The URL to redirect to
     * @param int $statusCode The HTTP status code
     */
    protected function redirect(string $url, int $statusCode = 302): void
    {
        $this->response->redirect($url, $statusCode);
    }

    /**
     * Redirect to a named route
     * 
     * @param string $name The route name
     * @param array $parameters The route parameters
     * @param int $statusCode The HTTP status code
     */
    protected function redirectToRoute(string $name, array $parameters = [], int $statusCode = 302): void
    {
        $url = $this->router->route($name, $parameters);
        $this->redirect($url, $statusCode);
    }

    /**
     * Redirect back to the previous page
     * 
     * @param string|null $fallback The fallback URL
     * @param int $statusCode The HTTP status code
     */
    protected function redirectBack(?string $fallback = null, int $statusCode = 302): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? null;

        if ($referer !== null) {
            $this->redirect($referer, $statusCode);
            return;
        }

        if ($fallback !== null) {
            $this->redirect($fallback, $statusCode);
            return;
        }

        $this->redirect('/', $statusCode);
    }

    /**
     * Get the current URL
     * 
     * @return string The current URL
     */
    protected function getCurrentUrl(): string
    {
        return $this->router->getRequestUri();
    }

    /**
     * Get a request input value
     * 
     * @param string $key The input key
     * @param mixed $default The default value
     * @return mixed The input value
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        return $_REQUEST[$key] ?? $default;
    }

    /**
     * Get a GET input value
     * 
     * @param string $key The input key
     * @param mixed $default The default value
     * @return mixed The input value
     */
    protected function query(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Get a POST input value
     * 
     * @param string $key The input key
     * @param mixed $default The default value
     * @return mixed The input value
     */
    protected function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get all POST data
     * 
     * @return array The POST data
     */
    protected function all(): array
    {
        return $_POST;
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
     * Validate input data
     * 
     * @param array $data The data to validate
     * @param array $rules The validation rules
     * @param array $messages The custom error messages
     * @return bool True if validation passes
     */
    protected function validate(array $data, array $rules, array $messages = []): bool
    {
        $validator = $this->validator->withData($data)->withRules($rules)->withMessages($messages);
        return $validator->validate();
    }

    /**
     * Get validation errors
     * 
     * @return array The validation errors
     */
    protected function getValidationErrors(): array
    {
        return $this->validator->getErrors();
    }

    /**
     * Set flash data
     * 
     * @param string $key The flash key
     * @param mixed $value The value to flash
     */
    protected function flash(string $key, mixed $value): void
    {
        $this->session->flash($key, $value);
    }

    /**
     * Get flash data
     * 
     * @param string $key The flash key
     * @param mixed $default The default value
     * @return mixed The flash value
     */
    protected function getFlash(string $key, mixed $default = null): mixed
    {
        return $this->session->getFlash($key, $default);
    }

    /**
     * Check if flash data exists
     * 
     * @param string $key The flash key
     * @return bool True if flash data exists
     */
    protected function hasFlash(string $key): bool
    {
        return $this->session->hasFlash($key);
    }

    /**
     * Log an audit trail entry
     * 
     * @param string $action The action performed
     * @param string $entityType The entity type
     * @param int|string $entityId The entity ID
     * @param string|null $description The description
     * @param array $metadata The metadata
     */
    protected function audit(
        string $action,
        string $entityType,
        int|string $entityId,
        ?string $description = null,
        array $metadata = []
    ): void {
        $this->logger->audit($action, $entityType, $entityId, $description, $metadata);
    }

    /**
     * Check if the current user is authenticated
     * 
     * @return bool True if the user is authenticated
     */
    protected function isAuthenticated(): bool
    {
        return $this->auth->isAuthenticated();
    }

    /**
     * Check if the current user is an admin
     * 
     * @return bool True if the user is an admin
     */
    protected function isAdmin(): bool
    {
        return $this->auth->isAdmin();
    }

    /**
     * Check if the current user can perform an action
     * 
     * @param string $action The action to check
     * @param string|null $resource The resource type
     * @param int|null $resourceId The resource ID
     * @return bool True if the user can perform the action
     */
    protected function can(string $action, ?string $resource = null, ?int $resourceId = null): bool
    {
        return $this->auth->can($action, $resource, $resourceId);
    }

    /**
     * Get the authenticated user
     * 
     * @return array|null The user data or null if not authenticated
     */
    protected function getUser(): ?array
    {
        return $this->auth->getAuthenticatedUser();
    }

    /**
     * Get the authenticated user ID
     * 
     * @return int|null The user ID or null if not authenticated
     */
    protected function getUserId(): ?int
    {
        return $this->auth->getUserId();
    }

    /**
     * Send a JSON response
     * 
     * @param array|object $data The data to send
     * @param int $statusCode The HTTP status code
     */
    protected function json(array|object $data, int $statusCode = 200): void
    {
        $this->response->json($data, $statusCode);
    }

    /**
     * Send a success response
     * 
     * @param array|object $data The data to send
     * @param string|null $message The success message
     * @param int $statusCode The HTTP status code
     */
    protected function success(array|object $data = [], ?string $message = null, int $statusCode = 200): void
    {
        $this->response->success($data, $message, $statusCode);
    }

    /**
     * Send an error response
     * 
     * @param string $message The error message
     * @param array|object $data The error data
     * @param int $statusCode The HTTP status code
     */
    protected function error(string $message, array|object $data = [], int $statusCode = 400): void
    {
        $this->response->error($message, $data, $statusCode);
    }

    /**
     * Send a validation error response
     * 
     * @param array $errors The validation errors
     * @param int $statusCode The HTTP status code
     */
    protected function validationError(array $errors, int $statusCode = 422): void
    {
        $this->response->validationError($errors, $statusCode);
    }

    /**
     * Send a not found response
     * 
     * @param string|null $message The error message
     */
    protected function notFound(?string $message = null): void
    {
        $this->response->notFound($message);
    }

    /**
     * Send a forbidden response
     * 
     * @param string|null $message The error message
     */
    protected function forbidden(?string $message = null): void
    {
        $this->response->forbidden($message);
    }

    /**
     * Send a server error response
     * 
     * @param string|null $message The error message
     */
    protected function serverError(?string $message = null): void
    {
        $this->response->serverError($message);
    }
}
