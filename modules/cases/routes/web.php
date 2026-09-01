<?php

declare(strict_types=1);

/**
 * Cases Module Routes
 */

use DDWB\Middleware\AuthMiddleware;
use DDWB\Middleware\AdminMiddleware;
use DDWB\Middleware\SessionMiddleware;
use DDWB\Middleware\CsrfMiddleware;

return [
    // Cases
    [
        'method' => 'GET',
        'path' => '/cases',
        'handler' => 'CasesController@index',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'cases',
    ],
    [
        'method' => 'GET',
        'path' => '/cases/create',
        'handler' => 'CasesController@create',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'cases.create',
    ],
    [
        'method' => 'POST',
        'path' => '/cases',
        'handler' => 'CasesController@store',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'cases.store',
    ],
    [
        'method' => 'GET',
        'path' => '/cases/{id}',
        'handler' => 'CasesController@show',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'cases.show',
    ],
    [
        'method' => 'GET',
        'path' => '/cases/{id}/edit',
        'handler' => 'CasesController@edit',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'cases.edit',
    ],
    [
        'method' => 'POST',
        'path' => '/cases/{id}',
        'handler' => 'CasesController@update',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'cases.update',
    ],
    [
        'method' => 'POST',
        'path' => '/cases/{id}/delete',
        'handler' => 'CasesController@destroy',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'cases.delete',
    ],
    [
        'method' => 'POST',
        'path' => '/cases/{id}/add-device',
        'handler' => 'CasesController@addDevice',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'cases.add_device',
    ],
    [
        'method' => 'POST',
        'path' => '/cases/{id}/remove-device/{device_id}',
        'handler' => 'CasesController@removeDevice',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'cases.remove_device',
    ],
    [
        'method' => 'GET',
        'path' => '/cases/{id}/qr',
        'handler' => 'CasesController@qr',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'cases.qr',
    ],
    [
        'method' => 'GET',
        'path' => '/cases/{id}/barcode',
        'handler' => 'CasesController@barcode',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'cases.barcode',
    ],
    [
        'method' => 'GET',
        'path' => '/cases/{id}/label',
        'handler' => 'CasesController@label',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'cases.label',
    ],

    // API routes
    [
        'method' => 'GET',
        'path' => '/api/cases',
        'handler' => 'Api\CasesController@index',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.cases.index',
    ],
    [
        'method' => 'POST',
        'path' => '/api/cases/search',
        'handler' => 'Api\CasesController@search',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.cases.search',
    ],
    [
        'method' => 'GET',
        'path' => '/api/cases/{id}',
        'handler' => 'Api\CasesController@show',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.cases.show',
    ],
];
