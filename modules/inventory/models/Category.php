<?php

declare(strict_types=1);

namespace DDWB\Modules\Inventory\Models;

use DDWB\Model;
use DDWB\Database;

/**
 * Category Model
 * 
 * Handles category data and operations
 */
final class Category extends Model
{
    protected string $table = 'categories';
    protected string $primaryKey = 'id';
    
    protected array $fillable = [
        'name',
        'description',
        'parent_id',
        'sort_order',
        'active',
    ];
    
    protected array $casts = [
        'active' => 'bool',
        'parent_id' => 'int',
        'sort_order' => 'int',
    ];
    
    protected array $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Create a new Category instance
     * 
     * @param Database $database The database instance
     */
    public function __construct(Database $database)
    {
        parent::__construct($database);
    }

    /**
     * Get all categories
     * 
     * @param bool $onlyActive Whether to return only active categories
     * @param bool $withChildren Whether to include children
     * @return array The categories
     */
    public function getAllCategories(bool $onlyActive = false, bool $withChildren = false): array
    {
        $query = 'SELECT * FROM categories WHERE 1=1';
        $params = [];

        if ($onlyActive) {
            $query .= ' AND active = 1';
        }

        $query .= ' AND deleted_at IS NULL ORDER BY sort_order ASC, name ASC';

        $categories = $this->database->select($query, $params);

        if ($withChildren) {
            $categories = $this->addChildrenToCategories($categories);
        }

        return $categories;
    }

    /**
     * Get categories as a nested tree
     * 
     * @param bool $onlyActive Whether to return only active categories
     * @return array The nested categories
     */
    public function getCategoriesTree(bool $onlyActive = false): array
    {
        $query = 'SELECT * FROM categories WHERE 1=1';
        $params = [];

        if ($onlyActive) {
            $query .= ' AND active = 1';
        }

        $query .= ' AND deleted_at IS NULL ORDER BY parent_id ASC, sort_order ASC, name ASC';

        $categories = $this->database->select($query, $params);

        return $this->buildTree($categories);
    }

    /**
     * Build a tree from flat categories
     * 
     * @param array $categories The flat categories
     * @param int|null $parentId The parent ID
     * @return array The tree
     */
    private function buildTree(array $categories, ?int $parentId = null): array
    {
        $tree = [];

        foreach ($categories as $category) {
            if ($category['parent_id'] === $parentId) {
                $children = $this->buildTree($categories, (int)$category['id']);
                
                if (!empty($children)) {
                    $category['children'] = $children;
                }

                $tree[] = $category;
            }
        }

        return $tree;
    }

    /**
     * Add children to categories
     * 
     * @param array $categories The categories
     * @return array The categories with children
     */
    private function addChildrenToCategories(array $categories): array
    {
        $indexed = [];
        
        foreach ($categories as $category) {
            $indexed[$category['id']] = $category;
        }

        foreach ($categories as &$category) {
            if (isset($indexed[$category['parent_id']])) {
                $parent = &$indexed[$category['parent_id']];
                
                if (!isset($parent['children'])) {
                    $parent['children'] = [];
                }

                $parent['children'][] = $category;
            }
        }

        return array_filter($categories, fn($c) => $c['parent_id'] === null);
    }

    /**
     * Get root categories (categories without parent)
     * 
     * @param bool $onlyActive Whether to return only active categories
     * @return array The root categories
     */
    public function getRootCategories(bool $onlyActive = false): array
    {
        $query = 'SELECT * FROM categories WHERE parent_id IS NULL';
        $params = [];

        if ($onlyActive) {
            $query .= ' AND active = 1';
        }

        $query .= ' AND deleted_at IS NULL ORDER BY sort_order ASC, name ASC';

        return $this->database->select($query, $params);
    }

    /**
     * Get child categories
     * 
     * @param int $parentId The parent category ID
     * @param bool $onlyActive Whether to return only active categories
     * @return array The child categories
     */
    public function getChildCategories(int $parentId, bool $onlyActive = false): array
    {
        $query = 'SELECT * FROM categories WHERE parent_id = ?';
        $params = [$parentId];

        if ($onlyActive) {
            $query .= ' AND active = 1';
        }

        $query .= ' AND deleted_at IS NULL ORDER BY sort_order ASC, name ASC';

        return $this->database->select($query, $params);
    }

    /**
     * Get categories as options for select dropdown
     * 
     * @param bool $onlyActive Whether to return only active categories
     * @param string $prefix The prefix for nested categories
     * @param int|null $parentId The parent category ID
     * @return array The category options
     */
    public function getCategoryOptions(bool $onlyActive = false, string $prefix = '', ?int $parentId = null): array
    {
        $query = 'SELECT * FROM categories WHERE 1=1';
        $params = [];

        if ($parentId !== null) {
            $query .= ' AND parent_id = ?';
            $params[] = $parentId;
        } else {
            $query .= ' AND parent_id IS NULL';
        }

        if ($onlyActive) {
            $query .= ' AND active = 1';
        }

        $query .= ' AND deleted_at IS NULL ORDER BY sort_order ASC, name ASC';

        $categories = $this->database->select($query, $params);
        $options = [];

        foreach ($categories as $category) {
            $options[$category['id']] = $prefix . $category['name'];
            
            // Recursively add children
            $children = $this->getCategoryOptions($onlyActive, $prefix . '— ', (int)$category['id']);
            $options = array_merge($options, $children);
        }

        return $options;
    }

