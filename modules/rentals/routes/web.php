<?php

declare(strict_types=1);

/**
 * Rentals Module Routes
 */

use DDWB\Middleware\AuthMiddleware;
use DDWB\Middleware\AdminMiddleware;
use DDWB\Middleware\SessionMiddleware;
use DDWB\Middleware\CsrfMiddleware;

return [
    // Rentals
    [
        'method' => 'GET',
        'path' => '/rentals',
        'handler' => 'RentalsController@index',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'rentals',
    ],
    [
        'method' => 'GET',
        'path' => '/rentals/create',
        'handler' => 'RentalsController@create',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'rentals.create',
    ],
    [
        'method' => 'POST',
        'path' => '/rentals',
        'handler' => 'RentalsController@store',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'rentals.store',
    ],
    [
        'method' => 'GET',
        'path' => '/rentals/{id}',
        'handler' => 'RentalsController@show',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'rentals.show',
    ],
    [
        'method' => 'GET',
        'path' => '/rentals/{id}/edit',
        'handler' => 'RentalsController@edit',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'rentals.edit',
    ],
    [
        'method' => 'POST',
        'path' => '/rentals/{id}',
        'handler' => 'RentalsController@update',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'rentals.update',
    ],
    [
        'method' => 'POST',
        'path' => '/rentals/{id}/return',
        'handler' => 'RentalsController@returnRental',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'rentals.return',
    ],
    [
        'method' => 'POST',
        'path' => '/rentals/{id}/extend',
        'handler' => 'RentalsController@extend',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'rentals.extend',
    ],
    [
        'method' => 'POST',
        'path' => '/rentals/{id}/delete',
        'handler' => 'RentalsController@destroy',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'rentals.delete',
    ],

    // API routes
    [
        'method' => 'GET',
        'path' => '/api/rentals',
        'handler' => 'Api\RentalsController@index',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.rentals.index',
    ],
    [
        'method' => 'POST',
        'path' => '/api/rentals/search',
        'handler' => 'Api\RentalsController@search',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.rentals.search',
    ],
    [
        'method' => 'GET',
        'path' => '/api/rentals/{id}',
        'handler' => 'Api\RentalsController@show',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.rentals.show',
    ],
    [
        'method' => 'POST',
        'path' => '/api/rentals/{id}/return',
        'handler' => 'Api\RentalsController@returnRental',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, CsrfMiddleware::class],
        'name' => 'api.rentals.return',
    ],
];
