<?php

declare(strict_types=1);

namespace DDWB;

/**
 * Session
 * 
 * Wrapper for PHP session management
 */
final class Session
{
    private array $config;
    private bool $started = false;

    /**
     * Create a new Session instance
     * 
     * @param array $config The session configuration
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Start the session
     */
    public function start(): void
    {
        if ($this->started) {
            return;
        }

        // Set session configuration
        $options = $this->getSessionOptions();

        foreach ($options as $key => $value) {
            ini_set('session.' . $key, (string)$value);
        }

        // Set custom session name if configured
        if (!empty($this->config['name'])) {
            session_name($this->config['name']);
        }

        // Set session cookie parameters
        $cookieParams = [
            'lifetime' => $this->config['lifetime'] ?? 0,
            'path' => $this->config['path'] ?? '/',
            'domain' => $this->config['domain'] ?? '',
            'secure' => (bool)($this->config['secure'] ?? false),
            'httponly' => (bool)($this->config['httponly'] ?? true),
            'samesite' => $this->config['samesite'] ?? 'Lax',
        ];

        session_set_cookie_params($cookieParams);

        // Start the session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->started = true;
    }

    /**
     * Get session options
     * 
     * @return array The session options
     */
    private function getSessionOptions(): array
    {
        $app = Application::getInstance();
        $env = $app->getConfigValue('app.env', 'development');

        $options = [
            'use_strict_mode' => 1,
            'use_cookies' => 1,
            'cookie_httponly' => 1,
            'cookie_secure' => $env === 'production' ? 1 : 0,
            'use_only_cookies' => 1,
            'cache_limiter' => 'nocache',
        ];

        if (!empty($this->config['lifetime'])) {
            $options['gc_maxlifetime'] = $this->config['lifetime'];
            $options['cookie_lifetime'] = $this->config['lifetime'];
        }

        return $options;
    }

    /**
     * Check if the session is started
     * 
     * @return bool True if the session is started
     */
    public function isStarted(): bool
    {
        return $this->started && session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * Get a session value
     * 
     * @param string $key The session key
     * @param mixed $default The default value if key doesn't exist
     * @return mixed The session value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $this->ensureStarted();

        return $_SESSION[$key] ?? $default;
    }

    /**
     * Set a session value
     * 
     * @param string $key The session key
     * @param mixed $value The value to set
     */
    public function set(string $key, mixed $value): void
    {
        $this->ensureStarted();
        $_SESSION[$key] = $value;
    }

    /**
     * Check if a session key exists
     * 
     * @param string $key The session key
     * @return bool True if the key exists
     */
    public function has(string $key): bool
    {
        $this->ensureStarted();
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session value
     * 
     * @param string $key The session key
     */
    public function remove(string $key): void
    {
        $this->ensureStarted();
        unset($_SESSION[$key]);
    }

    /**
     * Clear all session data
     */
    public function clear(): void
    {
        $this->ensureStarted();
        $_SESSION = [];
    }

    /**
     * Destroy the session
     */
    public function destroy(): void
    {
        if ($this->started) {
            $this->clear();
            session_destroy();
            $this->started = false;
        }
    }

    /**
     * Regenerate the session ID
     * 
     * @param bool $deleteOldSession Whether to delete the old session
     */
    public function regenerate(bool $deleteOldSession = true): void
    {
        $this->ensureStarted();
        session_regenerate_id($deleteOldSession);
    }

    /**
     * Get the session ID
     * 
     * @return string The session ID
     */
    public function getId(): string
    {
        $this->ensureStarted();
        return session_id();
    }

    /**
     * Ensure the session is started
     * 
     * @throws \RuntimeException If the session cannot be started
     */
    private function ensureStarted(): void
    {
        if (!$this->started) {
            $this->start();
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new \RuntimeException('Session is not active');
        }
    }

    /**
     * Get all session data
     * 
     * @return array The session data
     */
    public function all(): array
    {
        $this->ensureStarted();
        return $_SESSION;
    }

    /**
     * Set flash data (data that will be available only for the next request)
     * 
     * @param string $key The flash key
     * @param mixed $value The value to flash
     */
    public function flash(string $key, mixed $value): void
    {
        $this->ensureStarted();
        $_SESSION['__flash__'][$key] = $value;
    }

    /**
     * Get flash data
     * 
     * @param string $key The flash key
     * @param mixed $default The default value if key doesn't exist
     * @return mixed The flash value
     */
    public function getFlash(string $key, mixed $default = null): mixed
    {
        $this->ensureStarted();

        if (isset($_SESSION['__flash__'][$key])) {
            $value = $_SESSION['__flash__'][$key];
            unset($_SESSION['__flash__'][$key]);
            return $value;
        }

        return $default;
    }

    /**
     * Check if flash data exists
     * 
     * @param string $key The flash key
     * @return bool True if flash data exists
     */
    public function hasFlash(string $key): bool
    {
        $this->ensureStarted();
        return isset($_SESSION['__flash__'][$key]);
    }

    /**
     * Get all flash data and clear it
     * 
     * @return array The flash data
     */
    public function getAllFlash(): array
    {
        $this->ensureStarted();

        $flash = $_SESSION['__flash__'] ?? [];
        unset($_SESSION['__flash__']);
        return $flash;
    }

    /**
     * Re-generate flash data for the next request
     * 
     * This is useful when you want flash data to persist across redirects
     */
    public function reflash(): void
    {
        $this->ensureStarted();

        if (isset($_SESSION['__flash__'])) {
            $_SESSION['__flash__'] = $_SESSION['__flash__'];
        }
    }
}
