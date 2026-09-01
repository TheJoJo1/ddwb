<?php

declare(strict_types=1);

namespace DDWB;

/**
 * CSRF Protection
 * 
 * Cross-Site Request Forgery protection using tokens
 */
final class Csrf
{
    private array $config;
    private Session $session;

    /**
     * Create a new Csrf instance
     * 
     * @param array $config The CSRF configuration
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->session = Application::getInstance()->getContainer()->resolve(Session::class);
    }

    /**
     * Get the token name
     * 
     * @return string The token name
     */
    public function getTokenName(): string
    {
        return $this->config['token_name'] ?? 'csrf_token';
    }

    /**
     * Get the token value
     * 
     * @return string The CSRF token
     */
    public function getToken(): string
    {
        $token = $this->session->get($this->getTokenName());

        if ($token === null || $this->isTokenExpired()) {
            $this->generateToken();
            $token = $this->session->get($this->getTokenName());
        }

        return $token ?? '';
    }

    /**
     * Generate a new CSRF token
     * 
     * @return string The new token
     */
    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + ($this->config['token_lifetime'] ?? 3600);

        $this->session->set($this->getTokenName(), $token);
        $this->session->set($this->getTokenName() . '_expiry', $expiry);

        return $token;
    }

    /**
     * Check if the token is expired
     * 
     * @return bool True if the token is expired
     */
    private function isTokenExpired(): bool
    {
        $expiry = $this->session->get($this->getTokenName() . '_expiry');

        if ($expiry === null) {
            return true;
        }

        return time() > (int)$expiry;
    }

    /**
     * Validate the CSRF token
     * 
     * @param string|null $token The token to validate (defaults to POST data)
     * @return bool True if the token is valid
     */
    public function validateToken(?string $token = null): bool
    {
        if ($token === null) {
            $token = $this->getTokenFromRequest();
        }

        if ($token === null) {
            return false;
        }

        $storedToken = $this->session->get($this->getTokenName());

        if ($storedToken === null) {
            return false;
        }

        return hash_equals($storedToken, $token);
    }

    /**
     * Get the token from the request
     * 
     * @return string|null The token from the request or null if not found
     */
    private function getTokenFromRequest(): ?string
    {
        $tokenName = $this->getTokenName();

        // Check POST data
        if (isset($_POST[$tokenName])) {
            return $_POST[$tokenName];
        }

        // Check GET data
        if (isset($_GET[$tokenName])) {
            return $_GET[$tokenName];
        }

        // Check headers (for AJAX requests)
        $headerName = strtoupper(str_replace('_', '-', $tokenName));
        $headerValue = $_SERVER['HTTP_' . $headerName] ?? null;

        if ($headerValue !== null) {
            return $headerValue;
        }

        return null;
    }

    /**
     * Get the CSRF token as a hidden input field
     * 
     * @return string The hidden input HTML
     */
    public function getTokenField(): string
    {
        return sprintf(
            '<input type="hidden" name="%s" value="%s" />',
            htmlspecialchars($this->getTokenName(), ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($this->getToken(), ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Get the CSRF token as a meta tag
     * 
     * @return string The meta tag HTML
     */
    public function getTokenMeta(): string
    {
        return sprintf(
            '<meta name="%s" content="%s" />',
            htmlspecialchars($this->getTokenName(), ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($this->getToken(), ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Clear the CSRF token
     */
    public function clearToken(): void
    {
        $this->session->remove($this->getTokenName());
        $this->session->remove($this->getTokenName() . '_expiry');
    }

    /**
     * Get the token lifetime in seconds
     * 
     * @return int The token lifetime
     */
    public function getTokenLifetime(): int
    {
        return $this->config['token_lifetime'] ?? 3600;
    }
}
