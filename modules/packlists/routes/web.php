<?php

declare(strict_types=1);

/**
 * Packlists Module Routes
 */

use DDWB\Middleware\AuthMiddleware;
use DDWB\Middleware\AdminMiddleware;
use DDWB\Middleware\SessionMiddleware;
use DDWB\Middleware\CsrfMiddleware;

return [
    // Packlists
    [
        'method' => 'GET',
        'path' => '/packlists',
        'handler' => 'PacklistsController@index',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'packlists',
    ],
    [
        'method' => 'GET',
        'path' => '/packlists/create',
        'handler' => 'PacklistsController@create',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'packlists.create',
    ],
    [
        'method' => 'POST',
        'path' => '/packlists',
        'handler' => 'PacklistsController@store',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'packlists.store',
    ],
    [
        'method' => 'GET',
        'path' => '/packlists/{id}',
        'handler' => 'PacklistsController@show',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'packlists.show',
    ],
    [
        'method' => 'GET',
        'path' => '/packlists/{id}/edit',
        'handler' => 'PacklistsController@edit',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'packlists.edit',
    ],
    [
        'method' => 'POST',
        'path' => '/packlists/{id}',
        'handler' => 'PacklistsController@update',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'packlists.update',
    ],
    [
        'method' => 'POST',
        'path' => '/packlists/{id}/delete',
        'handler' => 'PacklistsController@destroy',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'packlists.delete',
    ],
    [
        'method' => 'POST',
        'path' => '/packlists/{id}/add-item',
        'handler' => 'PacklistsController@addItem',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'packlists.add_item',
    ],
    [
        'method' => 'POST',
        'path' => '/packlists/{id}/remove-item/{item_id}',
        'handler' => 'PacklistsController@removeItem',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'packlists.remove_item',
    ],
    [
        'method' => 'POST',
        'path' => '/packlists/{id}/toggle-item/{item_id}',
        'handler' => 'PacklistsController@toggleItem',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, CsrfMiddleware::class],
        'name' => 'packlists.toggle_item',
    ],
    [
        'method' => 'POST',
        'path' => '/packlists/{id}/reorder-items',
        'handler' => 'PacklistsController@reorderItems',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'packlists.reorder_items',
    ],
    [
        'method' => 'GET',
        'path' => '/packlists/{id}/print',
        'handler' => 'PacklistsController@print',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'packlists.print',
    ],
    [
        'method' => 'GET',
        'path' => '/packlists/{id}/pdf',
        'handler' => 'PacklistsController@exportPdf',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'packlists.pdf',
    ],

    // API routes
    [
        'method' => 'GET',
        'path' => '/api/packlists',
        'handler' => 'Api\PacklistsController@index',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.packlists.index',
    ],
    [
        'method' => 'POST',
        'path' => '/api/packlists/search',
        'handler' => 'Api\PacklistsController@search',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.packlists.search',
    ],
    [
        'method' => 'GET',
        'path' => '/api/packlists/{id}',
        'handler' => 'Api\PacklistsController@show',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.packlists.show',
    ],
    [
        'method' => 'GET',
        'path' => '/api/packlists/{id}/pdf',
        'handler' => 'Api\PacklistsController@pdf',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.packlists.pdf',
    ],
    [
        'method' => 'POST',
        'path' => '/api/packlists/{id}/items',
        'handler' => 'Api\PacklistsController@items',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.packlists.items',
    ],
];
