<?php

declare(strict_types=1);

namespace DDWB\Modules\Logs\Models;

use DDWB\Model;
use DDWB\Database;

/**
 * Log Model
 * 
 * Handles log data and operations (Audit Trail)
 */
final class Log extends Model
{
    protected string $table = 'logs';
    protected string $primaryKey = 'id';
    
    protected array $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'metadata_json',
        'ip_address',
        'user_agent',
    ];
    
    protected array $casts = [
        'user_id' => 'int',
        'metadata_json' => 'array',
    ];
    
    protected array $dates = [
        'timestamp',
    ];

    /**
     * Common action types
     */
    public const ACTION_LOGIN = 'login';
    public const ACTION_LOGOUT = 'logout';
    public const ACTION_CREATE = 'create';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';
    public const ACTION_LEND = 'lend';
    public const ACTION_RETURN = 'return';
    public const ACTION_MAINTENANCE_CREATE = 'maintenance_create';
    public const ACTION_MAINTENANCE_UPDATE = 'maintenance_update';
    public const ACTION_CASE_ASSIGN = 'case_assign';
    public const ACTION_CASE_REMOVE = 'case_remove';
    public const ACTION_PACKLIST_CREATE = 'packlist_create';
    public const ACTION_PACKLIST_UPDATE = 'packlist_update';
    public const ACTION_LABEL_GENERATE = 'label_generate';

    /** @var array<string, string> */
    public static array $actionLabels = [
        self::ACTION_LOGIN => 'Anmeldung',
        self::ACTION_LOGOUT => 'Abmeldung',
        self::ACTION_CREATE => 'Erstellt',
        self::ACTION_UPDATE => 'Aktualisiert',
        self::ACTION_DELETE => 'Gelöscht',
        self::ACTION_LEND => 'Ausgeliehen',
        self::ACTION_RETURN => 'Zurückgegeben',
        self::ACTION_MAINTENANCE_CREATE => 'Wartung erstellt',
        self::ACTION_MAINTENANCE_UPDATE => 'Wartung aktualisiert',
        self::ACTION_CASE_ASSIGN => 'Zu Case hinzugefügt',
        self::ACTION_CASE_REMOVE => 'Aus Case entfernt',
        self::ACTION_PACKLIST_CREATE => 'Packliste erstellt',
        self::ACTION_PACKLIST_UPDATE => 'Packliste aktualisiert',
        self::ACTION_LABEL_GENERATE => 'Label generiert',
    ];

    /** @var array<string, string> */
    public static array $actionColors = [
        self::ACTION_LOGIN => 'success',
        self::ACTION_LOGOUT => 'secondary',
        self::ACTION_CREATE => 'primary',
        self::ACTION_UPDATE => 'info',
        self::ACTION_DELETE => 'danger',
        self::ACTION_LEND => 'warning',
        self::ACTION_RETURN => 'success',
        self::ACTION_MAINTENANCE_CREATE => 'primary',
        self::ACTION_MAINTENANCE_UPDATE => 'info',
        self::ACTION_CASE_ASSIGN => 'info',
        self::ACTION_CASE_REMOVE => 'warning',
        self::ACTION_PACKLIST_CREATE => 'primary',
        self::ACTION_PACKLIST_UPDATE => 'info',
        self::ACTION_LABEL_GENERATE => 'secondary',
    ];

    /**
     * Create a new Log instance
     * 
     * @param Database $database The database instance
     */
    public function __construct(Database $database)
    {
        parent::__construct($database);
    }

    /**
     * Get all logs
     * 
     * @param array $filters Filters to apply
     * @return array The logs
     */
    public function getAllLogs(array $filters = []): array
    {
        $query = 'SELECT l.*, ' .
                 'u.name as user_name, u.email as user_email ' .
                 'FROM logs l ' .
                 'LEFT JOIN users u ON l.user_id = u.id ' .
                 'ORDER BY l.timestamp DESC';
        $params = [];

        if (isset($filters['user_id'])) {
            $query = 'SELECT l.*, ' .
                     'u.name as user_name, u.email as user_email ' .
                     'FROM logs l ' .
                     'LEFT JOIN users u ON l.user_id = u.id ' .
                     'WHERE l.user_id = ? ' .
                     'ORDER BY l.timestamp DESC';
            $params[] = $filters['user_id'];
        }

        if (isset($filters['action'])) {
            if (strpos($query, 'WHERE') === false) {
                $query .= ' WHERE l.action = ?';
            } else {
                $query .= ' AND l.action = ?';
            }
            $params[] = $filters['action'];
        }

        if (isset($filters['entity_type'])) {
            if (strpos($query, 'WHERE') === false) {
                $query .= ' WHERE l.entity_type = ?';
            } else {
                $query .= ' AND l.entity_type = ?';
            }
            $params[] = $filters['entity_type'];
        }

        if (isset($filters['entity_id'])) {
            if (strpos($query, 'WHERE') === false) {
                $query .= ' WHERE l.entity_id = ?';
            } else {
                $query .= ' AND l.entity_id = ?';
            }
            $params[] = $filters['entity_id'];
        }

        if (isset($filters['start_date'])) {
            if (strpos($query, 'WHERE') === false) {
                $query .= ' WHERE l.timestamp >= ?';
            } else {
                $query .= ' AND l.timestamp >= ?';
            }
            $params[] = $filters['start_date'];
        }

        if (isset($filters['end_date'])) {
            if (strpos($query, 'WHERE') === false) {
                $query .= ' WHERE l.timestamp <= ?';
            } else {
                $query .= ' AND l.timestamp <= ?';
            }
            $params[] = $filters['end_date'];
        }

        return $this->database->select($query, $params);
    }

    /**
     * Get logs by user
     * 
     * @param int $userId The user ID
     * @return array The logs for the user
     */
    public function getLogsByUser(int $userId): array
    {
        return $this->database->select(
            'SELECT l.*, u.name as user_name, u.email as user_email ' .
            'FROM logs l ' .
            'LEFT JOIN users u ON l.user_id = u.id ' .
            'WHERE l.user_id = ? ' .
            'ORDER BY l.timestamp DESC',
            [$userId]
        );
    }

    /**
     * Get logs by action
     * 
     * @param string $action The action
     * @return array The logs for the action
     */
    public function getLogsByAction(string $action): array
    {
        return $this->database->select(
            'SELECT l.*, u.name as user_name, u.email as user_email ' .
            'FROM logs l ' .
            'LEFT JOIN users u ON l.user_id = u.id ' .
            'WHERE l.action = ? ' .
            'ORDER BY l.timestamp DESC',
            [$action]
        );
    }

    /**
     * Get logs by entity
     * 
     * @param string $entityType The entity type
     * @param string $entityId The entity ID
     * @return array The logs for the entity
     */
    public function getLogsByEntity(string $entityType, string $entityId): array
    {
        return $this->database->select(
            'SELECT l.*, u.name as user_name, u.email as user_email ' .
            'FROM logs l ' .
            'LEFT JOIN users u ON l.user_id = u.id ' .
            'WHERE l.entity_type = ? AND l.entity_id = ? ' .
            'ORDER BY l.timestamp DESC',
            [$entityType, $entityId]
        );
    }

    /**
     * Get recent logs
     * 
     * @param int $limit The number of logs to return
     * @return array The recent logs
     */
    public function getRecentLogs(int $limit = 50): array
    {
        return $this->database->select(
            'SELECT l.*, u.name as user_name, u.email as user_email ' .
            'FROM logs l ' .
            'LEFT JOIN users u ON l.user_id = u.id ' .
            'ORDER BY l.timestamp DESC ' .
            'LIMIT ?',
            [$limit]
        );
    }

    /**
     * Get logs for today
     * 
     * @return array The logs for today
     */
    public function getLogsForToday(): array
    {
        return $this->database->select(
            'SELECT l.*, u.name as user_name, u.email as user_email ' .
            'FROM logs l ' .
            'LEFT JOIN users u ON l.user_id = u.id ' .
            'WHERE DATE(l.timestamp) = CURDATE() ' .
            'ORDER BY l.timestamp DESC'
        );
    }

    /**
     * Get logs for a date range
     * 
     * @param string $startDate The start date
     * @param string $endDate The end date
     * @return array The logs for the date range
     */
    public function getLogsForDateRange(string $startDate, string $endDate): array
    {
        return $this->database->select(
            'SELECT l.*, u.name as user_name, u.email as user_email ' .
            'FROM logs l ' .
            'LEFT JOIN users u ON l.user_id = u.id ' .
            'WHERE l.timestamp BETWEEN ? AND ? ' .
            'ORDER BY l.timestamp DESC',
            [$startDate, $endDate]
        );
    }

    /**
     * Create a new log entry
     * 
     * @param array $data The log data
     * @return int|string The new log ID
     */
    public function createLog(array $data): int|string
    {
        // Set default values
        $data['timestamp'] = $data['timestamp'] ?? date('Y-m-d H:i:s');
        $data['ip_address'] = $data['ip_address'] ?? ($_SERVER['REMOTE_ADDR'] ?? null);
        $data['user_agent'] = $data['user_agent'] ?? ($_SERVER['HTTP_USER_AGENT'] ?? null);

        return $this->create($data);
    }

    /**
     * Log an action
     * 
     * @param int|null $userId The user ID
     * @param string $action The action
     * @param string $entityType The entity type
     * @param string $entityId The entity ID
     * @param string $description The description
     * @param array|null $metadata The metadata
     * @return int|string The new log ID
     */
    public function logAction(
        ?int $userId,
        string $action,
        string $entityType,
        string $entityId,
        string $description,
        ?array $metadata = null
    ): int|string {
        return $this->createLog([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'metadata_json' => $metadata !== null ? json_encode($metadata) : null,
        ]);
    }

    /**
     * Get log statistics
     * 
     * @return array The log statistics
     */
    public function getStatistics(): array
    {
        $total = $this->count();
        
        // Get counts by action
        $actions = $this->database->select(
            'SELECT action, COUNT(*) as count FROM logs GROUP BY action ORDER BY count DESC'
        );
        
        $actionCounts = [];
        foreach ($actions as $action) {
            $actionCounts[$action['action']] = (int)$action['count'];
        }

        // Get counts by entity type
        $entityTypes = $this->database->select(
            'SELECT entity_type, COUNT(*) as count FROM logs WHERE entity_type IS NOT NULL GROUP BY entity_type ORDER BY count DESC'
        );
        
        $entityTypeCounts = [];
        foreach ($entityTypes as $entityType) {
            $entityTypeCounts[$entityType['entity_type']] = (int)$entityType['count'];
        }

        // Get today's count
        $todayCount = (int)$this->database->selectValue(
            'SELECT COUNT(*) FROM logs WHERE DATE(timestamp) = CURDATE()'
        );

        // Get this week's count
        $weekCount = (int)$this->database->selectValue(
            'SELECT COUNT(*) FROM logs WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 7 DAY)'
        );

        // Get this month's count
        $monthCount = (int)$this->database->selectValue(
            'SELECT COUNT(*) FROM logs WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 1 MONTH)'
        );

        return [
            'total' => $total,
            'today' => $todayCount,
            'this_week' => $weekCount,
            'this_month' => $monthCount,
            'actions' => $actionCounts,
            'entity_types' => $entityTypeCounts,
        ];
    }

    /**
     * Search logs
     * 
     * @param string $query The search query
     * @param array $filters Additional filters
     * @return array The matching logs
     */
    public function search(string $query, array $filters = []): array
    {
        $searchQuery = 'SELECT l.*, u.name as user_name, u.email as user_email ' .
                       'FROM logs l ' .
                       'LEFT JOIN users u ON l.user_id = u.id ' .
                       'WHERE l.description LIKE ? OR ' .
                       'l.action LIKE ? OR ' .
                       'l.entity_type LIKE ? OR ' .
                       'l.entity_id LIKE ? OR ' .
                       'u.name LIKE ? OR ' .
                       'u.email LIKE ?';
        $params = ["%{$query}%", "%{$query}%", "%{$query}%", "%{$query}%", "%{$query}%", "%{$query}%"];

        if (isset($filters['user_id'])) {
            $searchQuery .= ' AND l.user_id = ?';
            $params[] = $filters['user_id'];
        }

        if (isset($filters['action'])) {
            $searchQuery .= ' AND l.action = ?';
            $params[] = $filters['action'];
        }

        $searchQuery .= ' ORDER BY l.timestamp DESC';

        return $this->database->select($searchQuery, $params);
    }

    /**
     * Get logs with pagination
     * 
     * @param int $page The page number
     * @param int $perPage The number of logs per page
     * @param array $filters Filters to apply
     * @return array The paginated results
     */
    public function paginateLogs(int $page = 1, int $perPage = 50, array $filters = []): array
    {
        $offset = ($page - 1) * $perPage;

        $query = 'SELECT l.*, u.name as user_name, u.email as user_email ' .
                 'FROM logs l ' .
                 'LEFT JOIN users u ON l.user_id = u.id';
        $countQuery = 'SELECT COUNT(*) FROM logs l';
        $params = [];
        $countParams = [];

        $whereAdded = false;

        if (isset($filters['user_id'])) {
            $query .= ' WHERE l.user_id = ?';
            $countQuery .= ' WHERE l.user_id = ?';
            $params[] = $filters['user_id'];
            $countParams[] = $filters['user_id'];
            $whereAdded = true;
        }

        if (isset($filters['action'])) {
            if ($whereAdded) {
                $query .= ' AND l.action = ?';
                $countQuery .= ' AND l.action = ?';
            } else {
                $query .= ' WHERE l.action = ?';
                $countQuery .= ' WHERE l.action = ?';
                $whereAdded = true;
            }
            $params[] = $filters['action'];
            $countParams[] = $filters['action'];
        }

        if (isset($filters['entity_type'])) {
            if ($whereAdded) {
                $query .= ' AND l.entity_type = ?';
                $countQuery .= ' AND l.entity_type = ?';
            } else {
                $query .= ' WHERE l.entity_type = ?';
                $countQuery .= ' WHERE l.entity_type = ?';
                $whereAdded = true;
            }
            $params[] = $filters['entity_type'];
            $countParams[] = $filters['entity_type'];
        }

        if (isset($filters['start_date'])) {
            if ($whereAdded) {
                $query .= ' AND l.timestamp >= ?';
                $countQuery .= ' AND l.timestamp >= ?';
            } else {
                $query .= ' WHERE l.timestamp >= ?';
                $countQuery .= ' WHERE l.timestamp >= ?';
                $whereAdded = true;
            }
            $params[] = $filters['start_date'];
            $countParams[] = $filters['start_date'];
        }

        if (isset($filters['end_date'])) {
            if ($whereAdded) {
                $query .= ' AND l.timestamp <= ?';
                $countQuery .= ' AND l.timestamp <= ?';
            } else {
                $query .= ' WHERE l.timestamp <= ?';
                $countQuery .= ' WHERE l.timestamp <= ?';
                $whereAdded = true;
            }
            $params[] = $filters['end_date'];
            $countParams[] = $filters['end_date'];
        }

        $query .= ' ORDER BY l.timestamp DESC LIMIT ? OFFSET ?';
        $params[] = $perPage;
        $params[] = $offset;

        $total = (int)$this->database->selectValue($countQuery, $countParams);
        $logs = $this->database->select($query, $params);

        return [
            'data' => $logs,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int)ceil($total / $perPage),
        ];
    }

    /**
     * Get log action label
     * 
     * @param string $action The action
     * @return string The label
     */
    public static function getActionLabel(string $action): string
    {
        return self::$actionLabels[$action] ?? $action;
    }

    /**
     * Get log action color
     * 
     * @param string $action The action
     * @return string The color
     */
    public static function getActionColor(string $action): string
    {
        return self::$actionColors[$action] ?? 'secondary';
    }

    /**
     * Get all action types
     * 
     * @return array The action types
     */
    public static function getActionTypes(): array
    {
        return array_keys(self::$actionLabels);
    }

    /**
     * Get action options for select dropdown
     * 
     * @return array The action options
     */
    public static function getActionOptions(): array
    {
        $options = [];
        
        foreach (self::$actionLabels as $value => $label) {
            $options[$value] = $label;
        }

        return $options;
    }

    /**
     * Clear old logs
     * 
     * @param int $days The number of days to keep logs
     * @return int The number of logs deleted
     */
    public function clearOldLogs(int $days = 365): int
    {
        $cutoffDate = date('Y-m-d', strtotime("-{$days} days"));
        
        return $this->database->delete(
            'logs',
            ['timestamp' => ['<' => $cutoffDate]]
        );
    }
}
