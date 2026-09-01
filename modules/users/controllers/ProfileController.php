<?php

declare(strict_types=1);

namespace DDWB\Modules\Users\Controllers;

use DDWB\Controller;
use DDWB\Modules\Users\Models\User as UserModel;

/**
 * Profile Controller
 * 
 * Handles user profile management
 */
final class ProfileController extends Controller
{
    private UserModel $userModel;

    /**
     * Create a new ProfileController instance
     */
    public function __construct()
    {
        parent::__construct($this->container);
        $this->userModel = new UserModel($this->getDatabase());
    }

    /**
     * Show the user profile
     */
    public function show(): void
    {
        $this->ensureAuthenticated();

        $user = $this->getUser();

        if ($user === null) {
            $this->redirectToRoute('login');
        }

        // Get user's activity
        $logs = $this->getDatabase()->select(
            'SELECT * FROM logs WHERE user_id = ? ORDER BY timestamp DESC LIMIT 10',
            [$user['id']]
        );

        $this->view('users/profile/show', [
            'user' => $user,
            'logs' => $logs,
            'title' => 'Mein Profil',
        ]);
    }

    /**
     * Show the edit profile form
     */
    public function edit(): void
    {
        $this->ensureAuthenticated();

        $user = $this->getUser();

        if ($user === null) {
            $this->redirectToRoute('login');
        }

        $this->view('users/profile/edit', [
            'user' => $user,
            'title' => 'Profil bearbeiten',
        ]);
    }

    /**
     * Update the user profile
     */
    public function update(): void
    {
        $this->ensureAuthenticated();

        $user = $this->getUser();

        if ($user === null) {
            $this->redirectToRoute('login');
        }

        $data = [
            'email' => trim($this->post('email', ''));
            'name' => trim($this->post('name', ''));
        ];

        // Validate input
        $validator = $this->validator->withData($data)->withRules([
            'email' => 'required|email|unique:users,' . $user['id'],
            'name' => 'required|min:2|max:100',
        ])->withMessages([
            'email.required' => 'Bitte geben Sie eine E-Mail-Adresse ein.',
            'email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
            'email.unique' => 'Diese E-Mail-Adresse ist bereits vergeben.',
            'name.required' => 'Bitte geben Sie einen Namen ein.',
            'name.min' => 'Der Name muss mindestens 2 Zeichen lang sein.',
            'name.max' => 'Der Name darf maximal 100 Zeichen lang sein.',
        ]);

        if (!$validator->validate()) {
            $this->flash('errors', $validator->getErrors());
            $this->flash('old', $data);
            $this->redirectToRoute('profile.edit');
        }

        // Update user
        try {
            $this->userModel->updateUser($user['id'], $data);

            // Update session data
            $this->getSession()->set('user_email', $data['email']);
            $this->getSession()->set('user_name', $data['name']);

            // Log the action
            $this->audit(
                'update',
                'users',
                $user['id'],
                'Profil aktualisiert',
                ['email' => $data['email'], 'name' => $data['name']]
            );

            $this->flash('success', 'Profil erfolgreich aktualisiert.');
            $this->redirectToRoute('profile');
        } catch (\Exception $e) {
            $this->logger->error('Failed to update profile: {error}', ['error' => $e->getMessage()]);
            $this->flash('error', 'Fehler beim Aktualisieren des Profils: ' . $e->getMessage());
            $this->redirectToRoute('profile.edit');
        }
    }

    /**
     * Show the change password form
     */
    public function changePasswordForm(): void
    {
        $this->ensureAuthenticated();

        $this->view('users/profile/change_password', [
            'title' => 'Passwort ändern',
        ]);
    }

    /**
     * Change the user password
     */
    public function changePassword(): void
    {
        $this->ensureAuthenticated();

        $user = $this->getUser();

        if ($user === null) {
            $this->redirectToRoute('login');
        }

        $currentPassword = $this->post('current_password', '');
        $newPassword = $this->post('new_password', '');
        $newPasswordConfirmation = $this->post('new_password_confirmation', '');

        // Validate input
        $validator = $this->validator->withData([
            'current_password' => $currentPassword,
            'new_password' => $newPassword,
            'new_password_confirmation' => $newPasswordConfirmation,
        ])->withRules([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ])->withMessages([
            'current_password.required' => 'Bitte geben Sie Ihr aktuelles Passwort ein.',
            'new_password.required' => 'Bitte geben Sie ein neues Passwort ein.',
            'new_password.min' => 'Das Passwort muss mindestens 8 Zeichen lang sein.',
            'new_password.confirmed' => 'Die Passwort-Bestätigung stimmt nicht überein.',
        ]);

        if (!$validator->validate()) {
            $this->flash('errors', $validator->getErrors());
            $this->redirectToRoute('profile.change_password');
        }

        // Verify current password
        $auth = $this->getAuth();
        if (!password_verify($currentPassword, $user['password_hash'])) {
            $this->flash('error', 'Das aktuelle Passwort ist falsch.');
            $this->redirectToRoute('profile.change_password');
        }

        // Update password
        try {
            $this->userModel->updatePassword($user['id'], $newPassword);

            // Log the action
            $this->audit(
                'change_password',
                'users',
                $user['id'],
                'Passwort geändert'
            );

            $this->flash('success', 'Passwort erfolgreich geändert.');
            $this->redirectToRoute('profile');
        } catch (\Exception $e) {
            $this->logger->error('Failed to change password: {error}', ['error' => $e->getMessage()]);
            $this->flash('error', 'Fehler beim Ändern des Passworts: ' . $e->getMessage());
            $this->redirectToRoute('profile.change_password');
        }
    }

    /**
     * Ensure the current user is authenticated
     */
    private function ensureAuthenticated(): void
    {
        if (!$this->isAuthenticated()) {
            $this->redirectToRoute('login');
        }
    }
}
