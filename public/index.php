<?php

declare(strict_types=1);

/**
 * DDWB - DingeDieWirBesitzen
 * 
 * Main entry point for the application
 */

// Define the application root directory
define('DDWB_ROOT', dirname(__DIR__));

// Require the autoloader
require DDWB_ROOT . '/core/Autoloader.php';

// Require helper functions
require DDWB_ROOT . '/core/helpers.php';

// Register the autoloader
DDWB\Autoloader::register();

// Get the application instance
$app = DDWB\Application::getInstance(DDWB_ROOT);

// Run the application
$app->run();
