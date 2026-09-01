<?php

declare(strict_types=1);

namespace DDWB;

/**
 * PSR-4 Autoloader for DDWB
 * 
 * This autoloader follows PSR-4 standards for class loading
 */
final class Autoloader
{
    /** @var array<string, string> */
    private static array $prefixes = [];
    
    /** @var bool */
    private static bool $registered = false;

    /**
     * Register the autoloader with PHP's SPL autoloader
     */
    public static function register(): void
    {
        if (self::$registered) {
            return;
        }

        spl_autoload_register([self::class, 'loadClass']);
        self::$registered = true;
    }

    /**
     * Unregister the autoloader
     */
    public static function unregister(): void
    {
        spl_autoload_unregister([self::class, 'loadClass']);
        self::$registered = false;
    }

    /**
     * Register a namespace prefix with its base directory
     * 
     * @param string $prefix The namespace prefix
     * @param string $baseDir The base directory for the prefix
     */
    public static function addNamespace(string $prefix, string $baseDir): void
    {
        // Normalize the prefix
        $prefix = trim($prefix, '\\') . '\\';
        
        // Normalize the base directory with a trailing separator
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        // Initialize the prefix array if it doesn't exist
        if (!isset(self::$prefixes[$prefix])) {
            self::$prefixes[$prefix] = [];
        }

        // Add the base directory to the prefix
        array_push(self::$prefixes[$prefix], $baseDir);
    }

    /**
     * Load the class file for a given class name
     * 
     * @param string $class The fully-qualified class name
     * @return bool True if the file was loaded, false otherwise
     */
    public static function loadClass(string $class): bool
    {
        // Find the file path for the class
        $file = self::findFile($class);

        if ($file === null) {
            return false;
        }

        require $file;
        return true;
    }

    /**
     * Find the file path for a given class name
     * 
     * @param string $class The fully-qualified class name
     * @return string|null The file path or null if not found
     */
    private static function findFile(string $class): ?string
    {
        // The current class uses the namespace prefix
        $prefix = $class;

        // Work backwards through the namespace prefix to find a match
        while (false !== $pos = strrpos($prefix, '\\')) {
            $prefix = substr($class, 0, $pos + 1);
            
            // Look for a registered prefix
            if (isset(self::$prefixes[$prefix])) {
                $relativeClass = substr($class, $pos + 1);
                $file = self::findFileInPaths($prefix, $relativeClass);
                
                if ($file !== null) {
                    return $file;
                }
            }
        }

        return null;
    }

    /**
     * Find the file in the registered paths for a prefix
     * 
     * @param string $prefix The namespace prefix
     * @param string $relativeClass The relative class name
     * @return string|null The file path or null if not found
     */
    private static function findFileInPaths(string $prefix, string $relativeClass): ?string
    {
        foreach (self::$prefixes[$prefix] as $baseDir) {
            $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
            
            if (file_exists($file)) {
                return $file;
            }
        }

        return null;
    }
}
