<?php

declare(strict_types=1);

namespace DDWB\Controllers;

use DDWB\Controller;

/**
 * Error Controller
 * 
 * Handles error pages
 */
final class ErrorController extends Controller
{
    /**
     * Display the 404 Not Found page
     */
    public function notFound(): void
    {
        $this->view('errors/404', [
            'title' => '404 Not Found',
            'message' => 'The page you are looking for could not be found.',
        ], null);
    }

    /**
     * Display the 403 Forbidden page
     */
    public function forbidden(): void
    {
        $this->view('errors/403', [
            'title' => '403 Forbidden',
            'message' => 'You do not have permission to access this page.',
        ], null);
    }

    /**
     * Display the 500 Server Error page
     */
    public function serverError(): void
    {
        $app = Application::getInstance();
        $env = $app->getConfigValue('app.env', 'development');

        if ($env === 'development') {
            $this->view('errors/500', [
                'title' => '500 Server Error',
                'message' => 'An unexpected error occurred.',
                'debug' => true,
            ], null);
        } else {
            $this->view('errors/500', [
                'title' => '500 Server Error',
                'message' => 'An unexpected error occurred. Please try again later.',
                'debug' => false,
            ], null);
        }
    }
}
