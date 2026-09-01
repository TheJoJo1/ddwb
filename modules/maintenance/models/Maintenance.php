<?php

declare(strict_types=1);

namespace DDWB\Modules\Maintenance\Models;

use DDWB\Model;
use DDWB\Database;
use DDWB\Application;

/**
 * Maintenance Model
 * 
 * Handles maintenance data and operations (DGUV3)
 */
final class Maintenance extends Model
{
    protected string $table = 'maintenance';
    protected string $primaryKey = 'id';
    
    protected array $fillable = [
        'device_id',
        'type',
        'last_inspection_date',
        'interval_months',
        'next_inspection_date',
        'status',
        'inspector',
        'notes',
        'created_by',
    ];
    
    protected array $casts = [
        'device_id' => 'int',
        'interval_months' => 'int',
        'created_by' => 'int',
        'status' => 'string',
    ];
    
    protected array $dates = [
        'last_inspection_date',
        'next_inspection_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Valid statuses
     */
    public const STATUS_OK = 'ok';
    public const STATUS_UPCOMING = 'upcoming';
    public const STATUS_DUE = 'due';
    public const STATUS_OVERDUE = 'overdue';

    /** @var array<string, string> */
    public static array $statusLabels = [
        self::STATUS_OK => 'OK',
        self::STATUS_UPCOMING => 'Bald fällig',
        self::STATUS_DUE => 'Fällig',
        self::STATUS_OVERDUE => 'Überfällig',
    ];

    /** @var array<string, string> */
    public static array $statusColors = [
        self::STATUS_OK => 'success',
        self::STATUS_UPCOMING => 'warning',
        self::STATUS_DUE => 'danger',
        self::STATUS_OVERDUE => 'danger',
    ];

    /**
     * Default inspection type
     */
    public const TYPE_DGUV3 = 'DGUV3';

    /**
     * Create a new Maintenance instance
     * 
     * @param Database $database The database instance
     */
    public function __construct(Database $database)
    {
        parent::__construct($database);
    }

    /**
     * Get all maintenance records
     * 
     * @param array $filters Filters to apply
     * @return array The maintenance records
     */
    public function getAllMaintenance(array $filters = []): array
    {
        $query = 'SELECT m.*, ' .
                 'd.name as device_name, d.internal_id as device_internal_id, d.status as device_status, ' .
                 'u.name as created_by_name, u.email as created_by_email ' .
                 'FROM maintenance m ' .
                 'JOIN devices d ON m.device_id = d.id ' .
                 'LEFT JOIN users u ON m.created_by = u.id ' .
                 'WHERE m.deleted_at IS NULL';
        $params = [];

        if (isset($filters['status'])) {
            $query .= ' AND m.status = ?';
            $params[] = $filters['status'];
        }

        if (isset($filters['device_id'])) {
            $query .= ' AND m.device_id = ?';
            $params[] = $filters['device_id'];
        }

        if (isset($filters['type'])) {
            $query .= ' AND m.type = ?';
            $params[] = $filters['type'];
        }

        if (isset($filters['upcoming'])) {
            $query .= ' AND m.next_inspection_date >= CURDATE()';
        }

        if (isset($filters['overdue'])) {
            $query .= ' AND m.next_inspection_date < CURDATE()';
        }

        $query .= ' ORDER BY m.next_inspection_date ASC';

        return $this->database->select($query, $params);
    }

    /**
     * Get maintenance records by status
     * 
     * @param string $status The status to filter by
     * @return array The maintenance records with the specified status
     */
    public function getMaintenanceByStatus(string $status): array
    {
        return $this->database->select(
            'SELECT m.*, ' .
            'd.name as device_name, d.internal_id as device_internal_id, ' .
            'u.name as created_by_name ' .
            'FROM maintenance m ' .
            'JOIN devices d ON m.device_id = d.id ' .
            'LEFT JOIN users u ON m.created_by = u.id ' .
            'WHERE m.status = ? AND m.deleted_at IS NULL ' .
            'ORDER BY m.next_inspection_date ASC',
            [$status]
        );
    }

    /**
     * Get upcoming maintenance records
     * 
     * @return array The upcoming maintenance records
     */
    public function getUpcomingMaintenance(): array
    {
        return $this->getMaintenanceByStatus(self::STATUS_UPCOMING);
    }

    /**
     * Get due maintenance records
     * 
     * @return array The due maintenance records
     */
    public function getDueMaintenance(): array
    {
        return $this->getMaintenanceByStatus(self::STATUS_DUE);
    }

    /**
     * Get overdue maintenance records
     * 
     * @return array The overdue maintenance records
     */
    public function getOverdueMaintenance(): array
    {
        return $this->getMaintenanceByStatus(self::STATUS_OVERDUE);
    }

    /**
     * Get maintenance record by ID
     * 
     * @param int $maintenanceId The maintenance ID
     * @return array|null The maintenance record or null if not found
     */
    public function getMaintenanceById(int $maintenanceId): ?array
    {
        $maintenance = $this->database->selectOne(
            'SELECT m.*, ' .
            'd.name as device_name, d.internal_id as device_internal_id, d.status as device_status, ' .
            'u.name as created_by_name, u.email as created_by_email ' .
            'FROM maintenance m ' .
            'JOIN devices d ON m.device_id = d.id ' .
            'LEFT JOIN users u ON m.created_by = u.id ' .
            'WHERE m.id = ? AND m.deleted_at IS NULL',
            [$maintenanceId]
        );

        return $maintenance ?? null;
    }

    /**
     * Get maintenance records for a device
     * 
     * @param int $deviceId The device ID
     * @return array The maintenance records for the device
     */
    public function getMaintenanceForDevice(int $deviceId): array
    {
        return $this->database->select(
            'SELECT m.*, ' .
            'u.name as created_by_name, u.email as created_by_email ' .
            'FROM maintenance m ' .
            'LEFT JOIN users u ON m.created_by = u.id ' .
            'WHERE m.device_id = ? AND m.deleted_at IS NULL ' .
            'ORDER BY m.last_inspection_date DESC',
            [$deviceId]
        );
    }

    /**
     * Get the latest maintenance record for a device
     * 
     * @param int $deviceId The device ID
     * @return array|null The latest maintenance record or null if not found
     */
    public function getLatestMaintenanceForDevice(int $deviceId): ?array
    {
        $maintenance = $this->database->selectOne(
            'SELECT m.*, ' .
            'u.name as created_by_name ' .
            'FROM maintenance m ' .
            'LEFT JOIN users u ON m.created_by = u.id ' .
            'WHERE m.device_id = ? AND m.deleted_at IS NULL ' .
            'ORDER BY m.last_inspection_date DESC, m.created_at DESC',
            [$deviceId]
        );

        return $maintenance ?? null;
    }

    /**
     * Create a new maintenance record
     * 
     * @param array $data The maintenance data
     * @return int|string The new maintenance ID
     */
    public function createMaintenance(array $data): int|string
    {
        // Validate data
        if (!isset($data['device_id']) || $data['device_id'] <= 0) {
            throw new \InvalidArgumentException('Device ID is required');
        }

        if (!isset($data['last_inspection_date']) || empty($data['last_inspection_date'])) {
            $data['last_inspection_date'] = date('Y-m-d');
        }

        if (!isset($data['interval_months']) || $data['interval_months'] <= 0) {
            $data['interval_months'] = 12; // Default to 12 months
        }

        // Set default type
        $data['type'] = $data['type'] ?? self::TYPE_DGUV3;

        // Set created_by if not provided
        if (!isset($data['created_by'])) {
            $auth = Application::getInstance()->getContainer()->resolve(\DDWB\Auth::class);
            $data['created_by'] = $auth->id();
        }

        // Calculate next inspection date if not provided
        if (!isset($data['next_inspection_date']) || empty($data['next_inspection_date'])) {
            $lastDate = new \DateTime($data['last_inspection_date']);
            $nextDate = $lastDate->add(new \DateInterval("P{$data['interval_months']}M"));
            $data['next_inspection_date'] = $nextDate->format('Y-m-d');
        }

        // Calculate status based on next inspection date
        $data['status'] = $this->calculateStatus($data['next_inspection_date']);

        // Begin transaction
        $this->database->beginTransaction();

        try {
            // Create the maintenance record
            $maintenanceId = $this->create($data);

            // Update device status to maintenance if this is the latest record
            $latestMaintenance = $this->getLatestMaintenanceForDevice($data['device_id']);
            if ($latestMaintenance === null || $latestMaintenance['id'] === $maintenanceId) {
                // Only update device status if the next inspection is overdue or due
                if ($data['status'] === self::STATUS_OVERDUE || $data['status'] === self::STATUS_DUE) {
                    $this->database->update(
                        'devices',
                        ['status' => 'maintenance', 'updated_at' => date('Y-m-d H:i:s')],
                        ['id' => $data['device_id']]
                    );
                }
            }

            // Log the action
            $this->logAction('maintenance_create', 'maintenance', (string)$maintenanceId, 
                "Created maintenance for device {$data['device_id']}");

            // Commit transaction
            $this->database->commit();

            return $maintenanceId;
        } catch (\Exception $e) {
            // Rollback transaction
            $this->database->rollback();
            throw $e;
        }
    }

    /**
     * Update a maintenance record
     * 
     * @param int $maintenanceId The maintenance ID
     * @param array $data The maintenance data
     * @return int The number of affected rows
     */
    public function updateMaintenance(int $maintenanceId, array $data): int
    {
        // Get the current maintenance record
        $maintenance = $this->getMaintenanceById($maintenanceId);

        if ($maintenance === null) {
            throw new \RuntimeException('Maintenance record not found');
        }

        // Handle date changes
        if (isset($data['last_inspection_date']) || isset($data['interval_months'])) {
            $lastDate = $data['last_inspection_date'] ?? $maintenance['last_inspection_date'];
            $interval = $data['interval_months'] ?? $maintenance['interval_months'];

            // Recalculate next inspection date
            $nextDate = new \DateTime($lastDate);
            $nextDate->add(new \DateInterval("P{$interval}M"));
            $data['next_inspection_date'] = $nextDate->format('Y-m-d');
        }

        // Recalculate status if next inspection date changes
        if (isset($data['next_inspection_date'])) {
            $data['status'] = $this->calculateStatus($data['next_inspection_date']);
        } elseif (isset($data['last_inspection_date']) || isset($data['interval_months'])) {
            $data['status'] = $this->calculateStatus($data['next_inspection_date']);
        }

        // Begin transaction
        $this->database->beginTransaction();

        try {
            // Update the maintenance record
            $result = $this->update($maintenanceId, $data);

            // Update device status if needed
            $updatedMaintenance = $this->getMaintenanceById($maintenanceId);
            if ($updatedMaintenance !== null) {
                // Check if this is the latest maintenance record for the device
                $latestMaintenance = $this->getLatestMaintenanceForDevice($updatedMaintenance['device_id']);
                if ($latestMaintenance !== null && $latestMaintenance['id'] === $maintenanceId) {
                    // Update device status based on maintenance status
                    if ($updatedMaintenance['status'] === self::STATUS_OVERDUE || 
                        $updatedMaintenance['status'] === self::STATUS_DUE) {
                        $this->database->update(
                            'devices',
                            ['status' => 'maintenance', 'updated_at' => date('Y-m-d H:i:s')],
                            ['id' => $updatedMaintenance['device_id']]
                        );
                    } else {
                        // Check if device should be available
                        $deviceInCase = $this->database->selectOne(
                            'SELECT case_id FROM case_device WHERE device_id = ?',
                            [$updatedMaintenance['device_id']]
                        );

                        $deviceLentOut = $this->database->selectOne(
                            'SELECT id FROM rentals WHERE device_id = ? AND status = ? AND deleted_at IS NULL',
                            [$updatedMaintenance['device_id'], 'active']
                        );

                        if ($deviceInCase === null && $deviceLentOut === null) {
                            $this->database->update(
                                'devices',
                                ['status' => 'available', 'updated_at' => date('Y-m-d H:i:s')],
                                ['id' => $updatedMaintenance['device_id']]
                            );
                        }
                    }
                }
            }

            // Log the action
            $this->logAction('maintenance_update', 'maintenance', (string)$maintenanceId, 
                "Updated maintenance for device {$updatedMaintenance['device_id']}");

            // Commit transaction
            $this->database->commit();

            return $result;
        } catch (\Exception $e) {
            // Rollback transaction
            $this->database->rollback();
            throw $e;
        }
    }

    /**
     * Delete a maintenance record
     * 
     * @param int $maintenanceId The maintenance ID
     * @return int The number of affected rows
     */
    public function deleteMaintenance(int $maintenanceId): int
    {
        // Soft delete
        return $this->softDelete($maintenanceId);
    }

    /**
     * Calculate maintenance status based on next inspection date
     * 
     * @param string $nextInspectionDate The next inspection date
     * @return string The calculated status
     */
    public function calculateStatus(string $nextInspectionDate): string
    {
        $app = Application::getInstance();
        $upcomingDays = (int)$app->getConfigValue('maintenance.upcoming_days', 30);
        $dueDays = (int)$app->getConfigValue('maintenance.due_days', 7);

        $nextDate = new \DateTime($nextInspectionDate);
        $today = new \DateTime();
        $diff = $today->diff($nextDate);
        $daysDiff = (int)$diff->format('%r%a');

        if ($daysDiff < 0) {
            return self::STATUS_OVERDUE;
        } elseif ($daysDiff <= $dueDays) {
            return self::STATUS_DUE;
        } elseif ($daysDiff <= $upcomingDays) {
            return self::STATUS_UPCOMING;
        } else {
            return self::STATUS_OK;
        }
    }

    /**
     * Get maintenance statistics
     * 
     * @return array The maintenance statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->count(),
            'ok' => $this->database->count($this->table, ['status' => self::STATUS_OK, 'deleted_at' => null]),
            'upcoming' => $this->database->count($this->table, ['status' => self::STATUS_UPCOMING, 'deleted_at' => null]),
            'due' => $this->database->count($this->table, ['status' => self::STATUS_DUE, 'deleted_at' => null]),
            'overdue' => $this->database->count($this->table, ['status' => self::STATUS_OVERDUE, 'deleted_at' => null]),
        ];
    }

    /**
     * Search maintenance records
     * 
     * @param string $query The search query
     * @param array $filters Additional filters
     * @return array The matching maintenance records
     */
    public function search(string $query, array $filters = []): array
    {
        $searchQuery = 'SELECT m.*, ' .
                       'd.name as device_name, d.internal_id as device_internal_id, ' .
                       'u.name as created_by_name ' .
                       'FROM maintenance m ' .
                       'JOIN devices d ON m.device_id = d.id ' .
                       'LEFT JOIN users u ON m.created_by = u.id ' .
                       'WHERE m.deleted_at IS NULL AND (' .
                       'd.name LIKE ? OR ' .
                       'd.internal_id LIKE ? OR ' .
                       'm.inspector LIKE ? OR ' .
                       'm.notes LIKE ?)';
        $params = ["%{$query}%", "%{$query}%", "%{$query}%", "%{$query}%"];

        if (isset($filters['status'])) {
            $searchQuery .= ' AND m.status = ?';
            $params[] = $filters['status'];
        }

        $searchQuery .= ' ORDER BY m.next_inspection_date ASC';

        return $this->database->select($searchQuery, $params);
    }

    /**
     * Get maintenance records with pagination
     * 
     * @param int $page The page number
     * @param int $perPage The number of maintenance records per page
     * @param array $filters Filters to apply
     * @return array The paginated results
     */
    public function paginateMaintenance(int $page = 1, int $perPage = 25, array $filters = []): array
    {
        $offset = ($page - 1) * $perPage;

        $query = 'SELECT m.*, ' .
                 'd.name as device_name, d.internal_id as device_internal_id, ' .
                 'u.name as created_by_name ' .
                 'FROM maintenance m ' .
                 'JOIN devices d ON m.device_id = d.id ' .
                 'LEFT JOIN users u ON m.created_by = u.id ' .
                 'WHERE m.deleted_at IS NULL';
        $countQuery = 'SELECT COUNT(*) FROM maintenance m WHERE m.deleted_at IS NULL';
        $params = [];
        $countParams = [];

        if (isset($filters['status'])) {
            $query .= ' AND m.status = ?';
            $countQuery .= ' AND m.status = ?';
            $params[] = $filters['status'];
            $countParams[] = $filters['status'];
        }

        if (isset($filters['device_id'])) {
            $query .= ' AND m.device_id = ?';
            $countQuery .= ' AND m.device_id = ?';
            $params[] = $filters['device_id'];
            $countParams[] = $filters['device_id'];
        }

        $query .= ' ORDER BY m.next_inspection_date ASC LIMIT ? OFFSET ?';
        $params[] = $perPage;
        $params[] = $offset;

        $total = (int)$this->database->selectValue($countQuery, $countParams);
        $maintenance = $this->database->select($query, $params);

        return [
            'data' => $maintenance,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int)ceil($total / $perPage),
        ];
    }

