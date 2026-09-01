<?php

declare(strict_types=1);

/**
 * Logs Module Routes
 */

use DDWB\Middleware\AuthMiddleware;
use DDWB\Middleware\AdminMiddleware;
use DDWB\Middleware\SessionMiddleware;
use DDWB\Middleware\CsrfMiddleware;

return [
    // Logs
    [
        'method' => 'GET',
        'path' => '/logs',
        'handler' => 'LogsController@index',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class],
        'name' => 'logs',
    ],
    [
        'method' => 'GET',
        'path' => '/logs/recent',
        'handler' => 'LogsController@recent',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'logs.recent',
    ],
    [
        'method' => 'GET',
        'path' => '/logs/today',
        'handler' => 'LogsController@today',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class],
        'name' => 'logs.today',
    ],
    [
        'method' => 'GET',
        'path' => '/logs/{id}',
        'handler' => 'LogsController@show',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class],
        'name' => 'logs.show',
    ],
    [
        'method' => 'GET',
        'path' => '/logs/user/{user_id}',
        'handler' => 'LogsController@byUser',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class],
        'name' => 'logs.by_user',
    ],
    [
        'method' => 'GET',
        'path' => '/logs/action/{action}',
        'handler' => 'LogsController@byAction',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class],
        'name' => 'logs.by_action',
    ],
    [
        'method' => 'POST',
        'path' => '/logs/clear-old',
        'handler' => 'LogsController@clearOld',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'logs.clear_old',
    ],
    [
        'method' => 'GET',
        'path' => '/logs/export',
        'handler' => 'LogsController@export',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class],
        'name' => 'logs.export',
    ],

    // API routes
    [
        'method' => 'GET',
        'path' => '/api/logs',
        'handler' => 'Api\LogsController@index',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class],
        'name' => 'api.logs.index',
    ],
    [
        'method' => 'POST',
        'path' => '/api/logs/search',
        'handler' => 'Api\LogsController@search',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class],
        'name' => 'api.logs.search',
    ],
    [
        'method' => 'GET',
        'path' => '/api/logs/{id}',
        'handler' => 'Api\LogsController@show',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class],
        'name' => 'api.logs.show',
    ],
];
