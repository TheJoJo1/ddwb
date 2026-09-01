<?php

declare(strict_types=1);

namespace DDWB\Modules\Rentals\Models;

use DDWB\Model;
use DDWB\Database;
use DDWB\Application;

/**
 * Rental Model
 * 
 * Handles rental data and operations
 */
final class Rental extends Model
{
    protected string $table = 'rentals';
    protected string $primaryKey = 'id';
    
    protected array $fillable = [
        'device_id',
        'case_id',
        'borrower',
        'borrower_email',
        'borrower_phone',
        'date_out',
        'expected_return',
        'actual_return',
        'status',
        'notes',
        'created_by',
        'returned_by',
    ];
    
    protected array $casts = [
        'device_id' => 'int',
        'case_id' => 'int',
        'created_by' => 'int',
        'returned_by' => 'int',
        'status' => 'string',
    ];
    
    protected array $dates = [
        'date_out',
        'expected_return',
        'actual_return',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Valid statuses
     */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_OVERDUE = 'overdue';

    /** @var array<string, string> */
    public static array $statusLabels = [
        self::STATUS_ACTIVE => 'Aktiv',
        self::STATUS_RETURNED => 'Zurückgegeben',
        self::STATUS_OVERDUE => 'Überfällig',
    ];

    /** @var array<string, string> */
    public static array $statusColors = [
        self::STATUS_ACTIVE => 'primary',
        self::STATUS_RETURNED => 'success',
        self::STATUS_OVERDUE => 'danger',
    ];

    /**
     * Create a new Rental instance
     * 
     * @param Database $database The database instance
     */
    public function __construct(Database $database)
    {
        parent::__construct($database);
    }

    /**
     * Get all rentals
     * 
     * @param array $filters Filters to apply
     * @return array The rentals
     */
    public function getAllRentals(array $filters = []): array
    {
        $query = 'SELECT r.*, ' .
                 'd.name as device_name, d.internal_id as device_internal_id, ' .
                 'c.name as case_name, c.internal_id as case_internal_id, ' .
                 'u.name as created_by_name, ru.name as returned_by_name ' .
                 'FROM rentals r ' .
                 'LEFT JOIN devices d ON r.device_id = d.id ' .
                 'LEFT JOIN cases c ON r.case_id = c.id ' .
                 'LEFT JOIN users u ON r.created_by = u.id ' .
                 'LEFT JOIN users ru ON r.returned_by = ru.id ' .
                 'WHERE r.deleted_at IS NULL';
        $params = [];

        if (isset($filters['status'])) {
            $query .= ' AND r.status = ?';
            $params[] = $filters['status'];
        }

        if (isset($filters['device_id'])) {
            $query .= ' AND r.device_id = ?';
            $params[] = $filters['device_id'];
        }

        if (isset($filters['case_id'])) {
            $query .= ' AND r.case_id = ?';
            $params[] = $filters['case_id'];
        }

        if (isset($filters['borrower'])) {
            $search = '%' . $filters['borrower'] . '%';
            $query .= ' AND (r.borrower LIKE ? OR r.borrower_email LIKE ?)';
            $params[] = $search;
            $params[] = $search;
        }

        $query .= ' ORDER BY r.date_out DESC, r.created_at DESC';

        return $this->database->select($query, $params);
    }

    /**
     * Get active rentals
     * 
     * @return array The active rentals
     */
    public function getActiveRentals(): array
    {
        return $this->getRentalsByStatus(self::STATUS_ACTIVE);
    }

    /**
     * Get overdue rentals
     * 
     * @return array The overdue rentals
     */
    public function getOverdueRentals(): array
    {
        return $this->getRentalsByStatus(self::STATUS_OVERDUE);
    }

    /**
     * Get returned rentals
     * 
     * @return array The returned rentals
     */
    public function getReturnedRentals(): array
    {
        return $this->getRentalsByStatus(self::STATUS_RETURNED);
    }

    /**
     * Get rentals by status
     * 
     * @param string $status The status to filter by
     * @return array The rentals with the specified status
     */
    public function getRentalsByStatus(string $status): array
    {
        return $this->database->select(
            'SELECT r.*, ' .
            'd.name as device_name, d.internal_id as device_internal_id, ' .
            'c.name as case_name, c.internal_id as case_internal_id, ' .
            'u.name as created_by_name ' .
            'FROM rentals r ' .
            'LEFT JOIN devices d ON r.device_id = d.id ' .
            'LEFT JOIN cases c ON r.case_id = c.id ' .
            'LEFT JOIN users u ON r.created_by = u.id ' .
            'WHERE r.status = ? AND r.deleted_at IS NULL ' .
            'ORDER BY r.date_out DESC',
            [$status]
        );
    }

    /**
     * Get rental by ID
     * 
     * @param int $rentalId The rental ID
     * @return array|null The rental or null if not found
     */
    public function getRentalById(int $rentalId): ?array
    {
        $rental = $this->database->selectOne(
            'SELECT r.*, ' .
            'd.name as device_name, d.internal_id as device_internal_id, d.status as device_status, ' .
            'c.name as case_name, c.internal_id as case_internal_id, c.status as case_status, ' .
            'u.name as created_by_name, u.email as created_by_email, ' .
            'ru.name as returned_by_name, ru.email as returned_by_email ' .
            'FROM rentals r ' .
            'LEFT JOIN devices d ON r.device_id = d.id ' .
            'LEFT JOIN cases c ON r.case_id = c.id ' .
            'LEFT JOIN users u ON r.created_by = u.id ' .
            'LEFT JOIN users ru ON r.returned_by = ru.id ' .
            'WHERE r.id = ? AND r.deleted_at IS NULL',
            [$rentalId]
        );

        return $rental ?? null;
    }

    /**
     * Get active rental for device
     * 
     * @param int $deviceId The device ID
     * @return array|null The active rental or null if not found
     */
    public function getActiveRentalForDevice(int $deviceId): ?array
    {
        $rental = $this->database->selectOne(
            'SELECT r.*, ' .
            'd.name as device_name, d.internal_id as device_internal_id, ' .
            'u.name as created_by_name ' .
            'FROM rentals r ' .
            'LEFT JOIN devices d ON r.device_id = d.id ' .
            'LEFT JOIN users u ON r.created_by = u.id ' .
            'WHERE r.device_id = ? AND r.status = ? AND r.deleted_at IS NULL ' .
            'ORDER BY r.date_out DESC',
            [$deviceId, self::STATUS_ACTIVE]
        );

        return $rental ?? null;
    }

    /**
     * Get active rental for case
     * 
     * @param int $caseId The case ID
     * @return array|null The active rental or null if not found
     */
    public function getActiveRentalForCase(int $caseId): ?array
    {
        $rental = $this->database->selectOne(
            'SELECT r.*, ' .
            'c.name as case_name, c.internal_id as case_internal_id, ' .
            'u.name as created_by_name ' .
            'FROM rentals r ' .
            'LEFT JOIN cases c ON r.case_id = c.id ' .
            'LEFT JOIN users u ON r.created_by = u.id ' .
            'WHERE r.case_id = ? AND r.status = ? AND r.deleted_at IS NULL ' .
            'ORDER BY r.date_out DESC',
            [$caseId, self::STATUS_ACTIVE]
        );

        return $rental ?? null;
    }

    /**
     * Check if device is lent out
     * 
     * @param int $deviceId The device ID
     * @return bool True if the device is lent out
     */
    public function isDeviceLentOut(int $deviceId): bool
    {
        $count = (int)$this->database->selectValue(
            'SELECT COUNT(*) FROM rentals WHERE device_id = ? AND status = ? AND deleted_at IS NULL',
            [$deviceId, self::STATUS_ACTIVE]
        );

        return $count > 0;
    }

    /**
     * Check if case is lent out
     * 
     * @param int $caseId The case ID
     * @return bool True if the case is lent out
     */
    public function isCaseLentOut(int $caseId): bool
    {
        $count = (int)$this->database->selectValue(
            'SELECT COUNT(*) FROM rentals WHERE case_id = ? AND status = ? AND deleted_at IS NULL',
            [$caseId, self::STATUS_ACTIVE]
        );

        return $count > 0;
    }

    /**
     * Create a new rental
     * 
     * @param array $data The rental data
     * @return int|string The new rental ID
     */
    public function createRental(array $data): int|string
    {
        // Validate data
        if (!isset($data['borrower']) || empty($data['borrower'])) {
            throw new \InvalidArgumentException('Borrower is required');
        }

        if (!isset($data['date_out']) || empty($data['date_out'])) {
            $data['date_out'] = date('Y-m-d H:i:s');
        }

        if (!isset($data['expected_return']) || empty($data['expected_return'])) {
            throw new \InvalidArgumentException('Expected return date is required');
        }

        // Set default status
        $data['status'] = self::STATUS_ACTIVE;

        // Set created_by if not provided
        if (!isset($data['created_by'])) {
            $auth = Application::getInstance()->getContainer()->resolve(\DDWB\Auth::class);
            $data['created_by'] = $auth->id();
        }

        // Begin transaction
        $this->database->beginTransaction();

        try {
            // Create the rental
            $rentalId = $this->create($data);

            // Update device status if device is specified
            if (isset($data['device_id']) && $data['device_id'] > 0) {
                $this->database->update(
                    'devices',
                    ['status' => 'lent_out', 'updated_at' => date('Y-m-d H:i:s')],
                    ['id' => $data['device_id']]
                );
            }

            // Update case status if case is specified
            if (isset($data['case_id']) && $data['case_id'] > 0) {
                $this->database->update(
                    'cases',
                    ['status' => 'lent_out', 'updated_at' => date('Y-m-d H:i:s')],
                    ['id' => $data['case_id']]
                );

                // Update all devices in the case
                $this->database->update(
                    'devices',
                    ['status' => 'lent_out', 'updated_at' => date('Y-m-d H:i:s')],
                    ['id' => $this->database->selectColumn('SELECT device_id FROM case_device WHERE case_id = ?', [$data['case_id']])]
                );
            }

            // Log the action
            $this->logAction('lend', 'rental', (string)$rentalId, "Created rental for " . ($data['device_id'] ?? $data['case_id']));

            // Commit transaction
            $this->database->commit();

            return $rentalId;
        } catch (\Exception $e) {
            // Rollback transaction
            $this->database->rollback();
            throw $e;
        }
    }

    /**
     * Return a rental
     * 
     * @param int $rentalId The rental ID
     * @param array $data The return data
     * @return int The number of affected rows
     */
    public function returnRental(int $rentalId, array $data = []): int
    {
        $rental = $this->getRentalById($rentalId);

        if ($rental === null) {
            throw new \RuntimeException('Rental not found');
        }

        if ($rental['status'] !== self::STATUS_ACTIVE) {
            throw new \RuntimeException('Rental is not active');
        }

        // Begin transaction
        $this->database->beginTransaction();

        try {
            $updateData = [
                'status' => self::STATUS_RETURNED,
                'actual_return' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if (isset($data['returned_by'])) {
                $updateData['returned_by'] = $data['returned_by'];
            } else {
                $auth = Application::getInstance()->getContainer()->resolve(\DDWB\Auth::class);
                $updateData['returned_by'] = $auth->id();
            }

            if (isset($data['notes'])) {
                $updateData['notes'] = $data['notes'];
            }

            // Update the rental
            $result = $this->update($rentalId, $updateData);

            // Update device status if device is specified
            if ($rental['device_id'] > 0) {
                $this->database->update(
                    'devices',
                    ['status' => 'available', 'updated_at' => date('Y-m-d H:i:s')],
                    ['id' => $rental['device_id']]
                );
            }

            // Update case status if case is specified
            if ($rental['case_id'] > 0) {
                $this->database->update(
                    'cases',
                    ['status' => 'available', 'updated_at' => date('Y-m-d H:i:s')],
                    ['id' => $rental['case_id']]
                );

                // Update all devices in the case to available
                $deviceIds = $this->database->selectColumn(
                    'SELECT device_id FROM case_device WHERE case_id = ?',
                    [$rental['case_id']]
                );

                if (!empty($deviceIds)) {
                    $this->database->update(
                        'devices',
                        ['status' => 'available', 'updated_at' => date('Y-m-d H:i:s')],
                        ['id' => $deviceIds]
                    );
                }
            }

            // Log the action
            $this->logAction('return', 'rental', (string)$rentalId, "Returned rental for " . ($rental['device_id'] ?? $rental['case_id']));

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
     * Update a rental
     * 
     * @param int $rentalId The rental ID
     * @param array $data The rental data
     * @return int The number of affected rows
     */
    public function updateRental(int $rentalId, array $data): int
    {
        // Don't allow changing certain fields
        unset($data['status']);
        unset($data['created_by']);
        unset($data['created_at']);

        return $this->update($rentalId, $data);
    }

    /**
     * Delete a rental
     * 
     * @param int $rentalId The rental ID
     * @return int The number of affected rows
     */
    public function deleteRental(int $rentalId): int
    {
        // Soft delete
        return $this->softDelete($rentalId);
    }

    /**
     * Get rental statistics
     * 
     * @return array The rental statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->count(),
            'active' => $this->database->count($this->table, ['status' => self::STATUS_ACTIVE, 'deleted_at' => null]),
            'returned' => $this->database->count($this->table, ['status' => self::STATUS_RETURNED, 'deleted_at' => null]),
            'overdue' => $this->database->count($this->table, ['status' => self::STATUS_OVERDUE, 'deleted_at' => null]),
        ];
    }

    /**
     * Search rentals
     * 
     * @param string $query The search query
     * @param array $filters Additional filters
     * @return array The matching rentals
     */
    public function search(string $query, array $filters = []): array
    {
        $searchQuery = 'SELECT r.*, ' .
                       'd.name as device_name, d.internal_id as device_internal_id, ' .
                       'c.name as case_name, c.internal_id as case_internal_id, ' .
                       'u.name as created_by_name ' .
                       'FROM rentals r ' .
                       'LEFT JOIN devices d ON r.device_id = d.id ' .
                       'LEFT JOIN cases c ON r.case_id = c.id ' .
                       'LEFT JOIN users u ON r.created_by = u.id ' .
                       'WHERE r.deleted_at IS NULL AND (' .
                       'r.borrower LIKE ? OR ' .
                       'r.borrower_email LIKE ? OR ' .
                       'd.name LIKE ? OR ' .
                       'd.internal_id LIKE ? OR ' .
                       'c.name LIKE ? OR ' .
                       'c.internal_id LIKE ?)';
        $params = ["%{$query}%", "%{$query}%", "%{$query}%", "%{$query}%", "%{$query}%", "%{$query}%"];

        if (isset($filters['status'])) {
            $searchQuery .= ' AND r.status = ?';
            $params[] = $filters['status'];
        }

        $searchQuery .= ' ORDER BY r.date_out DESC';

        return $this->database->select($searchQuery, $params);
    }

    /**
     * Get rentals with pagination
     * 
     * @param int $page The page number
     * @param int $perPage The number of rentals per page
     * @param array $filters Filters to apply
     * @return array The paginated results
     */
    public function paginateRentals(int $page = 1, int $perPage = 25, array $filters = []): array
    {
        $offset = ($page - 1) * $perPage;

        $query = 'SELECT r.*, ' .
                 'd.name as device_name, d.internal_id as device_internal_id, ' .
                 'c.name as case_name, c.internal_id as case_internal_id, ' .
                 'u.name as created_by_name ' .
                 'FROM rentals r ' .
                 'LEFT JOIN devices d ON r.device_id = d.id ' .
                 'LEFT JOIN cases c ON r.case_id = c.id ' .
                 'LEFT JOIN users u ON r.created_by = u.id ' .
                 'WHERE r.deleted_at IS NULL';
        $countQuery = 'SELECT COUNT(*) FROM rentals r WHERE r.deleted_at IS NULL';
        $params = [];
        $countParams = [];

        if (isset($filters['status'])) {
            $query .= ' AND r.status = ?';
            $countQuery .= ' AND r.status = ?';
            $params[] = $filters['status'];
            $countParams[] = $filters['status'];
        }

        if (isset($filters['borrower'])) {
            $search = '%' . $filters['borrower'] . '%';
            $query .= ' AND (r.borrower LIKE ? OR r.borrower_email LIKE ?)';
            $params[] = $search;
            $params[] = $search;
        }

        $query .= ' ORDER BY r.date_out DESC LIMIT ? OFFSET ?';
        $params[] = $perPage;
        $params[] = $offset;

        $total = (int)$this->database->selectValue($countQuery, $countParams);
        $rentals = $this->database->select($query, $params);

        return [
            'data' => $rentals,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int)ceil($total / $perPage),
        ];
    }

    /**
     * Get overdue rentals that need to be updated
     * 
     * @return int The number of rentals updated
     */
    public function updateOverdueStatuses(): int
    {
        // Find rentals that should be overdue
        $overdueRentals = $this->database->select(
            'SELECT id FROM rentals WHERE status = ? AND expected_return < ? AND deleted_at IS NULL',
            [self::STATUS_ACTIVE, date('Y-m-d H:i:s')]
        );

        $count = 0;
        foreach ($overdueRentals as $rental) {
            $this->database->update(
                'rentals',
                ['status' => self::STATUS_OVERDUE, 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => $rental['id']]
            );
            $count++;
        }

        return $count;
    }

    /**
     * Check if a device or case can be lent out
     * 
     * @param int|null $deviceId The device ID
     * @param int|null $caseId The case ID
     * @return array Validation result with message
     */
    public function validateLendable(?int $deviceId = null, ?int $caseId = null): array
    {
        if ($deviceId > 0) {
            // Check if device exists
            $device = $this->database->selectOne(
                'SELECT * FROM devices WHERE id = ? AND deleted_at IS NULL',
                [$deviceId]
            );

            if ($device === null) {
                return ['valid' => false, 'message' => 'Gerät nicht gefunden'];
            }

            // Check if device is already lent out
            if ($this->isDeviceLentOut($deviceId)) {
                return ['valid' => false, 'message' => 'Gerät ist bereits ausgeliehen'];
            }

            // Check if device is in maintenance
            if ($device['status'] === 'maintenance') {
                return ['valid' => false, 'message' => 'Gerät befindet sich in Wartung'];
            }

            // Check if device is in a case that is lent out
            $inCase = $this->database->selectOne(
                'SELECT cd.case_id FROM case_device cd WHERE cd.device_id = ?',
                [$deviceId]
            );

            if ($inCase !== null && $this->isCaseLentOut($inCase['case_id'])) {
                return ['valid' => false, 'message' => 'Gerät befindet sich in einem ausgeliehenen Case'];
            }
        }

        if ($caseId > 0) {
            // Check if case exists
            $case = $this->database->selectOne(
                'SELECT * FROM cases WHERE id = ? AND deleted_at IS NULL',
                [$caseId]
            );

            if ($case === null) {
                return ['valid' => false, 'message' => 'Case nicht gefunden'];
            }

            // Check if case is already lent out
            if ($this->isCaseLentOut($caseId)) {
                return ['valid' => false, 'message' => 'Case ist bereits ausgeliehen'];
            }

            // Check if case is in maintenance
            if ($case['status'] === 'maintenance') {
                return ['valid' => false, 'message' => 'Case befindet sich in Wartung'];
            }
        }

        return ['valid' => true, 'message' => 'Verfügbar für Ausleihe'];
    }

    /**
     * Get rental status label
     * 
     * @param string $status The status
     * @return string The label
     */
    public static function getStatusLabel(string $status): string
    {
        return self::$statusLabels[$status] ?? $status;
    }

    /**
     * Get rental status color
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
            self::STATUS_ACTIVE,
            self::STATUS_RETURNED,
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
