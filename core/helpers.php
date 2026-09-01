<?php

declare(strict_types=1);

/**
 * DDWB Helper Functions
 * 
 * Global helper functions for the DDWB application
 */

use DDWB\Application;
use DDWB\Container;
use DDWB\Router;
use DDWB\Session;
use DDWB\Auth;
use DDWB\Csrf;
use DDWB\Database;
use DDWB\Logger;
use DDWB\Validator;
use DDWB\Response;

if (!function_exists('dd')) {
    /**
     * Dump and die
     * 
     * @param mixed ...$args The arguments to dump
     */
    function dd(...$args): void
    {
        foreach ($args as $arg) {
            var_dump($arg);
        }
        die;
    }
}

if (!function_exists('dump')) {
    /**
     * Dump variables
     * 
     * @param mixed ...$args The arguments to dump
     */
    function dump(...$args): void
    {
        foreach ($args as $arg) {
            var_dump($arg);
        }
    }
}

if (!function_exists('app')) {
    /**
     * Get the Application instance
     * 
     * @return Application The application instance
     */
    function app(): Application
    {
        return Application::getInstance();
    }
}

if (!function_exists('container')) {
    /**
     * Get the Container instance
     * 
     * @return Container The dependency injection container
     */
    function container(): Container
    {
        return app()->getContainer();
    }
}

if (!function_exists('config')) {
    /**
     * Get a configuration value
     * 
     * @param string $key The configuration key (dot notation)
     * @param mixed $default The default value
     * @return mixed The configuration value
     */
    function config(string $key, mixed $default = null): mixed
    {
        return app()->getConfigValue($key, $default);
    }
}

if (!function_exists('router')) {
    /**
     * Get the Router instance
     * 
     * @return Router The router instance
     */
    function router(): Router
    {
        return app()->getRouter();
    }
}

if (!function_exists('session')) {
    /**
     * Get the Session instance
     * 
     * @return Session The session instance
     */
    function session(): Session
    {
        return container()->resolve(Session::class);
    }
}

if (!function_exists('auth')) {
    /**
     * Get the Auth instance
     * 
     * @return Auth The authentication instance
     */
    function auth(): Auth
    {
        return container()->resolve(Auth::class);
    }
}

if (!function_exists('csrf')) {
    /**
     * Get the CSRF instance
     * 
     * @return Csrf The CSRF protection instance
     */
    function csrf(): Csrf
    {
        return container()->resolve(Csrf::class);
    }
}

if (!function_exists('db')) {
    /**
     * Get the Database instance
     * 
     * @return Database The database instance
     */
    function db(): Database
    {
        return container()->resolve(Database::class);
    }
}

if (!function_exists('logger')) {
    /**
     * Get the Logger instance
     * 
     * @return Logger The logger instance
     */
    function logger(): Logger
    {
        return container()->resolve(Logger::class);
    }
}

if (!function_exists('validator')) {
    /**
     * Get the Validator instance
     * 
     * @param array $data The data to validate
     * @param array $rules The validation rules
     * @return Validator The validator instance
     */
    function validator(array $data = [], array $rules = []): Validator
    {
        return (new Validator($data, $rules));
    }
}

if (!function_exists('response')) {
    /**
     * Get the Response instance
     * 
     * @return Response The response instance
     */
    function response(): Response
    {
        return container()->resolve(Response::class);
    }
}

if (!function_exists('e')) {
    /**
     * Escape HTML output
     * 
     * @param string $value The value to escape
     * @return string The escaped value
     */
    function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8', false);
    }
}

if (!function_exists('esc_attr')) {
    /**
     * Escape HTML attribute value
     * 
     * @param string $value The value to escape
     * @return string The escaped value
     */
    function esc_attr(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8', true);
    }
}

