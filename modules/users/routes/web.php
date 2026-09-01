<?php

declare(strict_types=1);

/**
 * Users Module Routes
 */

use DDWB\Middleware\AuthMiddleware;
use DDWB\Middleware\AdminMiddleware;
use DDWB\Middleware\SessionMiddleware;
use DDWB\Middleware\CsrfMiddleware;

return [
    // Login routes (already in core/routes.php)
    // These are here for completeness but handled by core

    // User management (Admin only)
    [
        'method' => 'GET',
        'path' => '/admin/users',
        'handler' => 'UsersController@index',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class],
        'name' => 'admin.users',
    ],
    [
        'method' => 'GET',
        'path' => '/admin/users/create',
        'handler' => 'UsersController@create',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'admin.users.create',
    ],
    [
        'method' => 'POST',
        'path' => '/admin/users',
        'handler' => 'UsersController@store',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'admin.users.store',
    ],
    [
        'method' => 'GET',
        'path' => '/admin/users/{id}',
        'handler' => 'UsersController@show',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class],
        'name' => 'admin.users.show',
    ],
    [
        'method' => 'GET',
        'path' => '/admin/users/{id}/edit',
        'handler' => 'UsersController@edit',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'admin.users.edit',
    ],
    [
        'method' => 'POST',
        'path' => '/admin/users/{id}',
        'handler' => 'UsersController@update',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'admin.users.update',
    ],
    [
        'method' => 'POST',
        'path' => '/admin/users/{id}/delete',
        'handler' => 'UsersController@destroy',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'admin.users.delete',
    ],
    [
        'method' => 'POST',
        'path' => '/admin/users/{id}/toggle-active',
        'handler' => 'UsersController@toggleActive',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'admin.users.toggle_active',
    ],
    [
        'method' => 'POST',
        'path' => '/admin/users/{id}/reset-password',
        'handler' => 'UsersController@resetPassword',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'admin.users.reset_password',
    ],

    // Profile routes (for all authenticated users)
    [
        'method' => 'GET',
        'path' => '/profile',
        'handler' => 'ProfileController@show',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'profile',
    ],
    [
        'method' => 'GET',
        'path' => '/profile/edit',
        'handler' => 'ProfileController@edit',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, CsrfMiddleware::class],
        'name' => 'profile.edit',
    ],
    [
        'method' => 'POST',
        'path' => '/profile',
        'handler' => 'ProfileController@update',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, CsrfMiddleware::class],
        'name' => 'profile.update',
    ],
    [
        'method' => 'POST',
        'path' => '/profile/change-password',
        'handler' => 'ProfileController@changePassword',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, CsrfMiddleware::class],
        'name' => 'profile.change_password',
    ],

    // API routes
    [
        'method' => 'GET',
        'path' => '/api/users',
        'handler' => 'Api\UsersController@index',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class],
        'name' => 'api.users.index',
    ],
    [
        'method' => 'POST',
        'path' => '/api/users/search',
        'handler' => 'Api\UsersController@search',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class],
        'name' => 'api.users.search',
    ],
];
