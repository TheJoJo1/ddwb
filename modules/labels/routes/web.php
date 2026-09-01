<?php

declare(strict_types=1);

/**
 * Labels Module Routes
 */

use DDWB\Middleware\AuthMiddleware;
use DDWB\Middleware\AdminMiddleware;
use DDWB\Middleware\SessionMiddleware;
use DDWB\Middleware\CsrfMiddleware;

return [
    // Label Templates
    [
        'method' => 'GET',
        'path' => '/labels/templates',
        'handler' => 'LabelsController@index',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'labels.templates',
    ],
    [
        'method' => 'GET',
        'path' => '/labels/templates/create',
        'handler' => 'LabelsController@create',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'labels.templates.create',
    ],
    [
        'method' => 'POST',
        'path' => '/labels/templates',
        'handler' => 'LabelsController@store',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'labels.templates.store',
    ],
    [
        'method' => 'GET',
        'path' => '/labels/templates/{id}',
        'handler' => 'LabelsController@showTemplate',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'labels.templates.show',
    ],
    [
        'method' => 'GET',
        'path' => '/labels/templates/{id}/edit',
        'handler' => 'LabelsController@editTemplate',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'labels.templates.edit',
    ],
    [
        'method' => 'POST',
        'path' => '/labels/templates/{id}',
        'handler' => 'LabelsController@updateTemplate',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'labels.templates.update',
    ],
    [
        'method' => 'POST',
        'path' => '/labels/templates/{id}/delete',
        'handler' => 'LabelsController@destroyTemplate',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'labels.templates.delete',
    ],
    [
        'method' => 'POST',
        'path' => '/labels/templates/create-standard',
        'handler' => 'LabelsController@createStandardTemplates',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class],
        'name' => 'labels.templates.create_standard',
    ],

    // Label Designer
    [
        'method' => 'GET',
        'path' => '/labels/designer',
        'handler' => 'LabelsController@designer',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'labels.designer',
    ],
    [
        'method' => 'POST',
        'path' => '/labels/generate',
        'handler' => 'LabelsController@generate',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class, CsrfMiddleware::class],
        'name' => 'labels.generate',
    ],
    [
        'method' => 'GET',
        'path' => '/labels/generate-pdf',
        'handler' => 'LabelsController@generatePdf',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'labels.generate_pdf',
    ],

    // API routes
    [
        'method' => 'GET',
        'path' => '/api/labels/templates',
        'handler' => 'Api\LabelsController@templates',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.labels.templates',
    ],
    [
        'method' => 'POST',
        'path' => '/api/labels/generate',
        'handler' => 'Api\LabelsController@generate',
        'middleware' => [SessionMiddleware::class, AuthMiddleware::class],
        'name' => 'api.labels.generate',
    ],
];
