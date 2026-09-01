<?php

declare(strict_types=1);

namespace DDWB\Modules\Packlists\Models;

use DDWB\Model;
use DDWB\Database;
use DDWB\Application;

/**
 * Packlist Model
 * 
 * Handles packlist data and operations
 */
final class Packlist extends Model
{
    protected string $table = 'packlists';
    protected string $primaryKey = 'id';
    
    protected array $fillable = [
        'name',
        'description',
        'status',
        'notes',
        'created_by',
    ];
    
    protected array $casts = [
        'created_by' => 'int',
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
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_ARCHIVED = 'archived';

    /** @var array<string, string> */
    public static array $statusLabels = [
        self::STATUS_DRAFT => 'Entwurf',
        self::STATUS_ACTIVE => 'Aktiv',
        self::STATUS_COMPLETED => 'Abgeschlossen',
        self::STATUS_ARCHIVED => 'Archiviert',
    ];

    /** @var array<string, string> */
    public static array $statusColors = [
        self::STATUS_DRAFT => 'secondary',
        self::STATUS_ACTIVE => 'primary',
        self::STATUS_COMPLETED => 'success',
        self::STATUS_ARCHIVED => 'dark',
    ];

    /**
     * Valid item types
     */
    public const ITEM_TYPE_DEVICE = 'device';
    public const ITEM_TYPE_CASE = 'case';

    /** @var array<string, string> */
    public static array $itemTypeLabels = [
        self::ITEM_TYPE_DEVICE => 'Gerät',
        self::ITEM_TYPE_CASE => 'Case',
    ];

    /**
     * Create a new Packlist instance
     * 
     * @param Database $database The database instance
     */
    public function __construct(Database $database)
    {
        parent::__construct($database);
    }

    /**
     * Get all packlists
     * 
     * @param array $filters Filters to apply
     * @return array The packlists
     */
    public function getAllPacklists(array $filters = []): array
    {
        $query = 'SELECT p.*, ' .
                 'u.name as created_by_name, u.email as created_by_email ' .
                 'FROM packlists p ' .
                 'LEFT JOIN users u ON p.created_by = u.id ' .
                 'WHERE p.deleted_at IS NULL';
        $params = [];

        if (isset($filters['status'])) {
            $query .= ' AND p.status = ?';
            $params[] = $filters['status'];
        }

        if (isset($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query .= ' AND (p.name LIKE ? OR p.description LIKE ?)';
            $params[] = $search;
            $params[] = $search;
        }

        $query .= ' ORDER BY p.name ASC';

        return $this->database->select($query, $params);
    }

    /**
     * Get packlist by ID with items
     * 
     * @param int $packlistId The packlist ID
     * @return array|null The packlist with items or null if not found
     */
    public function getPacklistWithItems(int $packlistId): ?array
    {
        $packlist = $this->find($packlistId);
        
        if ($packlist === null) {
            return null;
        }

        $items = $this->database->select(
            'SELECT pi.*, ' .
            'd.name as device_name, d.internal_id as device_internal_id, ' .
            'c.name as case_name, c.internal_id as case_internal_id ' .
            'FROM packlist_items pi ' .
            'LEFT JOIN devices d ON pi.item_type = ? AND pi.item_id = d.id ' .
            'LEFT JOIN cases c ON pi.item_type = ? AND pi.item_id = c.id ' .
            'WHERE pi.packlist_id = ? ' .
            'ORDER BY pi.sort_order ASC, pi.created_at ASC',
            ['device', 'case', $packlistId]
        );

        $packlist['items'] = $items;
        $packlist['item_count'] = count($items);

        // Calculate checked count
        $checkedCount = 0;
        foreach ($items as $item) {
            if ($item['checked']) {
                $checkedCount++;
            }
        }
        $packlist['checked_count'] = $checkedCount;

        return $packlist;
    }

    /**
     * Create a new packlist
     * 
     * @param array $data The packlist data
     * @return int|string The new packlist ID
     */
    public function createPacklist(array $data): int|string
    {
        // Set default status
        $data['status'] = $data['status'] ?? self::STATUS_DRAFT;

        // Set created_by if not provided
        if (!isset($data['created_by'])) {
            $auth = Application::getInstance()->getContainer()->resolve(\DDWB\Auth::class);
            $data['created_by'] = $auth->id();
        }

        // Set default values
        $data['description'] = $data['description'] ?? '';
        $data['notes'] = $data['notes'] ?? '';

        // Begin transaction
        $this->database->beginTransaction();

        try {
            // Create the packlist
            $packlistId = $this->create($data);

            // Log the action
            $this->logAction('packlist_create', 'packlist', (string)$packlistId, 
                "Created packlist: {$data['name']}");

            // Commit transaction
            $this->database->commit();

            return $packlistId;
        } catch (\Exception $e) {
            // Rollback transaction
            $this->database->rollback();
            throw $e;
        }
    }

    /**
     * Update a packlist
     * 
     * @param int $packlistId The packlist ID
     * @param array $data The packlist data
     * @return int The number of affected rows
     */
    public function updatePacklist(int $packlistId, array $data): int
    {
        // Don't allow changing certain fields
        unset($data['created_by']);
        unset($data['created_at']);

        // Begin transaction
        $this->database->beginTransaction();

        try {
            // Update the packlist
            $result = $this->update($packlistId, $data);

            // Log the action
            $this->logAction('packlist_update', 'packlist', (string)$packlistId, 
                "Updated packlist: {$data['name']}");

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
     * Delete a packlist
     * 
     * @param int $packlistId The packlist ID
     * @return int The number of affected rows
     */
    public function deletePacklist(int $packlistId): int
    {
        // Soft delete
        return $this->softDelete($packlistId);
    }

    /**
     * Add item to packlist
     * 
     * @param int $packlistId The packlist ID
     * @param string $itemType The item type (device or case)
     * @param int $itemId The item ID
     * @param int $quantity The quantity
     * @param string|null $notes Item notes
     * @return int|string The packlist item ID
     */
    public function addItemToPacklist(int $packlistId, string $itemType, int $itemId, int $quantity = 1, ?string $notes = null): int|string
    {
        // Validate item type
        if (!in_array($itemType, [self::ITEM_TYPE_DEVICE, self::ITEM_TYPE_CASE])) {
            throw new \InvalidArgumentException('Invalid item type');
        }

        // Check if item already exists in packlist
        $existing = $this->database->selectOne(
            'SELECT id FROM packlist_items WHERE packlist_id = ? AND item_type = ? AND item_id = ?',
            [$packlistId, $itemType, $itemId]
        );

        if ($existing !== null) {
            throw new \RuntimeException('Item already exists in this packlist');
        }

        // Get the next sort order
        $nextSortOrder = (int)$this->database->selectValue(
            'SELECT COALESCE(MAX(sort_order), 0) + 1 FROM packlist_items WHERE packlist_id = ?',
            [$packlistId]
        );

        // Create the packlist item
        $itemId = $this->database->insert(
            'packlist_items',
            [
                'packlist_id' => $packlistId,
                'item_type' => $itemType,
                'item_id' => $itemId,
                'quantity' => $quantity,
                'sort_order' => $nextSortOrder,
                'checked' => 0,
                'notes' => $notes,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );

        // Log the action
        $this->logAction('packlist_add_item', 'packlist_item', (string)$itemId, 
            "Added {$itemType} {$itemId} to packlist {$packlistId}");

        return $itemId;
    }

    /**
     * Remove item from packlist
     * 
     * @param int $packlistId The packlist ID
     * @param int $itemId The packlist item ID
     * @return int The number of affected rows
     */
    public function removeItemFromPacklist(int $packlistId, int $itemId): int
    {
        $result = $this->database->delete(
            'packlist_items',
            ['id' => $itemId, 'packlist_id' => $packlistId]
        );

        if ($result > 0) {
            // Log the action
            $this->logAction('packlist_remove_item', 'packlist_item', (string)$itemId, 
                "Removed item {$itemId} from packlist {$packlistId}");
        }

        return $result;
    }

    /**
     * Update packlist item
     * 
     * @param int $itemId The packlist item ID
     * @param array $data The item data
     * @return int The number of affected rows
     */
    public function updatePacklistItem(int $itemId, array $data): int
    {
        // Don't allow changing certain fields
        unset($data['packlist_id']);
        unset($data['item_type']);
        unset($data['item_id']);
        unset($data['created_at']);

        return $this->database->update(
            'packlist_items',
            $data,
            ['id' => $itemId]
        );
    }

    /**
     * Toggle item checked status
     * 
     * @param int $itemId The packlist item ID
     * @return int The number of affected rows
     */
    public function toggleItemChecked(int $itemId): int
    {
        $item = $this->database->selectOne(
            'SELECT checked FROM packlist_items WHERE id = ?',
            [$itemId]
        );

        if ($item === null) {
            return 0;
        }

        return $this->database->update(
            'packlist_items',
            ['checked' => $item['checked'] ? 0 : 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['id' => $itemId]
        );
    }

    /**
     * Reorder packlist items
     * 
     * @param int $packlistId The packlist ID
     * @param array $itemOrders Array of item IDs with new sort orders
     * @return int The number of affected rows
     */
    public function reorderItems(int $packlistId, array $itemOrders): int
    {
        $total = 0;

        foreach ($itemOrders as $itemId => $sortOrder) {
            $result = $this->database->update(
                'packlist_items',
                ['sort_order' => $sortOrder, 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => $itemId, 'packlist_id' => $packlistId]
            );
            $total += $result;
        }

        return $total;
    }

    /**
     * Get packlist statistics
     * 
     * @return array The packlist statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->count(),
            'draft' => $this->database->count($this->table, ['status' => self::STATUS_DRAFT, 'deleted_at' => null]),
            'active' => $this->database->count($this->table, ['status' => self::STATUS_ACTIVE, 'deleted_at' => null]),
            'completed' => $this->database->count($this->table, ['status' => self::STATUS_COMPLETED, 'deleted_at' => null]),
            'archived' => $this->database->count($this->table, ['status' => self::STATUS_ARCHIVED, 'deleted_at' => null]),
        ];
    }

    /**
     * Search packlists
     * 
     * @param string $query The search query
     * @param array $filters Additional filters
     * @return array The matching packlists
     */
    public function search(string $query, array $filters = []): array
    {
        $searchQuery = 'SELECT p.*, ' .
                       'u.name as created_by_name ' .
                       'FROM packlists p ' .
                       'LEFT JOIN users u ON p.created_by = u.id ' .
                       'WHERE p.deleted_at IS NULL AND (' .
                       'p.name LIKE ? OR ' .
                       'p.description LIKE ? OR ' .
                       'p.notes LIKE ?)';
        $params = ["%{$query}%", "%{$query}%", "%{$query}%"];

        if (isset($filters['status'])) {
            $searchQuery .= ' AND p.status = ?';
            $params[] = $filters['status'];
        }

        $searchQuery .= ' ORDER BY p.name ASC';

        return $this->database->select($searchQuery, $params);
    }

    /**
     * Get packlists with pagination
     * 
     * @param int $page The page number
     * @param int $perPage The number of packlists per page
     * @param array $filters Filters to apply
     * @return array The paginated results
     */
    public function paginatePacklists(int $page = 1, int $perPage = 25, array $filters = []): array
    {
        $offset = ($page - 1) * $perPage;

        $query = 'SELECT p.*, ' .
                 'u.name as created_by_name, u.email as created_by_email ' .
                 'FROM packlists p ' .
                 'LEFT JOIN users u ON p.created_by = u.id ' .
                 'WHERE p.deleted_at IS NULL';
        $countQuery = 'SELECT COUNT(*) FROM packlists p WHERE p.deleted_at IS NULL';
        $params = [];
        $countParams = [];

        if (isset($filters['status'])) {
            $query .= ' AND p.status = ?';
            $countQuery .= ' AND p.status = ?';
            $params[] = $filters['status'];
            $countParams[] = $filters['status'];
        }

        if (isset($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query .= ' AND (p.name LIKE ? OR p.description LIKE ?)';
            $params[] = $search;
            $params[] = $search;
        }

        $query .= ' ORDER BY p.name ASC LIMIT ? OFFSET ?';
        $params[] = $perPage;
        $params[] = $offset;

        $total = (int)$this->database->selectValue($countQuery, $countParams);
        $packlists = $this->database->select($query, $params);

        return [
            'data' => $packlists,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int)ceil($total / $perPage),
        ];
    }

    /**
     * Get available devices for packlist
     * 
     * @return array The available devices
     */
    public function getAvailableDevices(): array
    {
        return $this->database->select(
            'SELECT d.* FROM devices d WHERE d.deleted_at IS NULL ORDER BY d.name ASC'
        );
    }

    /**
     * Get available cases for packlist
     * 
     * @return array The available cases
     */
    public function getAvailableCases(): array
    {
        return $this->database->select(
            'SELECT c.* FROM cases c WHERE c.deleted_at IS NULL ORDER BY c.name ASC'
        );
    }

    /**
     * Check if device is already in packlist
     * 
     * @param int $packlistId The packlist ID
     * @param int $deviceId The device ID
     * @return bool True if the device is already in the packlist
     */
    public function isDeviceInPacklist(int $packlistId, int $deviceId): bool
    {
        $count = (int)$this->database->selectValue(
            'SELECT COUNT(*) FROM packlist_items WHERE packlist_id = ? AND item_type = ? AND item_id = ?',
            [$packlistId, self::ITEM_TYPE_DEVICE, $deviceId]
        );

        return $count > 0;
    }

    /**
     * Check if case is already in packlist
     * 
     * @param int $packlistId The packlist ID
     * @param int $caseId The case ID
     * @return bool True if the case is already in the packlist
     */
    public function isCaseInPacklist(int $packlistId, int $caseId): bool
    {
        $count = (int)$this->database->selectValue(
            'SELECT COUNT(*) FROM packlist_items WHERE packlist_id = ? AND item_type = ? AND item_id = ?',
            [$packlistId, self::ITEM_TYPE_CASE, $caseId]
        );

        return $count > 0;
    }

    /**
     * Get packlist status label
     * 
     * @param string $status The status
     * @return string The label
     */
    public static function getStatusLabel(string $status): string
    {
        return self::$statusLabels[$status] ?? $status;
    }

    /**
     * Get packlist status color
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
            self::STATUS_DRAFT,
            self::STATUS_ACTIVE,
            self::STATUS_COMPLETED,
            self::STATUS_ARCHIVED,
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
     * Get item type label
     * 
     * @param string $type The item type
     * @return string The label
     */
    public static function getItemTypeLabel(string $type): string
    {
        return self::$itemTypeLabels[$type] ?? $type;
    }

    /**
     * Get item type options for select dropdown
     * 
     * @return array The item type options
     */
    public static function getItemTypeOptions(): array
    {
        return [
            self::ITEM_TYPE_DEVICE => self::$itemTypeLabels[self::ITEM_TYPE_DEVICE],
            self::ITEM_TYPE_CASE => self::$itemTypeLabels[self::ITEM_TYPE_CASE],
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
