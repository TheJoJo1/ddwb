<?php

declare(strict_types=1);

namespace DDWB\Modules\Inventory\Models;

use DDWB\Model;
use DDWB\Database;

/**
 * Device Model
 * 
 * Handles device data and operations
 */
final class Device extends Model
{
    protected string $table = 'devices';
    protected string $primaryKey = 'id';
    
    protected array $fillable = [
        'internal_id',
        'name',
        'description',
        'category_id',
        'serial_number',
        'status',
        'location',
        'purchase_date',
        'purchase_price',
        'warranty_expires',
        'notes',
    ];
    
    protected array $casts = [
        'category_id' => 'int',
        'purchase_price' => 'float',
        'status' => 'string',
    ];
    
    protected array $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'purchase_date',
        'warranty_expires',
    ];

    /**
     * Valid statuses
     */
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_IN_CASE = 'in_case';
    public const STATUS_LENT_OUT = 'lent_out';
    public const STATUS_MAINTENANCE = 'maintenance';

    /** @var array<string, string> */
    public static array $statusLabels = [
        self::STATUS_AVAILABLE => 'Verfügbar',
        self::STATUS_IN_CASE => 'Im Case',
        self::STATUS_LENT_OUT => 'Ausgeliehen',
        self::STATUS_MAINTENANCE => 'In Wartung',
    ];

    /** @var array<string, string> */
    public static array $statusColors = [
        self::STATUS_AVAILABLE => 'success',
        self::STATUS_IN_CASE => 'info',
        self::STATUS_LENT_OUT => 'warning',
        self::STATUS_MAINTENANCE => 'error',
    ];

    /**
     * Create a new Device instance
     * 
     * @param Database $database The database instance
     */
    public function __construct(Database $database)
    {
        parent::__construct($database);
    }

    /**
     * Get all devices
     * 
     * @param array $filters Filters to apply
     * @return array The devices
     */
    public function getAllDevices(array $filters = []): array
    {
        $query = 'SELECT d.*, c.name as category_name ' .
                 'FROM devices d ' .
                 'LEFT JOIN categories c ON d.category_id = c.id ' .
                 'WHERE d.deleted_at IS NULL';
        $params = [];

        if (isset($filters['status'])) {
            $query .= ' AND d.status = ?';
            $params[] = $filters['status'];
        }

        if (isset($filters['category_id'])) {
            $query .= ' AND d.category_id = ?';
            $params[] = $filters['category_id'];
        }

        if (isset($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query .= ' AND (d.internal_id LIKE ? OR d.name LIKE ? OR d.serial_number LIKE ? OR d.description LIKE ?)';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $query .= ' ORDER BY d.name ASC';

        return $this->database->select($query, $params);
    }

    /**
     * Get devices by status
     * 
     * @param string $status The status to filter by
     * @return array The devices with the specified status
     */
    public function getDevicesByStatus(string $status): array
    {
        return $this->database->select(
            'SELECT d.*, c.name as category_name ' .
            'FROM devices d ' .
            'LEFT JOIN categories c ON d.category_id = c.id ' .
            'WHERE d.status = ? AND d.deleted_at IS NULL ' .
            'ORDER BY d.name ASC',
            [$status]
        );
    }

    /**
     * Get available devices
     * 
     * @return array The available devices
     */
    public function getAvailableDevices(): array
    {
        return $this->getDevicesByStatus(self::STATUS_AVAILABLE);
    }

    /**
     * Get devices in cases
     * 
     * @return array The devices in cases
     */
    public function getDevicesInCases(): array
    {
        return $this->getDevicesByStatus(self::STATUS_IN_CASE);
    }

    /**
     * Get lent out devices
     * 
     * @return array The lent out devices
     */
    public function getLentOutDevices(): array
    {
        return $this->getDevicesByStatus(self::STATUS_LENT_OUT);
    }

    /**
     * Get devices in maintenance
     * 
     * @return array The devices in maintenance
     */
    public function getDevicesInMaintenance(): array
    {
        return $this->getDevicesByStatus(self::STATUS_MAINTENANCE);
    }

    /**
     * Get device by internal ID
     * 
     * @param string $internalId The internal ID
     * @return array|null The device or null if not found
     */
    public function getDeviceByInternalId(string $internalId): ?array
    {
        return $this->findBy('internal_id', $internalId);
    }

    /**
     * Get device by serial number
     * 
     * @param string $serialNumber The serial number
     * @return array|null The device or null if not found
     */
    public function getDeviceBySerialNumber(string $serialNumber): ?array
    {
        return $this->findBy('serial_number', $serialNumber);
    }

    /**
     * Create a new device
     * 
     * @param array $data The device data
     * @return int|string The new device ID
     */
    public function createDevice(array $data): int|string
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
     * Update a device
     * 
     * @param int $deviceId The device ID
     * @param array $data The device data
     * @return int The number of affected rows
     */
    public function updateDevice(int $deviceId, array $data): int
    {
        // Don't allow changing internal_id
        unset($data['internal_id']);

        return $this->update($deviceId, $data);
    }

    /**
     * Delete a device
     * 
     * @param int $deviceId The device ID
     * @return int The number of affected rows
     */
    public function deleteDevice(int $deviceId): int
    {
        // Soft delete
        return $this->softDelete($deviceId);
    }

    /**
     * Update device status
     * 
     * @param int $deviceId The device ID
     * @param string $status The new status
     * @return int The number of affected rows
     */
    public function updateStatus(int $deviceId, string $status): int
    {
        if (!isset(self::$statusLabels[$status])) {
            throw new \InvalidArgumentException("Invalid device status: {$status}");
        }

        return $this->database->update(
            $this->table,
            ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')],
            [$this->primaryKey => $deviceId]
        );
    }

    /**
     * Get device statistics
     * 
     * @return array The device statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->count(),
            'available' => $this->database->count($this->table, ['status' => self::STATUS_AVAILABLE, 'deleted_at' => null]),
            'in_case' => $this->database->count($this->table, ['status' => self::STATUS_IN_CASE, 'deleted_at' => null]),
            'lent_out' => $this->database->count($this->table, ['status' => self::STATUS_LENT_OUT, 'deleted_at' => null]),
            'maintenance' => $this->database->count($this->table, ['status' => self::STATUS_MAINTENANCE, 'deleted_at' => null]),
        ];
    }

    /**
     * Search devices
     * 
     * @param string $query The search query
     * @param array $filters Additional filters
     * @return array The matching devices
     */
    public function search(string $query, array $filters = []): array
    {
        $searchQuery = 'SELECT d.*, c.name as category_name ' .
                       'FROM devices d ' .
                       'LEFT JOIN categories c ON d.category_id = c.id ' .
                       'WHERE d.deleted_at IS NULL AND (' .
                       'd.internal_id LIKE ? OR ' .
                       'd.name LIKE ? OR ' .
                       'd.serial_number LIKE ? OR ' .
                       'd.description LIKE ?)';
        $params = ["%{$query}%", "%{$query}%", "%{$query}%", "%{$query}%"];

        if (isset($filters['status'])) {
            $searchQuery .= ' AND d.status = ?';
            $params[] = $filters['status'];
        }

        if (isset($filters['category_id'])) {
            $searchQuery .= ' AND d.category_id = ?';
            $params[] = $filters['category_id'];
        }

        $searchQuery .= ' ORDER BY d.name ASC';

        return $this->database->select($searchQuery, $params);
    }

    /**
     * Get devices with pagination
     * 
     * @param int $page The page number
     * @param int $perPage The number of devices per page
     * @param array $filters Filters to apply
     * @return array The paginated results
     */
    public function paginateDevices(int $page = 1, int $perPage = 25, array $filters = []): array
    {
        $offset = ($page - 1) * $perPage;

        $query = 'SELECT d.*, c.name as category_name ' .
                 'FROM devices d ' .
                 'LEFT JOIN categories c ON d.category_id = c.id ' .
                 'WHERE d.deleted_at IS NULL';
        $countQuery = 'SELECT COUNT(*) FROM devices d WHERE d.deleted_at IS NULL';
        $params = [];
        $countParams = [];

        if (isset($filters['status'])) {
            $query .= ' AND d.status = ?';
            $countQuery .= ' AND d.status = ?';
            $params[] = $filters['status'];
            $countParams[] = $filters['status'];
        }

        if (isset($filters['category_id'])) {
            $query .= ' AND d.category_id = ?';
            $countQuery .= ' AND d.category_id = ?';
            $params[] = $filters['category_id'];
            $countParams[] = $filters['category_id'];
        }

        if (isset($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query .= ' AND (d.internal_id LIKE ? OR d.name LIKE ? OR d.serial_number LIKE ? OR d.description LIKE ?)';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $query .= ' ORDER BY d.name ASC LIMIT ? OFFSET ?';
        $params[] = $perPage;
        $params[] = $offset;

        $total = (int)$this->database->selectValue($countQuery, $countParams);
        $devices = $this->database->select($query, $params);

        return [
            'data' => $devices,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int)ceil($total / $perPage),
        ];
    }

    /**
     * Get devices by category
     * 
     * @param int $categoryId The category ID
     * @return array The devices in the category
     */
    public function getDevicesByCategory(int $categoryId): array
    {
        return $this->database->select(
            'SELECT * FROM devices WHERE category_id = ? AND deleted_at IS NULL ORDER BY name ASC',
            [$categoryId]
        );
    }

    /**
     * Get devices not in any case
     * 
     * @return array The devices not in any case
     */
    public function getDevicesNotInCase(): array
    {
        return $this->database->select(
            'SELECT d.* FROM devices d ' .
            'LEFT JOIN case_device cd ON d.id = cd.device_id ' .
            'WHERE cd.id IS NULL AND d.deleted_at IS NULL ' .
            'ORDER BY d.name ASC'
        );
    }

    /**
     * Check if a device is in a case
     * 
     * @param int $deviceId The device ID
     * @return bool True if the device is in a case
     */
    public function isInCase(int $deviceId): bool
    {
        $count = (int)$this->database->selectValue(
            'SELECT COUNT(*) FROM case_device WHERE device_id = ?',
            [$deviceId]
        );

        return $count > 0;
    }

    /**
     * Check if a device is lent out
     * 
     * @param int $deviceId The device ID
     * @return bool True if the device is lent out
     */
    public function isLentOut(int $deviceId): bool
    {
        $count = (int)$this->database->selectValue(
            'SELECT COUNT(*) FROM rentals WHERE device_id = ? AND status = ? AND deleted_at IS NULL',
            [$deviceId, 'active']
        );

        return $count > 0;
    }

    /**
     * Check if a device is in maintenance
     * 
     * @param int $deviceId The device ID
     * @return bool True if the device is in maintenance
     */
    public function isInMaintenance(int $deviceId): bool
    {
        $count = (int)$this->database->selectValue(
            'SELECT COUNT(*) FROM maintenance WHERE device_id = ? AND deleted_at IS NULL',
            [$deviceId]
        );

        return $count > 0;
    }

    /**
     * Get the case a device is in
     * 
     * @param int $deviceId The device ID
     * @return array|null The case or null if not in a case
     */
    public function getCase(int $deviceId): ?array
    {
        $result = $this->database->selectOne(
            'SELECT c.* FROM cases c ' .
            'JOIN case_device cd ON c.id = cd.case_id ' .
            'WHERE cd.device_id = ? AND c.deleted_at IS NULL',
            [$deviceId]
        );

        return $result ?? null;
    }

    /**
     * Get the rental for a device
     * 
     * @param int $deviceId The device ID
     * @return array|null The rental or null if not lent out
     */
    public function getRental(int $deviceId): ?array
    {
        $result = $this->database->selectOne(
            'SELECT * FROM rentals WHERE device_id = ? AND status = ? AND deleted_at IS NULL',
            [$deviceId, 'active']
        );

        return $result ?? null;
    }

    /**
     * Get the maintenance records for a device
     * 
     * @param int $deviceId The device ID
     * @return array The maintenance records
     */
    public function getMaintenanceRecords(int $deviceId): array
    {
        return $this->database->select(
            'SELECT * FROM maintenance WHERE device_id = ? AND deleted_at IS NULL ORDER BY last_inspection_date DESC',
            [$deviceId]
        );
    }

    /**
     * Generate the next internal ID
     * 
     * @return string The next internal ID
     */
    public function generateNextInternalId(): string
    {
        return generate_internal_id('DEV', $this->count() + 1);
    }

    /**
     * Get devices with upcoming warranty expiration
     * 
     * @param int $days The number of days to look ahead
     * @return array The devices with upcoming warranty expiration
     */
    public function getDevicesWithUpcomingWarranty(int $days = 30): array
    {
        $date = date('Y-m-d', strtotime("+{$days} days"));

        return $this->database->select(
            'SELECT * FROM devices ' .
            'WHERE warranty_expires IS NOT NULL ' .
            'AND warranty_expires <= ? ' .
            'AND warranty_expires >= CURDATE() ' .
            'AND deleted_at IS NULL ' .
            'ORDER BY warranty_expires ASC',
            [$date]
        );
    }

    /**
     * Get devices with expired warranty
     * 
     * @return array The devices with expired warranty
     */
    public function getDevicesWithExpiredWarranty(): array
    {
        return $this->database->select(
            'SELECT * FROM devices ' .
            'WHERE warranty_expires IS NOT NULL ' .
            'AND warranty_expires < CURDATE() ' .
            'AND deleted_at IS NULL ' .
            'ORDER BY warranty_expires ASC'
        );
    }

    /**
     * Get device status label
     * 
     * @param string $status The status
     * @return string The label
     */
    public static function getStatusLabel(string $status): string
    {
        return self::$statusLabels[$status] ?? $status;
    }

    /**
     * Get device status color
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
            self::STATUS_IN_CASE,
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
}
