<?php

declare(strict_types=1);

/**
 * Core Routes
 * 
 * These routes are registered by the core application
 */

use DDWB\Middleware\AuthMiddleware;
use DDWB\Middleware\AdminMiddleware;
use DDWB\Middleware\CsrfMiddleware;
use DDWB\Middleware\SessionMiddleware;

return [
    // Home / Dashboard
    [
        'method' => 'GET',
        'path' => '/',
        'handler' => 'DashboardController@index',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'home',
    ],
    [
        'method' => 'GET',
        'path' => '/dashboard',
        'handler' => 'DashboardController@index',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'dashboard',
    ],

    // Authentication
    [
        'method' => 'GET',
        'path' => '/login',
        'handler' => 'AuthController@showLogin',
        'middleware' => [SessionMiddleware::class, CsrfMiddleware::class],
        'name' => 'login',
    ],
    [
        'method' => 'POST',
        'path' => '/login',
        'handler' => 'AuthController@login',
        'middleware' => [SessionMiddleware::class, CsrfMiddleware::class],
        'name' => 'login.post',
    ],
    [
        'method' => 'POST',
        'path' => '/logout',
        'handler' => 'AuthController@logout',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, CsrfMiddleware::class],
        'name' => 'logout',
    ],

    // Scanner
    [
        'method' => 'GET',
        'path' => '/scanner',
        'handler' => 'ScannerController@showScanner',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'scanner',
    ],
    [
        'method' => 'GET',
        'path' => '/scan/{identifier}',
        'handler' => 'ScannerController@scan',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'scan',
    ],

    // API Routes
    [
        'method' => 'POST',
        'path' => '/api/scanner/resolve',
        'handler' => 'Api\ScannerController@resolve',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.scanner.resolve',
    ],

    // Error pages
    [
        'method' => 'GET',
        'path' => '/403',
        'handler' => 'ErrorController@forbidden',
        'middleware' => [SessionMiddleware::class],
        'name' => 'error.403',
    ],
    [
        'method' => 'GET',
        'path' => '/404',
        'handler' => 'ErrorController@notFound',
        'middleware' => [SessionMiddleware::class],
        'name' => 'error.404',
    ],
    [
        'method' => 'GET',
        'path' => '/500',
        'handler' => 'ErrorController@serverError',
        'middleware' => [SessionMiddleware::class],
        'name' => 'error.500',
    ],
];
