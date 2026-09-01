<?php

declare(strict_types=1);

namespace DDWB\Controllers;

use DDWB\Controller;
use DDWB\Auth;
use DDWB\Session;
use DDWB\Logger;

/**
 * Authentication Controller
 * 
 * Handles authentication-related requests
 */
final class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin(): void
    {
        // Redirect to dashboard if already authenticated
        if ($this->isAuthenticated()) {
            $redirect = $this->query('redirect', '');
            if (!empty($redirect)) {
                $this->redirect(urldecode($redirect));
            }
            $this->redirectToRoute('dashboard');
        }

        $this->view('auth/login', [
            'title' => 'Login',
            'errors' => $this->getFlash('errors', []),
            'old' => $this->getFlash('old', []),
        ]);
    }

    /**
     * Handle login submission
     */
    public function login(): void
    {
        $redirect = $this->post('redirect', '');
        $email = trim($this->post('email', ''));
        $password = $this->post('password', '');
        $remember = (bool)$this->post('remember', false);

        // Validate input
        $validator = $this->validator->withData([
            'email' => $email,
            'password' => $password,
        ])->withRules([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ])->withMessages([
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Please enter your password.',
            'password.min' => 'Password must be at least 8 characters long.',
        ]);

        if (!$validator->validate()) {
            $this->flash('errors', $validator->getErrors());
            $this->flash('old', ['email' => $email]);
            $this->redirectToRoute('login');
        }

        // Attempt authentication
        $auth = $this->getAuth();
        $authenticated = $auth->attempt($email, $password);

        if (!$authenticated) {
            $this->flash('errors', ['email' => 'Invalid email or password.']);
            $this->flash('old', ['email' => $email]);
            $this->redirectToRoute('login');
        }

        // Log successful login
        $user = $auth->getAuthenticatedUser();
        $this->getLogger()->audit(
            'login',
            'users',
            $user['id'] ?? 0,
            'User logged in',
            ['email' => $user['email'] ?? null]
        );

        // Redirect to intended page or dashboard
        if (!empty($redirect)) {
            $this->redirect(urldecode($redirect));
        }

        $this->redirectToRoute('dashboard');
    }

    /**
     * Handle logout
     */
    public function logout(): void
    {
        $auth = $this->getAuth();
        $userId = $auth->getUserId();

        // Log logout
        if ($userId !== null) {
            $this->getLogger()->audit(
                'logout',
                'users',
                $userId,
                'User logged out'
            );
        }

        // Perform logout
        $auth->logout();

        // Redirect to login page
        $this->redirectToRoute('login');
    }
}
