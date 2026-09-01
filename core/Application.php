<?php

declare(strict_types=1);

namespace DDWB;

use DDWB\Router;
use DDWB\Container;
use DDWB\Autoloader;

/**
 * DDWB Application
 * 
 * The main application class that bootstraps the system
 */
final class Application
{
    private static ?Application $instance = null;
    
    private Router $router;
    private Container $container;
    private array $config;
    private string $basePath;

    /**
     * Create a new Application instance
     * 
     * @param string $basePath The base path of the application
     */
    private function __construct(string $basePath)
    {
        $this->basePath = $basePath;
        $this->container = new Container();
        $this->router = new Router($this->container);
        $this->bootstrap();
    }

    /**
     * Get the singleton Application instance
     * 
     * @param string|null $basePath The base path of the application
     * @return Application The application instance
     */
    public static function getInstance(?string $basePath = null): Application
    {
        if (self::$instance === null) {
            $basePath = $basePath ?? dirname(__DIR__, 2);
            self::$instance = new self($basePath);
        }
        return self::$instance;
    }

    /**
     * Get the base path
     * 
     * @return string The base path
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Get the container
     * 
     * @return Container The dependency injection container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Get the router
     * 
     * @return Router The router instance
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * Get the configuration
     * 
     * @return array The configuration array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get a configuration value by key
     * 
     * @param string $key The configuration key
     * @param mixed|null $default The default value if key doesn't exist
     * @return mixed The configuration value
     */
    public function getConfigValue(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Bootstrap the application
     */
    private function bootstrap(): void
    {
        $this->registerAutoloader();
        $this->loadConfiguration();
        $this->registerServices();
        $this->registerRoutes();
    }

    /**
     * Register the PSR-4 autoloader
     */
    private function registerAutoloader(): void
    {
        Autoloader::register();
        
        // Register core namespace
        Autoloader::addNamespace('DDWB\\', $this->basePath . '/core');
        
        // Register modules namespace
        Autoloader::addNamespace('DDWB\\Modules\\', $this->basePath . '/modules');
    }

    /**
     * Load the application configuration
     */
    private function loadConfiguration(): void
    {
        $configPath = $this->basePath . '/config/config.php';
        $configDistPath = $this->basePath . '/config/config.php.dist';

        if (file_exists($configPath)) {
            $this->config = require $configPath;
        } elseif (file_exists($configDistPath)) {
            $this->config = require $configDistPath;
        } else {
            $this->config = [];
        }

        // Load app configuration
        $appConfigPath = $this->basePath . '/config/app.php';
        $appConfigDistPath = $this->basePath . '/config/app.php.dist';

        if (file_exists($appConfigPath)) {
            $appConfig = require $appConfigPath;
        } elseif (file_exists($appConfigDistPath)) {
            $appConfig = require $appConfigDistPath;
        } else {
            $appConfig = [];
        }

        // Merge configurations
        $this->config = array_merge_recursive($this->config, $appConfig);

        // Set timezone
        if (isset($this->config['app']['timezone'])) {
            date_default_timezone_set($this->config['app']['timezone']);
        }

        // Configure error handling based on environment
        $this->configureErrorHandling();
    }

    /**
     * Configure error handling based on environment
     */
    private function configureErrorHandling(): void
    {
        $env = $this->getConfigValue('app.env', 'development');
        $debug = $this->getConfigValue('app.debug', true);

        if ($env === 'production') {
            error_reporting(E_ALL);
            ini_set('display_errors', '0');
            ini_set('log_errors', '1');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', $debug ? '1' : '0');
            ini_set('log_errors', '1');
        }

        // Set custom error handler
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
    }

    /**
     * Handle PHP errors
     * 
     * @param int $errno The error number
     * @param string $errstr The error string
     * @param string $errfile The error file
     * @param int $errline The error line
     * @throws \ErrorException
     */
    public function handleError(int $errno, string $errstr, string $errfile, int $errline): void
    {
        if (!(error_reporting() & $errno)) {
            return;
        }

        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    /**
     * Handle uncaught exceptions
     * 
     * @param \Throwable $exception The exception
     */
    public function handleException(\Throwable $exception): void
    {
        $this->logError($exception);
        $this->renderException($exception);
    }

    /**
     * Log an exception
     * 
     * @param \Throwable $exception The exception
     */
    private function logError(\Throwable $exception): void
    {
        $logger = $this->container->resolve(Logger::class);
        $logger->error(
            'Uncaught exception: {message}',
            [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]
        );
    }

    /**
     * Render an exception
     * 
     * @param \Throwable $exception The exception
     */
    private function renderException(\Throwable $exception): void
    {
        $env = $this->getConfigValue('app.env', 'development');
        $debug = $this->getConfigValue('app.debug', true);

        if ($env === 'development' && $debug) {
            // Show detailed error in development
            echo "<h1>Application Error</h1>";
            echo "<p><strong>Type:</strong> " . htmlspecialchars(get_class($exception)) . "</p>";
            echo "<p><strong>Message:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>";
            echo "<p><strong>File:</strong> " . htmlspecialchars($exception->getFile()) . ":" . $exception->getLine() . "</p>";
            echo "<p><strong>Trace:</strong></p>";
            echo "<pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
        } else {
            // Show generic error in production
            http_response_code(500);
            echo "<h1>500 Internal Server Error</h1>";
            echo "<p>Something went wrong. Please try again later.</p>";
        }
    }

    /**
     * Register core services
     */
    private function registerServices(): void
    {
        $config = $this->config;
        $basePath = $this->basePath;

        // Register configuration
        $this->container->singleton('config', function () use ($config) {
            return $config;
        });

        // Register base path
        $this->container->singleton('base_path', function () use ($basePath) {
            return $basePath;
        });

        // Register database
        $this->container->singleton(Database::class, function () use ($config) {
            return new Database($config['database'] ?? []);
        });

        // Register logger
        $this->container->singleton(Logger::class, function () use ($config, $basePath) {
            $logPath = $config['storage']['logs'] ?? $basePath . '/storage/logs';
            return new Logger($logPath);
        });

        // Register session
        $this->container->singleton(Session::class, function () use ($config) {
            return new Session($config['session'] ?? []);
        });

        // Register CSRF
        $this->container->singleton(Csrf::class, function () use ($config) {
            return new Csrf($config['csrf'] ?? []);
        });

        // Register auth
        $this->container->singleton(Auth::class, function () use ($config) {
            return new Auth($config['session'] ?? []);
        });

        // Register validator
        $this->container->singleton(Validator::class, function () {
            return new Validator();
        });

        // Register response
        $this->container->singleton(Response::class, function () {
            return new Response();
        });
    }

    /**
     * Register routes from all modules
     */
    private function registerRoutes(): void
    {
        $modules = $this->getConfigValue('modules', []);
        $basePath = $this->basePath;

        foreach ($modules as $module) {
            $routesFile = $basePath . '/modules/' . $module . '/routes/web.php';
            
            if (file_exists($routesFile)) {
                $routes = require $routesFile;
                
                if (is_array($routes)) {
                    foreach ($routes as $route) {
                        $this->router->addRoute(
                            $route['method'] ?? 'GET',
                            $route['path'] ?? '',
                            $route['handler'] ?? '',
                            $route['middleware'] ?? [],
                            $route['name'] ?? null
                        );
                    }
                }
            }
        }

        // Register core routes
        $coreRoutesFile = $basePath . '/core/routes.php';
        if (file_exists($coreRoutesFile)) {
            $routes = require $coreRoutesFile;
            
            if (is_array($routes)) {
                foreach ($routes as $route) {
                    $this->router->addRoute(
                        $route['method'] ?? 'GET',
                        $route['path'] ?? '',
                        $route['handler'] ?? '',
                        $route['middleware'] ?? [],
                        $route['name'] ?? null
                    );
                }
            }
        }
    }

    /**
     * Run the application
     */
    public function run(): void
    {
        $this->router->dispatch();
    }
}
