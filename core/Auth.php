<?php

declare(strict_types=1);

namespace DDWB;

/**
 * Authentication
 * 
 * Handles user authentication and authorization
 */
final class Auth
{
    private array $config;
    private Session $session;
    private Database $database;

    /**
     * Create a new Auth instance
     * 
     * @param array $config The authentication configuration
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->session = Application::getInstance()->getContainer()->resolve(Session::class);
        $this->database = Application::getInstance()->getContainer()->resolve(Database::class);
    }

    /**
     * Check if a user is authenticated
     * 
     * @return bool True if the user is authenticated
     */
    public function isAuthenticated(): bool
    {
        return $this->session->has('user_id') && $this->isUserActive();
    }

    /**
     * Check if the authenticated user is active
     * 
     * @return bool True if the user is active
     */
    private function isUserActive(): bool
    {
        $userId = $this->session->get('user_id');

        if ($userId === null) {
            return false;
        }

        $user = $this->getUserById((int)$userId);

        if ($user === null) {
            return false;
        }

        return (bool)($user['active'] ?? true);
    }

    /**
     * Check if the authenticated user is an admin
     * 
     * @return bool True if the user is an admin
     */
    public function isAdmin(): bool
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        $user = $this->getAuthenticatedUser();

        return ($user['role'] ?? '') === 'admin';
    }

    /**
     * Get the authenticated user
     * 
     * @return array|null The user data or null if not authenticated
     */
    public function getAuthenticatedUser(): ?array
    {
        $userId = $this->session->get('user_id');

        if ($userId === null) {
            return null;
        }

        return $this->getUserById((int)$userId);
    }

    /**
     * Get the authenticated user ID
     * 
     * @return int|null The user ID or null if not authenticated
     */
    public function getUserId(): ?int
    {
        return $this->session->get('user_id');
    }

    /**
     * Get a user by ID
     * 
     * @param int $userId The user ID
     * @return array|null The user data or null if not found
     */
    public function getUserById(int $userId): ?array
    {
        $sql = 'SELECT * FROM users WHERE id = ? LIMIT 1';
        return $this->database->selectOne($sql, [$userId]);
    }

    /**
     * Get a user by email
     * 
     * @param string $email The user email
     * @return array|null The user data or null if not found
     */
    public function getUserByEmail(string $email): ?array
    {
        $sql = 'SELECT * FROM users WHERE email = ? LIMIT 1';
        return $this->database->selectOne($sql, [$email]);
    }

    /**
     * Attempt to authenticate a user
     * 
     * @param string $email The user email
     * @param string $password The user password
     * @return bool True if authentication succeeded
     */
    public function attempt(string $email, string $password): bool
    {
        $user = $this->getUserByEmail($email);

        if ($user === null) {
            return false;
        }

        // Check if user is active
        if (!($user['active'] ?? true)) {
            return false;
        }

        // Verify password
        if (!password_verify($password, $user['password_hash'] ?? '')) {
            return false;
        }

        // Authentication successful
        $this->loginUser($user);

        return true;
    }

    /**
     * Login a user
     * 
     * @param array $user The user data
     */
    private function loginUser(array $user): void
    {
        $this->session->set('user_id', $user['id']);
        $this->session->set('user_email', $user['email'] ?? '');
        $this->session->set('user_name', $user['name'] ?? '');
        $this->session->set('user_role', $user['role'] ?? 'user');
        $this->session->set('logged_in_at', time());

        // Update last login timestamp
        $this->database->update(
            'users',
            ['last_login_at' => date('Y-m-d H:i:s')],
            ['id' => $user['id']]
        );

        // Regenerate session ID to prevent session fixation
        $this->session->regenerate(true);
    }

    /**
     * Logout the current user
     */
    public function logout(): void
    {
        $this->session->remove('user_id');
        $this->session->remove('user_email');
        $this->session->remove('user_name');
        $this->session->remove('user_role');
        $this->session->remove('logged_in_at');

        // Clear CSRF token
        $csrf = Application::getInstance()->getContainer()->resolve(Csrf::class);
        $csrf->clearToken();

        // Destroy the session
        $this->session->destroy();
    }

    /**
     * Check if the current user can perform an action
     * 
     * @param string $action The action to check
     * @param string|null $resource The resource type
     * @param int|null $resourceId The resource ID
     * @return bool True if the user can perform the action
     */
    public function can(string $action, ?string $resource = null, ?int $resourceId = null): bool
    {
        // Admins can do everything
        if ($this->isAdmin()) {
            return true;
        }

        // Users can view inventory
        if ($action === 'view' && in_array($resource, ['devices', 'cases', 'inventory', 'maintenance'], true)) {
            return true;
        }

        // Users can scan items
        if ($action === 'scan') {
            return true;
        }

        // Users can perform lending/return actions
        if (in_array($action, ['lend', 'return']) && $resource === 'rentals') {
            return true;
        }

        // Users can use packlists
        if ($action === 'use' && $resource === 'packlists') {
            return true;
        }

        // Users can view maintenance information
        if ($action === 'view' && $resource === 'maintenance') {
            return true;
        }

        // Default: deny
        return false;
    }

    /**
     * Check if the current user owns a resource
     * 
     * @param string $resourceType The resource type
     * @param int $resourceId The resource ID
     * @return bool True if the user owns the resource
     */
    public function owns(string $resourceType, int $resourceId): bool
    {
        $userId = $this->getUserId();

        if ($userId === null) {
            return false;
        }

        // Check ownership based on resource type
        $table = match ($resourceType) {
            'rental' => 'rentals',
            'packlist' => 'packlists',
            default => null,
        };

        if ($table === null) {
            return false;
        }

        $sql = sprintf(
            'SELECT COUNT(*) FROM %s WHERE id = ? AND created_by = ?',
            $this->database->quoteIdentifier($table)
        );

        $count = (int)$this->database->selectValue($sql, [$resourceId, $userId]);

        return $count > 0;
    }

    /**
     * Hash a password
     * 
     * @param string $password The password to hash
     * @return string The hashed password
     */
    public function hashPassword(string $password): string
    {
        $algorithm = $this->config['algorithm'] ?? PASSWORD_BCRYPT;
        $options = $this->config['options'] ?? [];

        return password_hash($password, $algorithm, $options);
    }

    /**
     * Verify a password against a hash
     * 
     * @param string $password The password to verify
     * @param string $hash The hash to verify against
     * @return bool True if the password matches the hash
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Check if a password needs rehashing
     * 
     * @param string $hash The password hash
     * @return bool True if the password needs rehashing
     */
    public function needsRehash(string $hash): bool
    {
        $algorithm = $this->config['algorithm'] ?? PASSWORD_BCRYPT;
        $options = $this->config['options'] ?? [];

        return password_needs_rehash($hash, $algorithm, $options);
    }

    /**
     * Get all users
     * 
     * @return array The users
     */
    public function getAllUsers(): array
    {
        return $this->database->select('SELECT * FROM users ORDER BY name ASC');
    }

    /**
     * Create a new user
     * 
     * @param array $data The user data
     * @return int|string The new user ID
     */
    public function createUser(array $data): int|string
    {
        $data['password_hash'] = $this->hashPassword($data['password'] ?? '');
        unset($data['password']);

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->database->insert('users', $data);
    }

    /**
     * Update a user
     * 
     * @param int $userId The user ID
     * @param array $data The user data
     * @return int The number of affected rows
     */
    public function updateUser(int $userId, array $data): int
    {
        if (isset($data['password'])) {
            $data['password_hash'] = $this->hashPassword($data['password']);
            unset($data['password']);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->database->update('users', $data, ['id' => $userId]);
    }

    /**
     * Delete a user
     * 
     * @param int $userId The user ID
     * @return int The number of affected rows
     */
    public function deleteUser(int $userId): int
    {
        return $this->database->delete('users', ['id' => $userId]);
    }

    /**
     * Get users by role
     * 
     * @param string $role The role to filter by
     * @return array The users with the specified role
     */
    public function getUsersByRole(string $role): array
    {
        return $this->database->select(
            'SELECT * FROM users WHERE role = ? ORDER BY name ASC',
            [$role]
        );
    }
}
