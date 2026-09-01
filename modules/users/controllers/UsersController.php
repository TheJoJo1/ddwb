<?php

declare(strict_types=1);

namespace DDWB\Modules\Users\Controllers;

use DDWB\Controller;
use DDWB\Modules\Users\Models\User as UserModel;
use DDWB\Validator;

/**
 * Users Controller
 * 
 * Handles user management for administrators
 */
final class UsersController extends Controller
{
    private UserModel $userModel;

    /**
     * Create a new UsersController instance
     */
    public function __construct()
    {
        parent::__construct($this->container);
        $this->userModel = new UserModel($this->getDatabase());
    }

    /**
     * Display the user list
     */
    public function index(): void
    {
        $this->ensureAdmin();

        // Get filter parameters
        $page = (int)$this->query('page', 1);
        $perPage = (int)$this->query('per_page', 25);
        $search = trim($this->query('search', ''));
        $role = $this->query('role', '');
        $active = $this->query('active', '');

        // Get filters
        $filters = [];
        if (!empty($search)) {
            $filters['search'] = $search;
        }
        if (!empty($role)) {
            $filters['role'] = $role;
        }
        if ($active !== '') {
            $filters['active'] = (int)$active;
        }

        // Get paginated users
        $result = $this->userModel->paginateUsers($page, $perPage, $filters);

        // Get statistics
        $stats = $this->userModel->getStatistics();

        $this->view('users/index', [
            'users' => $result['data'],
            'pagination' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'total_pages' => $result['total_pages'],
            ],
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'role' => $role,
                'active' => $active,
            ],
            'title' => 'Benutzerverwaltung',
        ]);
    }

    /**
     * Show the create user form
     */
    public function create(): void
    {
        $this->ensureAdmin();

        $this->view('users/create', [
            'title' => 'Benutzer erstellen',
            'roles' => ['user' => 'Benutzer', 'admin' => 'Administrator'],
        ]);
    }

    /**
     * Store a new user
     */
    public function store(): void
    {
        $this->ensureAdmin();

        $data = [
            'email' => trim($this->post('email', ''));
            'name' => trim($this->post('name', ''));
            'password' => $this->post('password', '');
            'password_confirmation' => $this->post('password_confirmation', '');
            'role' => $this->post('role', 'user');
            'active' => (bool)$this->post('active', true);
        ];

        // Validate input
        $validator = $this->validator->withData($data)->withRules([
            'email' => 'required|email|unique:users',
            'name' => 'required|min:2|max:100',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,user',
        ])->withMessages([
            'email.required' => 'Bitte geben Sie eine E-Mail-Adresse ein.',
            'email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
            'email.unique' => 'Diese E-Mail-Adresse ist bereits vergeben.',
            'name.required' => 'Bitte geben Sie einen Namen ein.',
            'name.min' => 'Der Name muss mindestens 2 Zeichen lang sein.',
            'name.max' => 'Der Name darf maximal 100 Zeichen lang sein.',
            'password.required' => 'Bitte geben Sie ein Passwort ein.',
            'password.min' => 'Das Passwort muss mindestens 8 Zeichen lang sein.',
            'password.confirmed' => 'Die Passwort-Bestätigung stimmt nicht überein.',
            'role.required' => 'Bitte wählen Sie eine Rolle aus.',
            'role.in' => 'Bitte wählen Sie eine gültige Rolle aus.',
        ]);

        if (!$validator->validate()) {
            $this->flash('errors', $validator->getErrors());
            $this->flash('old', $data);
            $this->redirectToRoute('admin.users.create');
        }

        // Create user
        try {
            $userId = $this->userModel->createUser($data);

            // Log the action
            $this->audit(
                'create',
                'users',
                $userId,
                'Benutzer erstellt: ' . $data['name'],
                ['email' => $data['email'], 'role' => $data['role']]
            );

            $this->flash('success', 'Benutzer erfolgreich erstellt.');
            $this->redirectToRoute('admin.users.show', ['id' => $userId]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to create user: {error}', ['error' => $e->getMessage()]);
            $this->flash('error', 'Fehler beim Erstellen des Benutzers: ' . $e->getMessage());
            $this->redirectToRoute('admin.users.create');
        }
    }

    /**
     * Show a user
     * 
     * @param int $id The user ID
     */
    public function show(int $id): void
    {
        $this->ensureAdmin();

        $user = $this->userModel->find($id);

        if ($user === null) {
            $this->flash('error', 'Benutzer nicht gefunden.');
            $this->redirectToRoute('admin.users');
        }

        $this->view('users/show', [
            'user' => $user,
            'title' => 'Benutzer: ' . e($user['name']),
        ]);
    }

    /**
     * Show the edit user form
     * 
     * @param int $id The user ID
     */
    public function edit(int $id): void
    {
        $this->ensureAdmin();

        $user = $this->userModel->find($id);

        if ($user === null) {
            $this->flash('error', 'Benutzer nicht gefunden.');
            $this->redirectToRoute('admin.users');
        }

        $this->view('users/edit', [
            'user' => $user,
            'roles' => ['user' => 'Benutzer', 'admin' => 'Administrator'],
            'title' => 'Benutzer bearbeiten: ' . e($user['name']),
        ]);
    }

    /**
     * Update a user
     * 
     * @param int $id The user ID
     */
    public function update(int $id): void
    {
        $this->ensureAdmin();

        $user = $this->userModel->find($id);

        if ($user === null) {
            $this->flash('error', 'Benutzer nicht gefunden.');
            $this->redirectToRoute('admin.users');
        }

        $data = [
            'email' => trim($this->post('email', ''));
            'name' => trim($this->post('name', ''));
            'role' => $this->post('role', 'user');
            'active' => (bool)$this->post('active', true);
        ];

        // Only require password if it's being changed
        $password = $this->post('password', '');
        $passwordConfirmation = $this->post('password_confirmation', '');

        // Validate input
        $rules = [
            'email' => 'required|email|unique:users,' . $id,
            'name' => 'required|min:2|max:100',
            'role' => 'required|in:admin,user',
        ];

        if (!empty($password)) {
            $rules['password'] = 'min:8|confirmed';
            $data['password'] = $password;
        }

        $validator = $this->validator->withData($data)->withRules($rules)->withMessages([
            'email.required' => 'Bitte geben Sie eine E-Mail-Adresse ein.',
            'email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
            'email.unique' => 'Diese E-Mail-Adresse ist bereits vergeben.',
            'name.required' => 'Bitte geben Sie einen Namen ein.',
            'name.min' => 'Der Name muss mindestens 2 Zeichen lang sein.',
            'name.max' => 'Der Name darf maximal 100 Zeichen lang sein.',
            'password.min' => 'Das Passwort muss mindestens 8 Zeichen lang sein.',
            'password.confirmed' => 'Die Passwort-Bestätigung stimmt nicht überein.',
            'role.required' => 'Bitte wählen Sie eine Rolle aus.',
            'role.in' => 'Bitte wählen Sie eine gültige Rolle aus.',
        ]);

        if (!$validator->validate()) {
            $this->flash('errors', $validator->getErrors());
            $this->flash('old', $data);
            $this->redirectToRoute('admin.users.edit', ['id' => $id]);
        }

        // Update user
        try {
            $this->userModel->updateUser($id, $data);

            // Log the action
            $this->audit(
                'update',
                'users',
                $id,
                'Benutzer aktualisiert: ' . $data['name'],
                ['email' => $data['email'], 'role' => $data['role']]
            );

            $this->flash('success', 'Benutzer erfolgreich aktualisiert.');
            $this->redirectToRoute('admin.users.show', ['id' => $id]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to update user: {error}', ['error' => $e->getMessage()]);
            $this->flash('error', 'Fehler beim Aktualisieren des Benutzers: ' . $e->getMessage());
            $this->redirectToRoute('admin.users.edit', ['id' => $id]);
        }
    }

    /**
     * Delete a user
     * 
     * @param int $id The user ID
     */
    public function destroy(int $id): void
    {
        $this->ensureAdmin();

        $user = $this->userModel->find($id);

        if ($user === null) {
            $this->flash('error', 'Benutzer nicht gefunden.');
            $this->redirectToRoute('admin.users');
        }

        // Prevent deletion of own account
        if ($id === $this->getUserId()) {
            $this->flash('error', 'Sie können Ihren eigenen Account nicht löschen.');
            $this->redirectToRoute('admin.users.show', ['id' => $id]);
        }

        // Prevent deletion of the last admin
        $adminCount = $this->userModel->database->count('users', ['role' => 'admin']);
        if ($user['role'] === 'admin' && $adminCount <= 1) {
            $this->flash('error', 'Sie können den letzten Administrator nicht löschen.');
            $this->redirectToRoute('admin.users.show', ['id' => $id]);
        }

        // Delete user
        try {
            $this->userModel->deleteUser($id);

            // Log the action
            $this->audit(
                'delete',
                'users',
                $id,
                'Benutzer gelöscht: ' . $user['name']
            );

            $this->flash('success', 'Benutzer erfolgreich gelöscht.');
        } catch (\Exception $e) {
            $this->logger->error('Failed to delete user: {error}', ['error' => $e->getMessage()]);
            $this->flash('error', 'Fehler beim Löschen des Benutzers: ' . $e->getMessage());
        }

        $this->redirectToRoute('admin.users');
    }

    /**
     * Toggle user active status
     * 
     * @param int $id The user ID
     */
    public function toggleActive(int $id): void
    {
        $this->ensureAdmin();

        $user = $this->userModel->find($id);

        if ($user === null) {
            $this->flash('error', 'Benutzer nicht gefunden.');
            $this->redirectToRoute('admin.users');
        }

        // Prevent deactivating own account
        if ($id === $this->getUserId() && $user['active']) {
            $this->flash('error', 'Sie können Ihren eigenen Account nicht deaktivieren.');
            $this->redirectToRoute('admin.users.show', ['id' => $id]);
        }

        // Prevent deactivating the last admin
        $adminCount = $this->userModel->database->count('users', ['role' => 'admin', 'active' => 1]);
        if ($user['role'] === 'admin' && $user['active'] && $adminCount <= 1) {
            $this->flash('error', 'Sie können den letzten aktiven Administrator nicht deaktivieren.');
            $this->redirectToRoute('admin.users.show', ['id' => $id]);
        }

        // Toggle active status
        try {
            $this->userModel->toggleActive($id);

            $newStatus = !$user['active'];
            $action = $newStatus ? 'activated' : 'deactivated';

            // Log the action
            $this->audit(
                $action,
                'users',
                $id,
                'Benutzer ' . ($newStatus ? 'aktiviert' : 'deaktiviert') . ': ' . $user['name']
            );

            $this->flash('success', 'Benutzer erfolgreich ' . ($newStatus ? 'aktiviert' : 'deaktiviert') . '.');
        } catch (\Exception $e) {
            $this->logger->error('Failed to toggle user active status: {error}', ['error' => $e->getMessage()]);
            $this->flash('error', 'Fehler beim Ändern des Benutzerstatus: ' . $e->getMessage());
        }

        $this->redirectToRoute('admin.users.show', ['id' => $id]);
    }

    /**
     * Reset user password
     * 
     * @param int $id The user ID
     */
    public function resetPassword(int $id): void
    {
        $this->ensureAdmin();

        $user = $this->userModel->find($id);

        if ($user === null) {
            $this->flash('error', 'Benutzer nicht gefunden.');
            $this->redirectToRoute('admin.users');
        }

        // Generate a new random password
        $newPassword = $this->generateRandomPassword();

        // Update password
        try {
            $this->userModel->resetPassword($id, $newPassword);

            // Log the action (without the password)
            $this->audit(
                'reset_password',
                'users',
                $id,
                'Passwort für Benutzer zurückgesetzt: ' . $user['name']
            );

            $this->flash('success', 'Passwort erfolgreich zurückgesetzt. Das neue Passwort wurde nicht gespeichert.');
        } catch (\Exception $e) {
            $this->logger->error('Failed to reset user password: {error}', ['error' => $e->getMessage()]);
            $this->flash('error', 'Fehler beim Zurücksetzen des Passworts: ' . $e->getMessage());
        }

        $this->redirectToRoute('admin.users.show', ['id' => $id]);
    }

    /**
     * Ensure the current user is an admin
     */
    private function ensureAdmin(): void
    {
        if (!$this->isAdmin()) {
            $this->forbidden('Keine Berechtigung für diese Aktion.');
        }
    }

    /**
     * Generate a random password
     * 
     * @return string The generated password
     */
    private function generateRandomPassword(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
        $password = '';
        $length = 12;

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $password;
    }
}
