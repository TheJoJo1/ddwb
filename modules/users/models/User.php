<?php

declare(strict_types=1);

namespace DDWB\Modules\Users\Models;

use DDWB\Model;
use DDWB\Database;

/**
 * User Model
 * 
 * Handles user data and operations
 */
final class User extends Model
{
    protected string $table = 'users';
    protected string $primaryKey = 'id';
    
    protected array $fillable = [
        'email',
        'password_hash',
        'name',
        'role',
        'active',
    ];
    
    protected array $guarded = [
        'id',
        'created_at',
        'updated_at',
        'last_login_at',
    ];
    
    protected array $casts = [
        'active' => 'bool',
    ];
    
    protected array $dates = [
        'created_at',
        'updated_at',
        'last_login_at',
    ];

    /**
     * Create a new User instance
     * 
     * @param Database $database The database instance
     */
    public function __construct(Database $database)
    {
        parent::__construct($database);
    }

    /**
     * Get all users
     * 
     * @param array $filters Filters to apply
     * @return array The users
     */
    public function getAllUsers(array $filters = []): array
    {
        $query = 'SELECT * FROM users WHERE 1=1';
        $params = [];

        if (isset($filters['role'])) {
            $query .= ' AND role = ?';
            $params[] = $filters['role'];
        }

        if (isset($filters['active'])) {
            $query .= ' AND active = ?';
            $params[] = (int)$filters['active'];
        }

        if (isset($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query .= ' AND (name LIKE ? OR email LIKE ?)';
            $params[] = $search;
            $params[] = $search;
        }

        $query .= ' ORDER BY name ASC';

        return $this->database->select($query, $params);
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

    /**
     * Get active users
     * 
     * @return array The active users
     */
    public function getActiveUsers(): array
    {
        return $this->database->select(
            'SELECT * FROM users WHERE active = 1 ORDER BY name ASC'
        );
    }

    /**
     * Get inactive users
     * 
     * @return array The inactive users
     */
    public function getInactiveUsers(): array
    {
        return $this->database->select(
            'SELECT * FROM users WHERE active = 0 ORDER BY name ASC'
        );
    }

    /**
     * Create a new user
     * 
     * @param array $data The user data
     * @return int|string The new user ID
     */
    public function createUser(array $data): int|string
    {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            unset($data['password']);
        }

        // Set default values
        $data['active'] = $data['active'] ?? true;
        $data['role'] = $data['role'] ?? 'user';

        return $this->create($data);
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
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            unset($data['password']);
        }

        return $this->update($userId, $data);
    }

    /**
     * Update user password
     * 
     * @param int $userId The user ID
     * @param string $password The new password
     * @return int The number of affected rows
     */
    public function updatePassword(int $userId, string $password): int
    {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        return $this->database->update(
            $this->table,
            ['password_hash' => $passwordHash, 'updated_at' => date('Y-m-d H:i:s')],
            [$this->primaryKey => $userId]
        );
    }

    /**
     * Reset user password
     * 
     * @param int $userId The user ID
     * @param string $newPassword The new password
     * @return int The number of affected rows
     */
    public function resetPassword(int $userId, string $newPassword): int
    {
        return $this->updatePassword($userId, $newPassword);
    }

    /**
     * Toggle user active status
     * 
     * @param int $userId The user ID
     * @return int The number of affected rows
     */
    public function toggleActive(int $userId): int
    {
        return $this->database->execute(
            'UPDATE users SET active = NOT active, updated_at = NOW() WHERE id = ?',
            [$userId]
        );
    }

    /**
     * Delete a user
     * 
     * @param int $userId The user ID
     * @return int The number of affected rows
     */
    public function deleteUser(int $userId): int
    {
        return $this->delete($userId);
    }

    /**
     * Check if a user exists
     * 
     * @param int $userId The user ID
     * @return bool True if the user exists
     */
    public function userExists(int $userId): bool
    {
        return $this->exists($userId);
    }

    /**
     * Check if an email is available
     * 
     * @param string $email The email to check
     * @param int|null $excludeUserId The user ID to exclude
     * @return bool True if the email is available
     */
    public function isEmailAvailable(string $email, ?int $excludeUserId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE email = ?';
        $params = [$email];

        if ($excludeUserId !== null) {
            $sql .= ' AND id != ?';
            $params[] = $excludeUserId;
        }

        $count = (int)$this->database->selectValue($sql, $params);
        return $count === 0;
    }

    /**
     * Get user statistics
     * 
     * @return array The user statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->count(),
            'active' => $this->database->count($this->table, ['active' => 1]),
            'inactive' => $this->database->count($this->table, ['active' => 0]),
            'admin' => $this->database->count($this->table, ['role' => 'admin']),
            'user' => $this->database->count($this->table, ['role' => 'user']),
        ];
    }

    /**
     * Search users
     * 
     * @param string $query The search query
     * @return array The matching users
     */
    public function search(string $query): array
    {
        return $this->database->select(
            'SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY name ASC',
            ["%{$query}%", "%{$query}%"]
        );
    }

    /**
     * Get users with pagination
     * 
     * @param int $page The page number
     * @param int $perPage The number of users per page
     * @param array $filters Filters to apply
     * @return array The paginated results
     */
    public function paginateUsers(int $page = 1, int $perPage = 25, array $filters = []): array
    {
        $offset = ($page - 1) * $perPage;

        $query = 'SELECT * FROM users WHERE 1=1';
        $countQuery = 'SELECT COUNT(*) FROM users WHERE 1=1';
        $params = [];
        $countParams = [];

        if (isset($filters['role'])) {
            $query .= ' AND role = ?';
            $countQuery .= ' AND role = ?';
            $params[] = $filters['role'];
            $countParams[] = $filters['role'];
        }

        if (isset($filters['active'])) {
            $query .= ' AND active = ?';
            $countQuery .= ' AND active = ?';
            $params[] = (int)$filters['active'];
            $countParams[] = (int)$filters['active'];
        }

        if (isset($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query .= ' AND (name LIKE ? OR email LIKE ?)';
            $countQuery .= ' AND (name LIKE ? OR email LIKE ?)';
            $params[] = $search;
            $params[] = $search;
            $countParams[] = $search;
            $countParams[] = $search;
        }

        $query .= ' ORDER BY name ASC LIMIT ? OFFSET ?';
        $params[] = $perPage;
        $params[] = $offset;

        $total = (int)$this->database->selectValue($countQuery, $countParams);
        $users = $this->database->select($query, $params);

        return [
            'data' => $users,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int)ceil($total / $perPage),
        ];
    }

    /**
     * Get the next available user ID
     * 
     * @return int The next user ID
     */
    public function getNextUserId(): int
    {
        $result = $this->database->selectOne('SELECT MAX(id) as max_id FROM users');
        return (int)($result['max_id'] ?? 0) + 1;
    }

    /**
     * Update user last login timestamp
     * 
     * @param int $userId The user ID
     * @return int The number of affected rows
     */
    public function updateLastLogin(int $userId): int
    {
        return $this->database->update(
            $this->table,
            ['last_login_at' => date('Y-m-d H:i:s')],
            [$this->primaryKey => $userId]
        );
    }
}