    /**
     * Update maintenance statuses for all records
     * 
     * @return int The number of records updated
     */
    public function updateAllStatuses(): int
    {
        // Get all maintenance records
        $maintenanceRecords = $this->database->select(
            'SELECT id, next_inspection_date, status FROM maintenance WHERE deleted_at IS NULL',
            []
        );

        $count = 0;
        foreach ($maintenanceRecords as $record) {
            $newStatus = $this->calculateStatus($record['next_inspection_date']);
            if ($newStatus !== $record['status']) {
                $this->database->update(
                    'maintenance',
                    ['status' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')],
                    ['id' => $record['id']]
                );
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get maintenance status label
     * 
     * @param string $status The status
     * @return string The label
     */
    public static function getStatusLabel(string $status): string
    {
        return self::$statusLabels[$status] ?? $status;
    }

    /**
     * Get maintenance status color
     * 
     * @param string $status The status
     * @return string The color
     */
    public static function getStatusColor(string $status): string
    {
        return self::$statusColors[$status] ?? 'secondary';
    }

    /**
     * Get valid statuses
     * 
     * @return array The valid statuses
     */
    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_OK,
            self::STATUS_UPCOMING,
            self::STATUS_DUE,
            self::STATUS_OVERDUE,
        ];
    }

    /**
     * Get status options for select dropdown
     * 
     * @return array The status options
     */
    public static function getStatusOptions(): array
    {
        $options = [];
        
        foreach (self::$statusLabels as $value => $label) {
            $options[$value] = $label;
        }

        return $options;
    }

    /**
     * Get type options for select dropdown
     * 
     * @return array The type options
     */
    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_DGUV3 => 'DGUV3 Prüfung',
            'visual' => 'Sichtprüfung',
            'functional' => 'Funktionsprüfung',
            'safety' => 'Sicherheitsprüfung',
            'other' => 'Sonstige Prüfung',
        ];
    }

    /**
     * Log an action
     * 
     * @param string $action The action
     * @param string $entityType The entity type
     * @param string $entityId The entity ID
     * @param string $description The description
     */
    private function logAction(string $action, string $entityType, string $entityId, string $description): void
    {
        $auth = Application::getInstance()->getContainer()->resolve(\DDWB\Auth::class);
        $userId = $auth->id();
        
        $this->database->insert(
            'logs',
            [
                'user_id' => $userId,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'description' => $description,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'timestamp' => date('Y-m-d H:i:s'),
            ]
        );
    }
}
