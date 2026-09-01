<?php

declare(strict_types=1);

/**
 * Maintenance Module Routes
 */

use DDWB\Middleware\AuthMiddleware;
use DDWB\Middleware\AdminMiddleware;
use DDWB\Middleware\SessionMiddleware;
use DDWB\Middleware\CsrfMiddleware;

return [
    // Maintenance
    [
        'method' => 'GET',
        'path' => '/maintenance',
        'handler' => 'MaintenanceController@index',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'maintenance',
    ],
    [
        'method' => 'GET',
        'path' => '/maintenance/create',
        'handler' => 'MaintenanceController@create',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'maintenance.create',
    ],
    [
        'method' => 'POST',
        'path' => '/maintenance',
        'handler' => 'MaintenanceController@store',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'maintenance.store',
    ],
    [
        'method' => 'GET',
        'path' => '/maintenance/{id}',
        'handler' => 'MaintenanceController@show',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'maintenance.show',
    ],
    [
        'method' => 'GET',
        'path' => '/maintenance/{id}/edit',
        'handler' => 'MaintenanceController@edit',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'maintenance.edit',
    ],
    [
        'method' => 'POST',
        'path' => '/maintenance/{id}',
        'handler' => 'MaintenanceController@update',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'maintenance.update',
    ],
    [
        'method' => 'POST',
        'path' => '/maintenance/{id}/delete',
        'handler' => 'MaintenanceController@destroy',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'maintenance.delete',
    ],
    [
        'method' => 'GET',
        'path' => '/maintenance/overdue',
        'handler' => 'MaintenanceController@overdue',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'maintenance.overdue',
    ],
    [
        'method' => 'GET',
        'path' => '/maintenance/upcoming',
        'handler' => 'MaintenanceController@upcoming',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'maintenance.upcoming',
    ],
    [
        'method' => 'POST',
        'path' => '/maintenance/update-statuses',
        'handler' => 'MaintenanceController@updateStatuses',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'maintenance.update_statuses',
    ],

    // API routes
    [
        'method' => 'GET',
        'path' => '/api/maintenance',
        'handler' => 'Api\MaintenanceController@index',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.maintenance.index',
    ],
    [
        'method' => 'POST',
        'path' => '/api/maintenance/search',
        'handler' => 'Api\MaintenanceController@search',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.maintenance.search',
    ],
    [
        'method' => 'GET',
        'path' => '/api/maintenance/{id}',
        'handler' => 'Api\MaintenanceController@show',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.maintenance.show',
    ],
];
