<?php

declare(strict_types=1);

namespace DDWB\Modules\Cases\Models;

use DDWB\Model;
use DDWB\Database;

/**
 * Case Model
 * 
 * Handles case data and operations
 */
final class CaseModel extends Model
{
    protected string $table = 'cases';
    protected string $primaryKey = 'id';
    
    protected array $fillable = [
        'internal_id',
        'name',
        'description',
        'status',
        'location',
        'notes',
    ];
    
    protected array $casts = [
        'status' => 'string',
    ];
    
    protected array $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Valid statuses
     */
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_LENT_OUT = 'lent_out';
    public const STATUS_MAINTENANCE = 'maintenance';

    /** @var array<string, string> */
    public static array $statusLabels = [
        self::STATUS_AVAILABLE => 'Verfügbar',
        self::STATUS_LENT_OUT => 'Ausgeliehen',
        self::STATUS_MAINTENANCE => 'In Wartung',
    ];

    /** @var array<string, string> */
    public static array $statusColors = [
        self::STATUS_AVAILABLE => 'success',
        self::STATUS_LENT_OUT => 'warning',
        self::STATUS_MAINTENANCE => 'error',
    ];

    /**
     * Create a new CaseModel instance
     * 
     * @param Database $database The database instance
     */
    public function __construct(Database $database)
    {
        parent::__construct($database);
    }

