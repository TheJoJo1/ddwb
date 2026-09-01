<?php

declare(strict_types=1);

namespace DDWB\Modules\Users\Controllers\Api;

use DDWB\Controller;
use DDWB\Modules\Users\Models\User as UserModel;

/**
 * API Users Controller
 * 
 * Handles API requests for user management
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
     * Get all users
     */
    public function index(): void
    {
        $this->ensureAdmin();

        $page = (int)$this->query('page', 1);
        $perPage = (int)$this->query('per_page', 25);
        $search = trim($this->query('search', ''));
        $role = $this->query('role', '');
        $active = $this->query('active', '');

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

        $result = $this->userModel->paginateUsers($page, $perPage, $filters);

        $this->json([
            'success' => true,
            'data' => $result['data'],
            'pagination' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'total_pages' => $result['total_pages'],
            ],
        ]);
    }

    /**
     * Search users
     */
    public function search(): void
    {
        $this->ensureAdmin();

        $query = trim($this->post('query', ''));

        if (empty($query)) {
            $this->error('Query parameter is required', [], 400);
            return;
        }

        $users = $this->userModel->search($query);

        $this->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * Get a specific user
     * 
     * @param int $id The user ID
     */
    public function show(int $id): void
    {
        $this->ensureAdmin();

        $user = $this->userModel->find($id);

        if ($user === null) {
            $this->notFound('User not found');
            return;
        }

        // Remove sensitive data
        unset($user['password_hash']);

        $this->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /**
     * Create a new user
     */
    public function store(): void
    {
        $this->ensureAdmin();

        $data = [
            'email' => trim($this->post('email', ''));
            'name' => trim($this->post('name', ''));
            'password' => $this->post('password', '');
            'role' => $this->post('role', 'user');
            'active' => (bool)$this->post('active', true);
        ];

        // Validate input
        $validator = $this->validator->withData($data)->withRules([
            'email' => 'required|email|unique:users',
            'name' => 'required|min:2|max:100',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,user',
        ]);

        if (!$validator->validate()) {
            $this->validationError($validator->getErrors());
            return;
        }

        // Create user
        try {
            $userId = $this->userModel->createUser($data);
            $user = $this->userModel->find($userId);

            // Remove sensitive data
            unset($user['password_hash']);

            // Log the action
            $this->audit(
                'create',
                'users',
                $userId,
                'User created via API: ' . $data['name']
            );

            $this->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user,
            ], 201);
        } catch (\Exception $e) {
            $this->logger->error('Failed to create user via API: {error}', ['error' => $e->getMessage()]);
            $this->error('Failed to create user: ' . $e->getMessage());
        }
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
            $this->notFound('User not found');
            return;
        }

        $data = [
            'email' => trim($this->post('email', ''));
            'name' => trim($this->post('name', ''));
            'role' => $this->post('role', 'user');
            'active' => (bool)$this->post('active', true);
        ];

        // Only update password if provided
        $password = $this->post('password', '');
        if (!empty($password)) {
            $data['password'] = $password;
        }

        // Validate input
        $rules = [
            'email' => 'required|email|unique:users,' . $id,
            'name' => 'required|min:2|max:100',
            'role' => 'required|in:admin,user',
        ];

        if (!empty($password)) {
            $rules['password'] = 'min:8';
        }

        $validator = $this->validator->withData($data)->withRules($rules);

        if (!$validator->validate()) {
            $this->validationError($validator->getErrors());
            return;
        }

        // Update user
        try {
            $this->userModel->updateUser($id, $data);
            $updatedUser = $this->userModel->find($id);

            // Remove sensitive data
            unset($updatedUser['password_hash']);

            // Log the action
            $this->audit(
                'update',
                'users',
                $id,
                'User updated via API: ' . $data['name']
            );

            $this->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $updatedUser,
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to update user via API: {error}', ['error' => $e->getMessage()]);
            $this->error('Failed to update user: ' . $e->getMessage());
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
            $this->notFound('User not found');
            return;
        }

        // Prevent deletion of own account
        if ($id === $this->getUserId()) {
            $this->error('Cannot delete your own account', [], 403);
            return;
        }

        // Prevent deletion of the last admin
        $adminCount = $this->userModel->database->count('users', ['role' => 'admin']);
        if ($user['role'] === 'admin' && $adminCount <= 1) {
            $this->error('Cannot delete the last administrator', [], 403);
            return;
        }

        // Delete user
        try {
            $this->userModel->deleteUser($id);

            // Log the action
            $this->audit(
                'delete',
                'users',
                $id,
                'User deleted via API: ' . $user['name']
            );

            $this->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to delete user via API: {error}', ['error' => $e->getMessage()]);
            $this->error('Failed to delete user: ' . $e->getMessage());
        }
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
            $this->notFound('User not found');
            return;
        }

        // Prevent deactivating own account
        if ($id === $this->getUserId() && $user['active']) {
            $this->error('Cannot deactivate your own account', [], 403);
            return;
        }

        // Prevent deactivating the last admin
        $adminCount = $this->userModel->database->count('users', ['role' => 'admin', 'active' => 1]);
        if ($user['role'] === 'admin' && $user['active'] && $adminCount <= 1) {
            $this->error('Cannot deactivate the last active administrator', [], 403);
            return;
        }

        // Toggle active status
        try {
            $this->userModel->toggleActive($id);
            $updatedUser = $this->userModel->find($id);

            // Remove sensitive data
            unset($updatedUser['password_hash']);

            $newStatus = !$user['active'];

            // Log the action
            $this->audit(
                $newStatus ? 'activate' : 'deactivate',
                'users',
                $id,
                'User ' . ($newStatus ? 'activated' : 'deactivated') . ' via API: ' . $user['name']
            );

            $this->json([
                'success' => true,
                'message' => 'User ' . ($newStatus ? 'activated' : 'deactivated') . ' successfully',
                'data' => $updatedUser,
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to toggle user active status via API: {error}', ['error' => $e->getMessage()]);
            $this->error('Failed to toggle user active status: ' . $e->getMessage());
        }
    }

    /**
     * Ensure the current user is an admin
     */
    private function ensureAdmin(): void
    {
        if (!$this->isAdmin()) {
            $this->forbidden('Insufficient permissions');
        }
    }
}