if (!function_exists('esc_html')) {
    /**
     * Escape HTML content
     * 
     * @param string $value The value to escape
     * @return string The escaped value
     */
    function esc_html(string $value): string
    {
        return htmlspecialchars($value, ENT_NOQUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8', false);
    }
}

if (!function_exists('esc_js')) {
    /**
     * Escape JavaScript string
     * 
     * @param string $value The value to escape
     * @return string The escaped value
     */
    function esc_js(string $value): string
    {
        return json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    }
}

if (!function_exists('esc_url')) {
    /**
     * Escape URL
     * 
     * @param string $value The URL to escape
     * @return string The escaped URL
     */
    function esc_url(string $value): string
    {
        return filter_var($value, FILTER_SANITIZE_URL);
    }
}

if (!function_exists('route')) {
    /**
     * Generate a URL for a named route
     * 
     * @param string $name The route name
     * @param array $parameters The route parameters
     * @return string The generated URL
     */
    function route(string $name, array $parameters = []): string
    {
        return router()->route($name, $parameters);
    }
}

if (!function_exists('url')) {
    /**
     * Generate a URL
     * 
     * @param string $path The path
     * @return string The full URL
     */
    function url(string $path = ''): string
    {
        $baseUrl = router()->getBaseUrl();
        $path = ltrim($path, '/');
        
        if (empty($path)) {
            return $baseUrl;
        }

        return $baseUrl . '/' . $path;
    }
}

if (!function_exists('asset')) {
    /**
     * Generate an asset URL
     * 
     * @param string $path The asset path
     * @param string $base The base path (default: /assets)
     * @return string The asset URL
     */
    function asset(string $path, string $base = '/assets'): string
    {
        $path = ltrim($path, '/');
        return url($base . '/' . $path);
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Get a CSRF token hidden input field
     * 
     * @return string The hidden input HTML
     */
    function csrf_field(): string
    {
        return csrf()->getTokenField();
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Get the CSRF token value
     * 
     * @return string The CSRF token
     */
    function csrf_token(): string
    {
        return csrf()->getToken();
    }
}

if (!function_exists('csrf_meta')) {
    /**
     * Get a CSRF token meta tag
     * 
     * @return string The meta tag HTML
     */
    function csrf_meta(): string
    {
        return csrf()->getTokenMeta();
    }
}

if (!function_exists('is_active_route')) {
    /**
     * Check if a route is active
     * 
     * @param string $path The path to check
     * @param string $current The current path (defaults to current request URI)
     * @return bool True if the route is active
     */
    function is_active_route(string $path, string $current = ''): bool
    {
        if (empty($current)) {
            $current = router()->getRequestUri();
        }

        $path = ltrim($path, '/');
        $current = ltrim($current, '/');

        return str_starts_with($current, $path);
    }
}

if (!function_exists('request')) {
    /**
     * Get a request input value
     * 
     * @param string $key The input key
     * @param mixed $default The default value
     * @return mixed The input value
     */
    function request(string $key, mixed $default = null): mixed
    {
        return $_REQUEST[$key] ?? $default;
    }
}

if (!function_exists('get')) {
    /**
     * Get a GET input value
     * 
     * @param string $key The input key
     * @param mixed $default The default value
     * @return mixed The input value
     */
    function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }
}

if (!function_exists('post')) {
    /**
     * Get a POST input value
     * 
     * @param string $key The input key
     * @param mixed $default The default value
     * @return mixed The input value
     */
    function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }
}

if (!function_exists('server')) {
    /**
     * Get a server variable
     * 
     * @param string $key The server variable key
     * @param mixed $default The default value
     * @return mixed The server variable value
     */
    function server(string $key, mixed $default = null): mixed
    {
        return $_SERVER[$key] ?? $default;
    }
}

if (!function_exists('old')) {
    /**
     * Get old input data (from session flash)
     * 
     * @param string $key The input key
     * @param mixed $default The default value
     * @return mixed The old input value
     */
    function old(string $key, mixed $default = null): mixed
    {
        $session = session();
        
        if (!$session->isStarted()) {
            return $default;
        }

        $old = $session->getFlash('old', []);
        
        if (is_array($old) && isset($old[$key])) {
            return $old[$key];
        }

        return $default;
    }
}

if (!function_exists('has_old')) {
    /**
     * Check if old input data exists
     * 
     * @param string $key The input key
     * @return bool True if old input exists
     */
    function has_old(string $key): bool
    {
        $session = session();
        
        if (!$session->isStarted()) {
            return false;
        }

        $old = $session->getFlash('old', []);
        
        if (is_array($old)) {
            return isset($old[$key]);
        }

        return false;
    }
}

if (!function_exists('flash')) {
    /**
     * Set flash data
     * 
     * @param string $key The flash key
     * @param mixed $value The value to flash
     */
    function flash(string $key, mixed $value): void
    {
        session()->flash($key, $value);
    }
}

if (!function_exists('trans')) {
    /**
     * Translate a string (placeholder for future i18n)
     * 
     * @param string $key The translation key
     * @param array $replace The replacement values
     * @return string The translated string
     */
    function trans(string $key, array $replace = []): string
    {
        $translation = $key;
        
        foreach ($replace as $search => $replacement) {
            $translation = str_replace(':' . $search, $replacement, $translation);
        }

        return $translation;
    }
}

if (!function_exists('format_date')) {
    /**
     * Format a date
     * 
     * @param string|\DateTimeInterface $date The date to format
     * @param string $format The date format (default: Y-m-d H:i:s)
     * @return string The formatted date
     */
    function format_date(string|\DateTimeInterface $date, string $format = 'Y-m-d H:i:s'): string
    {
        if ($date instanceof \DateTimeInterface) {
            return $date->format($format);
        }

        if (is_string($date)) {
            $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $date);
            if ($dateTime !== false) {
                return $dateTime->format($format);
            }
        }

        return $date;
    }
}

