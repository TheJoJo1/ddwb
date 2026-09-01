<?php

declare(strict_types=1);

namespace DDWB;

/**
 * Response
 * 
 * Handles HTTP response formatting and sending
 */
final class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private bool $sent = false;

    /**
     * Set the HTTP status code
     * 
     * @param int $code The HTTP status code
     * @return self
     */
    public function status(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Set a response header
     * 
     * @param string $name The header name
     * @param string $value The header value
     * @return self
     */
    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Set multiple response headers
     * 
     * @param array<string, string> $headers The headers to set
     * @return self
     */
    public function withHeaders(array $headers): self
    {
        foreach ($headers as $name => $value) {
            $this->header($name, $value);
        }
        return $this;
    }

    /**
     * Redirect to a URL
     * 
     * @param string $url The URL to redirect to
     * @param int $statusCode The HTTP status code (default: 302)
     */
    public function redirect(string $url, int $statusCode = 302): void
    {
        $this->status($statusCode);
        $this->header('Location', $url);
        $this->send();
        exit;
    }

    /**
     * Send a JSON response
     * 
     * @param array|object $data The data to send
     * @param int $statusCode The HTTP status code (default: 200)
     * @param int $options The JSON encode options (default: JSON_PRETTY_PRINT in development)
     */
    public function json(array|object $data, int $statusCode = 200, int $options = 0): void
    {
        $app = Application::getInstance();
        $env = $app->getConfigValue('app.env', 'development');
        $debug = $app->getConfigValue('app.debug', true);

        // Use pretty print in development
        if ($env === 'development' && $debug) {
            $options |= JSON_PRETTY_PRINT;
        }

        $this->status($statusCode);
        $this->header('Content-Type', 'application/json');
        $this->send($this->encodeJson($data, $options));
    }

    /**
     * Send a plain text response
     * 
     * @param string $content The content to send
     * @param int $statusCode The HTTP status code (default: 200)
     */
    public function text(string $content, int $statusCode = 200): void
    {
        $this->status($statusCode);
        $this->header('Content-Type', 'text/plain');
        $this->send($content);
    }

    /**
     * Send an HTML response
     * 
     * @param string $content The HTML content to send
     * @param int $statusCode The HTTP status code (default: 200)
     */
    public function html(string $content, int $statusCode = 200): void
    {
        $this->status($statusCode);
        $this->header('Content-Type', 'text/html; charset=utf-8');
        $this->send($content);
    }

    /**
     * Send a response with content
     * 
     * @param string $content The content to send
     */
    public function send(?string $content = null): void
    {
        if ($this->sent) {
            return;
        }

        // Set status code
        http_response_code($this->statusCode);

        // Set headers
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        // Send content if provided
        if ($content !== null) {
            echo $content;
        }

        $this->sent = true;
    }

    /**
     * Send a file for download
     * 
     * @param string $filePath The path to the file
     * @param string|null $filename The filename to send (defaults to basename of filePath)
     * @param string|null $mimeType The MIME type (auto-detected if not provided)
     */
    public function download(string $filePath, ?string $filename = null, ?string $mimeType = null): void
    {
        if (!file_exists($filePath)) {
            $this->status(404)->send('File not found');
            return;
        }

        $filename = $filename ?? basename($filePath);

        // Detect MIME type if not provided
        if ($mimeType === null) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($filePath);
        }

        $this->status(200);
        $this->header('Content-Type', $mimeType);
        $this->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $this->header('Content-Length', (string)filesize($filePath));
        $this->header('Cache-Control', 'private, no-cache, no-store, must-revalidate');
        $this->header('Pragma', 'no-cache');

        $this->send(file_get_contents($filePath));
        exit;
    }

    /**
     * Send a file inline
     * 
     * @param string $filePath The path to the file
     * @param string|null $mimeType The MIME type (auto-detected if not provided)
     */
    public function file(string $filePath, ?string $mimeType = null): void
    {
        if (!file_exists($filePath)) {
            $this->status(404)->send('File not found');
            return;
        }

        // Detect MIME type if not provided
        if ($mimeType === null) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($filePath);
        }

        $this->status(200);
        $this->header('Content-Type', $mimeType);
        $this->header('Content-Length', (string)filesize($filePath));

        $this->send(file_get_contents($filePath));
        exit;
    }

    /**
     * Send a PDF response
     * 
     * @param string $content The PDF content
     * @param string|null $filename The filename for download
     */
    public function pdf(string $content, ?string $filename = null): void
    {
        $this->status(200);
        $this->header('Content-Type', 'application/pdf');
        
        if ($filename !== null) {
            $this->header('Content-Disposition', 'inline; filename="' . $filename . '"');
        } else {
            $this->header('Content-Disposition', 'inline');
        }

        $this->header('Cache-Control', 'private, no-cache, no-store, must-revalidate');
        $this->send($content);
        exit;
    }

    /**
     * Encode data to JSON
     * 
     * @param array|object $data The data to encode
     * @param int $options The JSON encode options
     * @return string The JSON string
     */
    private function encodeJson(array|object $data, int $options): string
    {
        $json = json_encode($data, $options);

        if ($json === false) {
            $json = json_encode(['error' => 'Failed to encode JSON']);
        }

        return $json;
    }

    /**
     * Get the current status code
     * 
     * @return int The status code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get the headers
     * 
     * @return array The headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Check if the response has been sent
     * 
     * @return bool True if the response has been sent
     */
    public function isSent(): bool
    {
        return $this->sent;
    }

    /**
     * Send a success response
     * 
     * @param array|object $data The data to send
     * @param string|null $message The success message
     * @param int $statusCode The HTTP status code (default: 200)
     */
    public function success(array|object $data = [], ?string $message = null, int $statusCode = 200): void
    {
        $response = [
            'success' => true,
        ];

        if ($message !== null) {
            $response['message'] = $message;
        }

        if (!empty($data)) {
            $response['data'] = $data;
        }

        $this->json($response, $statusCode);
    }

    /**
     * Send an error response
     * 
     * @param string $message The error message
     * @param array|object $data The error data
     * @param int $statusCode The HTTP status code (default: 400)
     */
    public function error(string $message, array|object $data = [], int $statusCode = 400): void
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        $this->json($response, $statusCode);
    }

    /**
     * Send a validation error response
     * 
     * @param array $errors The validation errors
     * @param int $statusCode The HTTP status code (default: 422)
     */
    public function validationError(array $errors, int $statusCode = 422): void
    {
        $this->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors,
        ], $statusCode);
    }

    /**
     * Send a not found response
     * 
     * @param string|null $message The error message
     */
    public function notFound(?string $message = null): void
    {
        $this->json([
            'success' => false,
            'message' => $message ?? 'Resource not found',
        ], 404);
    }

    /**
     * Send a forbidden response
     * 
     * @param string|null $message The error message
     */
    public function forbidden(?string $message = null): void
    {
        $this->json([
            'success' => false,
            'message' => $message ?? 'Forbidden',
        ], 403);
    }

    /**
     * Send a server error response
     * 
     * @param string|null $message The error message
     */
    public function serverError(?string $message = null): void
    {
        $app = Application::getInstance();
        $env = $app->getConfigValue('app.env', 'development');

        if ($env === 'development') {
            $this->json([
                'success' => false,
                'message' => $message ?? 'Internal server error',
            ], 500);
        } else {
            $this->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }
    }
}