    /**
     * Get all cases
     * 
     * @param array $filters Filters to apply
     * @return array The cases
     */
    public function getAllCases(array $filters = []): array
    {
        $query = 'SELECT * FROM cases WHERE deleted_at IS NULL';
        $params = [];

        if (isset($filters['status'])) {
            $query .= ' AND status = ?';
            $params[] = $filters['status'];
        }

        if (isset($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query .= ' AND (internal_id LIKE ? OR name LIKE ? OR description LIKE ?)';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $query .= ' ORDER BY name ASC';

        return $this->database->select($query, $params);
    }

    /**
     * Get case by internal ID
     * 
     * @param string $internalId The internal ID
     * @return array|null The case or null if not found
     */
    public function getCaseByInternalId(string $internalId): ?array
    {
        return $this->findBy('internal_id', $internalId);
    }

    /**
     * Create a new case
     * 
     * @param array $data The case data
     * @return int|string The new case ID
     */
    public function createCase(array $data): int|string
    {
        // Generate internal ID if not provided
        if (!isset($data['internal_id']) || empty($data['internal_id'])) {
            $data['internal_id'] = $this->generateNextInternalId();
        }

        // Set default status
        $data['status'] = $data['status'] ?? self::STATUS_AVAILABLE;

        // Set default values
        $data['location'] = $data['location'] ?? '';
        $data['notes'] = $data['notes'] ?? '';

        return $this->create($data);
    }

    /**
     * Update a case
     * 
     * @param int $caseId The case ID
     * @param array $data The case data
     * @return int The number of affected rows
     */
    public function updateCase(int $caseId, array $data): int
    {
        // Don't allow changing internal_id
        unset($data['internal_id']);

        return $this->update($caseId, $data);
    }

    /**
     * Delete a case
     * 
     * @param int $caseId The case ID
     * @return int The number of affected rows
     */
    public function deleteCase(int $caseId): int
    {
        // Soft delete
        return $this->softDelete($caseId);
    }

    /**
     * Get case by ID with contained devices
     * 
     * @param int $caseId The case ID
     * @return array|null The case with devices or null if not found
     */
    public function getCaseWithDevices(int $caseId): ?array
    {
        $case = $this->find($caseId);
        
        if ($case === null) {
            return null;
        }

        $devices = $this->database->select(
            'SELECT d.*, cd.assigned_at, cd.assigned_by, cd.notes as assignment_notes ' .
            'FROM devices d ' .
            'JOIN case_device cd ON d.id = cd.device_id ' .
            'WHERE cd.case_id = ? AND d.deleted_at IS NULL ' .
            'ORDER BY cd.assigned_at ASC',
            [$caseId]
        );

        $case['devices'] = $devices;
        $case['device_count'] = count($devices);

        return $case;
    }

    /**
     * Get devices in a case
     * 
     * @param int $caseId The case ID
     * @return array The devices in the case
     */
    public function getDevicesInCase(int $caseId): array
    {
        return $this->database->select(
            'SELECT d.* FROM devices d ' .
            'JOIN case_device cd ON d.id = cd.device_id ' .
            'WHERE cd.case_id = ? AND d.deleted_at IS NULL ' .
            'ORDER BY d.name ASC',
            [$caseId]
        );
    }

    /**
     * Get devices not in this case
     * 
     * @param int $caseId The case ID
     * @return array The devices not in this case
     */
    public function getDevicesNotInCase(int $caseId): array
    {
        return $this->database->select(
            'SELECT d.* FROM devices d ' .
            'WHERE d.id NOT IN (SELECT device_id FROM case_device WHERE case_id = ?) ' .
            'AND d.deleted_at IS NULL ' .
            'AND d.status != ? ' .
            'ORDER BY d.name ASC',
            [$caseId, 'lent_out']
        );
    }

    /**
     * Add device to case
     * 
     * @param int $caseId The case ID
     * @param int $deviceId The device ID
     * @param int|null $assignedBy The user ID who assigned the device
     * @param string|null $notes Assignment notes
     * @return int|string The assignment ID
     */
    public function addDeviceToCase(int $caseId, int $deviceId, ?int $assignedBy = null, ?string $notes = null): int|string
    {
        // Check if device is already in this case
        $existing = $this->database->selectOne(
            'SELECT id FROM case_device WHERE case_id = ? AND device_id = ?',
            [$caseId, $deviceId]
        );

        if ($existing !== null) {
            throw new \RuntimeException("Device is already in this case");
        }

        // Check if device is in another case
        $inOtherCase = $this->database->selectOne(
            'SELECT case_id FROM case_device WHERE device_id = ?',
            [$deviceId]
        );

        if ($inOtherCase !== null) {
            throw new \RuntimeException("Device is already in another case");
        }

        // Check if device is lent out
        $lentOut = $this->database->selectOne(
            'SELECT id FROM rentals WHERE device_id = ? AND status = ? AND deleted_at IS NULL',
            [$deviceId, 'active']
        );

        if ($lentOut !== null) {
            throw new \RuntimeException("Device is currently lent out");
        }

        // Create the assignment
        $assignmentId = $this->database->insert(
            'case_device',
            [
                'case_id' => $caseId,
                'device_id' => $deviceId,
                'assigned_by' => $assignedBy,
                'notes' => $notes,
                'assigned_at' => date('Y-m-d H:i:s'),
            ]
        );

        // Update device status
        $this->database->update(
            'devices',
            ['status' => 'in_case', 'updated_at' => date('Y-m-d H:i:s')],
            ['id' => $deviceId]
        );

        // Log the action
        $this->logAction('case_assign', 'case_device', (string)$assignmentId, "Added device {$deviceId} to case {$caseId}");

        return $assignmentId;
    }

    /**
     * Remove device from case
     * 
     * @param int $caseId The case ID
     * @param int $deviceId The device ID
     * @param int|null $removedBy The user ID who removed the device
     * @return int The number of affected rows
     */
    public function removeDeviceFromCase(int $caseId, int $deviceId, ?int $removedBy = null): int
    {
        // Delete the assignment
        $result = $this->database->delete(
            'case_device',
            ['case_id' => $caseId, 'device_id' => $deviceId]
        );

        if ($result > 0) {
            // Update device status to available (triggers will handle this, but we do it explicitly for clarity)
            $this->database->update(
                'devices',
                ['status' => 'available', 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => $deviceId]
            );

            // Log the action
            $this->logAction('case_remove', 'case_device', (string)$deviceId, "Removed device {$deviceId} from case {$caseId}");
        }

        return $result;
    }

    /**
     * Check if a device is in a case
     * 
     * @param int $deviceId The device ID
     * @return bool True if the device is in a case
     */
    public function isDeviceInCase(int $deviceId): bool
    {
        $count = (int)$this->database->selectValue(
            'SELECT COUNT(*) FROM case_device WHERE device_id = ?',
            [$deviceId]
        );

        return $count > 0;
    }

    /**
     * Get case statistics
     * 
     * @return array The case statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->count(),
            'available' => $this->database->count($this->table, ['status' => self::STATUS_AVAILABLE, 'deleted_at' => null]),
            'lent_out' => $this->database->count($this->table, ['status' => self::STATUS_LENT_OUT, 'deleted_at' => null]),
            'maintenance' => $this->database->count($this->table, ['status' => self::STATUS_MAINTENANCE, 'deleted_at' => null]),
        ];
    }

    /**
     * Search cases
     * 
     * @param string $query The search query
     * @param array $filters Additional filters
     * @return array The matching cases
     */
    public function search(string $query, array $filters = []): array
    {
        $searchQuery = 'SELECT * FROM cases WHERE deleted_at IS NULL AND (' .
                       'internal_id LIKE ? OR ' .
                       'name LIKE ? OR ' .
                       'description LIKE ?)';
        $params = ["%{$query}%", "%{$query}%", "%{$query}%"];

        if (isset($filters['status'])) {
            $searchQuery .= ' AND status = ?';
            $params[] = $filters['status'];
        }

        $searchQuery .= ' ORDER BY name ASC';

        return $this->database->select($searchQuery, $params);
    }

    /**
     * Get cases with pagination
     * 
     * @param int $page The page number
     * @param int $perPage The number of cases per page
     * @param array $filters Filters to apply
     * @return array The paginated results
     */
    public function paginateCases(int $page = 1, int $perPage = 25, array $filters = []): array
    {
        $offset = ($page - 1) * $perPage;

        $query = 'SELECT * FROM cases WHERE deleted_at IS NULL';
        $countQuery = 'SELECT COUNT(*) FROM cases WHERE deleted_at IS NULL';
        $params = [];
        $countParams = [];

        if (isset($filters['status'])) {
            $query .= ' AND status = ?';
            $countQuery .= ' AND status = ?';
            $params[] = $filters['status'];
            $countParams[] = $filters['status'];
        }

        if (isset($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query .= ' AND (internal_id LIKE ? OR name LIKE ? OR description LIKE ?)';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $query .= ' ORDER BY name ASC LIMIT ? OFFSET ?';
        $params[] = $perPage;
        $params[] = $offset;

        $total = (int)$this->database->selectValue($countQuery, $countParams);
        $cases = $this->database->select($query, $params);

        return [
            'data' => $cases,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int)ceil($total / $perPage),
        ];
    }

    /**
     * Generate the next internal ID
     * 
     * @return string The next internal ID
     */
    public function generateNextInternalId(): string
    {
        return generate_internal_id('CASE', $this->count() + 1);
    }

    /**
     * Get case status label
     * 
     * @param string $status The status
     * @return string The label
     */
    public static function getStatusLabel(string $status): string
    {
        return self::$statusLabels[$status] ?? $status;
    }

    /**
     * Get case status color
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
            self::STATUS_AVAILABLE,
            self::STATUS_LENT_OUT,
            self::STATUS_MAINTENANCE,
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
        $auth = Application::getInstance()->getContainer()->resolve(Auth::class);
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