if (!function_exists('format_bytes')) {
    /**
     * Format bytes to human-readable string
     * 
     * @param int $bytes The number of bytes
     * @param int $precision The number of decimal places (default: 2)
     * @return string The formatted size
     */
    function format_bytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('str_limit')) {
    /**
     * Limit a string to a specified length
     * 
     * @param string $string The string to limit
     * @param int $limit The maximum length
     * @param string $end The string to append if truncated (default: ...)
     * @return string The limited string
     */
    function str_limit(string $string, int $limit, string $end = '...'): string
    {
        if (mb_strlen($string) <= $limit) {
            return $string;
        }

        return mb_substr($string, 0, $limit - mb_strlen($end)) . $end;
    }
}

if (!function_exists('str_contains')) {
    /**
     * Check if a string contains another string
     * 
     * @param string $haystack The string to search in
     * @param string $needle The string to search for
     * @return bool True if the string contains the needle
     */
    function str_contains(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) !== false;
    }
}

if (!function_exists('str_starts_with')) {
    /**
     * Check if a string starts with another string
     * 
     * @param string $haystack The string to search in
     * @param string $needle The string to search for
     * @return bool True if the string starts with the needle
     */
    function str_starts_with(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) === 0;
    }
}

if (!function_exists('str_ends_with')) {
    /**
     * Check if a string ends with another string
     * 
     * @param string $haystack The string to search in
     * @param string $needle The string to search for
     * @return bool True if the string ends with the needle
     */
    function str_ends_with(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        return $length > 0 && substr($haystack, -$length) === $needle;
    }
}

if (!function_exists('array_get')) {
    /**
     * Get a value from an array using dot notation
     * 
     * @param array $array The array
     * @param string $key The key (dot notation)
     * @param mixed $default The default value
     * @return mixed The value
     */
    function array_get(array $array, string $key, mixed $default = null): mixed
    {
        if (!str_contains($key, '.')) {
            return $array[$key] ?? $default;
        }

        $keys = explode('.', $key);
        $value = $array;

        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }
}

if (!function_exists('array_set')) {
    /**
     * Set a value in an array using dot notation
     * 
     * @param array &$array The array (passed by reference)
     * @param string $key The key (dot notation)
     * @param mixed $value The value to set
     */
    function array_set(array &$array, string $key, mixed $value): void
    {
        if (!str_contains($key, '.')) {
            $array[$key] = $value;
            return;
        }

        $keys = explode('.', $key);
        $current = &$array;

        foreach ($keys as $k) {
            if (!isset($current[$k]) || !is_array($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }

        $current = $value;
    }
}

if (!function_exists('generate_id')) {
    /**
     * Generate a unique ID
     * 
     * @param string $prefix The prefix (default: '')
     * @param int $length The length of the random part (default: 12)
     * @return string The generated ID
     */
    function generate_id(string $prefix = '', int $length = 12): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $random = '';

        for ($i = 0; $i < $length; $i++) {
            $random .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $prefix . $random;
    }
}

if (!function_exists('generate_internal_id')) {
    /**
     * Generate an internal ID (e.g., DEV-0001, CASE-0001)
     * 
     * @param string $prefix The prefix (e.g., DEV, CASE)
     * @param int $number The number
     * @param int $padding The padding length (default: 4)
     * @return string The generated internal ID
     */
    function generate_internal_id(string $prefix, int $number, int $padding = 4): string
    {
        return strtoupper($prefix) . '-' . str_pad((string)$number, $padding, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('is_json')) {
    /**
     * Check if a string is valid JSON
     * 
     * @param string $string The string to check
     * @return bool True if the string is valid JSON
     */
    function is_json(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}

if (!function_exists('json_decode_assoc')) {
    /**
     * Decode JSON to associative array
     * 
     * @param string $json The JSON string
     * @return array The associative array
     */
    function json_decode_assoc(string $json): array
    {
        return json_decode($json, true) ?? [];
    }
}