    /**
     * Get category by ID
     * 
     * @param int $categoryId The category ID
     * @return array|null The category or null if not found
     */
    public function getCategoryById(int $categoryId): ?array
    {
        return $this->find($categoryId);
    }

    /**
     * Get category by name
     * 
     * @param string $name The category name
     * @return array|null The category or null if not found
     */
    public function getCategoryByName(string $name): ?array
    {
        return $this->findBy('name', $name);
    }

    /**
     * Create a new category
     * 
     * @param array $data The category data
     * @return int|string The new category ID
     */
    public function createCategory(array $data): int|string
    {
        // Set default values
        $data['active'] = $data['active'] ?? true;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        // Get next sort order if not provided
        if (!isset($data['sort_order']) || $data['sort_order'] === 0) {
            $maxSortOrder = $this->database->selectValue(
                'SELECT MAX(sort_order) FROM categories WHERE parent_id ' . 
                ($data['parent_id'] ? '= ?' : 'IS NULL'),
                $data['parent_id'] ? [$data['parent_id']] : []
            );
            $data['sort_order'] = (int)$maxSortOrder + 1;
        }

        return $this->create($data);
    }

    /**
     * Update a category
     * 
     * @param int $categoryId The category ID
     * @param array $data The category data
     * @return int The number of affected rows
     */
    public function updateCategory(int $categoryId, array $data): int
    {
        return $this->update($categoryId, $data);
    }

    /**
     * Delete a category
     * 
     * @param int $categoryId The category ID
     * @return int The number of affected rows
     */
    public function deleteCategory(int $categoryId): int
    {
        // Soft delete
        return $this->softDelete($categoryId);
    }

    /**
     * Check if a category has children
     * 
     * @param int $categoryId The category ID
     * @return bool True if the category has children
     */
    public function hasChildren(int $categoryId): bool
    {
        $count = (int)$this->database->selectValue(
            'SELECT COUNT(*) FROM categories WHERE parent_id = ? AND deleted_at IS NULL',
            [$categoryId]
        );

        return $count > 0;
    }

    /**
     * Check if a category has devices
     * 
     * @param int $categoryId The category ID
     * @return bool True if the category has devices
     */
    public function hasDevices(int $categoryId): bool
    {
        $count = (int)$this->database->selectValue(
            'SELECT COUNT(*) FROM devices WHERE category_id = ? AND deleted_at IS NULL',
            [$categoryId]
        );

        return $count > 0;
    }

    /**
     * Get the number of devices in a category
     * 
     * @param int $categoryId The category ID
     * @return int The number of devices
     */
    public function getDeviceCount(int $categoryId): int
    {
        return (int)$this->database->selectValue(
            'SELECT COUNT(*) FROM devices WHERE category_id = ? AND deleted_at IS NULL',
            [$categoryId]
        );
    }

    /**
     * Reorder categories
     * 
     * @param array $categoryIds The category IDs in new order
     * @return int The number of affected rows
     */
    public function reorderCategories(array $categoryIds): int
    {
        $affected = 0;

        foreach ($categoryIds as $index => $categoryId) {
            $affected += $this->database->update(
                $this->table,
                ['sort_order' => $index + 1],
                [$this->primaryKey => $categoryId]
            );
        }

        return $affected;
    }

    /**
     * Get categories with device counts
     * 
     * @param bool $onlyActive Whether to return only active categories
     * @return array The categories with device counts
     */
    public function getCategoriesWithDeviceCounts(bool $onlyActive = false): array
    {
        $query = 'SELECT c.*, COUNT(d.id) as device_count ' .
                 'FROM categories c ' .
                 'LEFT JOIN devices d ON c.id = d.category_id AND d.deleted_at IS NULL ' .
                 'WHERE c.deleted_at IS NULL';

        if ($onlyActive) {
            $query .= ' AND c.active = 1';
        }

        $query .= ' GROUP BY c.id ORDER BY c.sort_order ASC, c.name ASC';

        return $this->database->select($query);
    }

    /**
     * Search categories
     * 
     * @param string $query The search query
     * @return array The matching categories
     */
    public function search(string $query): array
    {
        return $this->database->select(
            'SELECT * FROM categories WHERE name LIKE ? AND deleted_at IS NULL ORDER BY name ASC',
            ["%{$query}%"]
        );
    }

    /**
     * Get next internal ID for a category
     * 
     * @return string The next internal ID
     */
    public function getNextInternalId(): string
    {
        return $this->getNextInternalId('CAT');
    }
}
