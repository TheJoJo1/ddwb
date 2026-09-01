<?php

declare(strict_types=1);

/**
 * Inventory Module Routes
 */

use DDWB\Middleware\AuthMiddleware;
use DDWB\Middleware\AdminMiddleware;
use DDWB\Middleware\SessionMiddleware;
use DDWB\Middleware\CsrfMiddleware;

return [
    // Categories
    [
        'method' => 'GET',
        'path' => '/categories',
        'handler' => 'CategoriesController@index',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'categories',
    ],
    [
        'method' => 'GET',
        'path' => '/categories/create',
        'handler' => 'CategoriesController@create',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'categories.create',
    ],
    [
        'method' => 'POST',
        'path' => '/categories',
        'handler' => 'CategoriesController@store',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'categories.store',
    ],
    [
        'method' => 'GET',
        'path' => '/categories/{id}',
        'handler' => 'CategoriesController@show',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'categories.show',
    ],
    [
        'method' => 'GET',
        'path' => '/categories/{id}/edit',
        'handler' => 'CategoriesController@edit',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'categories.edit',
    ],
    [
        'method' => 'POST',
        'path' => '/categories/{id}',
        'handler' => 'CategoriesController@update',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'categories.update',
    ],
    [
        'method' => 'POST',
        'path' => '/categories/{id}/delete',
        'handler' => 'CategoriesController@destroy',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'categories.delete',
    ],

    // Devices
    [
        'method' => 'GET',
        'path' => '/devices',
        'handler' => 'DevicesController@index',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'devices',
    ],
    [
        'method' => 'GET',
        'path' => '/devices/create',
        'handler' => 'DevicesController@create',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'devices.create',
    ],
    [
        'method' => 'POST',
        'path' => '/devices',
        'handler' => 'DevicesController@store',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'devices.store',
    ],
    [
        'method' => 'GET',
        'path' => '/devices/{id}',
        'handler' => 'DevicesController@show',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'devices.show',
    ],
    [
        'method' => 'GET',
        'path' => '/devices/{id}/edit',
        'handler' => 'DevicesController@edit',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'devices.edit',
    ],
    [
        'method' => 'POST',
        'path' => '/devices/{id}',
        'handler' => 'DevicesController@update',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'devices.update',
    ],
    [
        'method' => 'POST',
        'path' => '/devices/{id}/delete',
        'handler' => 'DevicesController@destroy',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'devices.delete',
    ],
    [
        'method' => 'GET',
        'path' => '/devices/{id}/qr',
        'handler' => 'DevicesController@qr',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'devices.qr',
    ],
    [
        'method' => 'GET',
        'path' => '/devices/{id}/barcode',
        'handler' => 'DevicesController@barcode',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'devices.barcode',
    ],
    [
        'method' => 'GET',
        'path' => '/devices/{id}/label',
        'handler' => 'DevicesController@label',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'devices.label',
    ],

    // API routes
    [
        'method' => 'GET',
        'path' => '/api/devices',
        'handler' => 'Api\DevicesController@index',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.devices.index',
    ],
    [
        'method' => 'POST',
        'path' => '/api/devices/search',
        'handler' => 'Api\DevicesController@search',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.devices.search',
    ],
    [
        'method' => 'GET',
        'path' => '/api/devices/{id}',
        'handler' => 'Api\DevicesController@show',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.devices.show',
    ],
];
