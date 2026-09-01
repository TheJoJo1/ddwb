<?php

declare(strict_types=1);

namespace DDWB;

/**
 * Logger
 * 
 * Simple file-based logger for DDWB
 */
final class Logger
{
    private string $logPath;
    private string $logFile;

    /**
     * Log levels
     */
    public const EMERGENCY = 'emergency';
    public const ALERT = 'alert';
    public const CRITICAL = 'critical';
    public const ERROR = 'error';
    public const WARNING = 'warning';
    public const NOTICE = 'notice';
    public const INFO = 'info';
    public const DEBUG = 'debug';

    /** @var array<string, int> */
    private static array $logLevels = [
        self::EMERGENCY => 0,
        self::ALERT => 1,
        self::CRITICAL => 2,
        self::ERROR => 3,
        self::WARNING => 4,
        self::NOTICE => 5,
        self::INFO => 6,
        self::DEBUG => 7,
    ];

    /**
     * Create a new Logger instance
     * 
     * @param string $logPath The path to the log directory
     */
    public function __construct(string $logPath)
    {
        $this->logPath = rtrim($logPath, DIRECTORY_SEPARATOR);
        $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . 'app-' . date('Y-m-d') . '.log';

        // Ensure log directory exists
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }

    /**
     * Log a message
     * 
     * @param string $level The log level
     * @param string $message The log message
     * @param array $context The context data
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $level = strtolower($level);

        if (!isset(self::$logLevels[$level])) {
            $level = self::INFO;
        }

        $this->writeLog($level, $message, $context);
    }

    /**
     * Log an emergency message
     * 
     * @param string $message The log message
     * @param array $context The context data
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->log(self::EMERGENCY, $message, $context);
    }

    /**
     * Log an alert message
     * 
     * @param string $message The log message
     * @param array $context The context data
     */
    public function alert(string $message, array $context = []): void
    {
        $this->log(self::ALERT, $message, $context);
    }

    /**
     * Log a critical message
     * 
     * @param string $message The log message
     * @param array $context The context data
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log(self::CRITICAL, $message, $context);
    }

    /**
     * Log an error message
     * 
     * @param string $message The log message
     * @param array $context The context data
     */
    public function error(string $message, array $context = []): void
    {
        $this->log(self::ERROR, $message, $context);
    }

    /**
     * Log a warning message
     * 
     * @param string $message The log message
     * @param array $context The context data
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log(self::WARNING, $message, $context);
    }

    /**
     * Log a notice message
     * 
     * @param string $message The log message
     * @param array $context The context data
     */
    public function notice(string $message, array $context = []): void
    {
        $this->log(self::NOTICE, $message, $context);
    }

    /**
     * Log an info message
     * 
     * @param string $message The log message
     * @param array $context The context data
     */
    public function info(string $message, array $context = []): void
    {
        $this->log(self::INFO, $message, $context);
    }

    /**
     * Log a debug message
     * 
     * @param string $message The log message
     * @param array $context The context data
     */
    public function debug(string $message, array $context = []): void
    {
        $app = Application::getInstance();
        $env = $app->getConfigValue('app.env', 'development');

        // Only log debug messages in development
        if ($env !== 'development') {
            return;
        }

        $this->log(self::DEBUG, $message, $context);
    }

    /**
     * Write a log entry
     * 
     * @param string $level The log level
     * @param string $message The log message
     * @param array $context The context data
     */
    private function writeLog(string $level, string $message, array $context): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $level = strtoupper($level);

        // Format the message with context
        $formattedMessage = $this->formatMessage($message, $context);

        // Format the log line
        $logLine = sprintf(
            "[%s] %s: %s\n",
            $timestamp,
            $level,
            $formattedMessage
        );

        // Write to log file
        file_put_contents(
            $this->logFile,
            $logLine,
            FILE_APPEND | LOCK_EX
        );
    }

    /**
     * Format a log message with context
     * 
     * @param string $message The log message
     * @param array $context The context data
     * @return string The formatted message
     */
    private function formatMessage(string $message, array $context): string
    {
        if (empty($context)) {
            return $message;
        }

        $formatted = $message;

        foreach ($context as $key => $value) {
            $placeholder = '{' . $key . '}';
            
            if (is_array($value) || is_object($value)) {
                $replacement = json_encode($value, JSON_UNESCAPED_SLASHES);
            } elseif (is_scalar($value)) {
                $replacement = (string)$value;
            } else {
                $replacement = '[complex value]';
            }

            $formatted = str_replace($placeholder, $replacement, $formatted);
        }

        if (str_contains($formatted, '{')) {
            // Append context that wasn't used in the message
            $formatted .= ' ' . json_encode($context, JSON_UNESCAPED_SLASHES);
        }

        return $formatted;
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
    public function audit(
        string $action,
        string $entityType,
        int|string $entityId,
        ?string $description = null,
        array $metadata = []
    ): void {
        $auth = Application::getInstance()->getContainer()->resolve(Auth::class);
        $userId = $auth->getUserId();

        $logEntry = [
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => (string)$entityId,
            'description' => $description,
            'metadata_json' => json_encode($metadata),
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        // Store in database if available
        try {
            $database = Application::getInstance()->getContainer()->resolve(Database::class);
            $database->insert('logs', $logEntry);
        } catch (\Exception $e) {
            // Fall back to file logging if database is not available
            $this->info(
                'Audit: {action} on {entity_type} #{entity_id} by user #{user_id}',
                array_merge($logEntry, ['error' => $e->getMessage()])
            );
        }

        // Also log to file
        $this->info(
            'Audit: {action} on {entity_type} #{entity_id} by user #{user_id}',
            $logEntry
        );
    }

    /**
     * Get the log file path
     * 
     * @return string The log file path
     */
    public function getLogFile(): string
    {
        return $this->logFile;
    }

    /**
     * Clear old log files
     * 
     * @param int $days The number of days to keep logs
     * @return int The number of files deleted
     */
    public function clearOldLogs(int $days = 30): int
    {
        $cutoff = time() - ($days * 24 * 60 * 60);
        $deleted = 0;

        $files = glob($this->logPath . DIRECTORY_SEPARATOR . '*.log');

        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
                $deleted++;
            }
        }

        return $deleted;
    }
}
