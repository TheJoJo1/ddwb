<?php

declare(strict_types=1);

namespace DDWB\Modules\Inventory\Controllers;

use DDWB\Controller;
use DDWB\Modules\Inventory\Models\Category as CategoryModel;

/**
 * Categories Controller
 * 
 * Handles category management
 */
final class CategoriesController extends Controller
{
    private CategoryModel $categoryModel;

    /**
     * Create a new CategoriesController instance
     */
    public function __construct()
    {
        parent::__construct($this->container);
        $this->categoryModel = new CategoryModel($this->getDatabase());
    }

    /**
     * Display the category list
     */
    public function index(): void
    {
        $this->ensureAuthenticated();

        // Get all categories with device counts
        $categories = $this->categoryModel->getCategoriesWithDeviceCounts(true);

        // Get category tree
        $categoryTree = $this->categoryModel->getCategoriesTree(true);

        $this->view('inventory/categories/index', [
            'categories' => $categories,
            'categoryTree' => $categoryTree,
            'title' => 'Kategorien',
        ]);
    }

    /**
     * Show the create category form
     */
    public function create(): void
    {
        $this->ensureAdmin();

        // Get parent category options
        $parentOptions = $this->categoryModel->getCategoryOptions(true);

        $this->view('inventory/categories/create', [
            'parentOptions' => $parentOptions,
            'title' => 'Kategorie erstellen',
        ]);
    }

    /**
     * Store a new category
     */
    public function store(): void
    {
        $this->ensureAdmin();

        $data = [
            'name' => trim($this->post('name', ''));
            'description' => trim($this->post('description', ''));
            'parent_id' => $this->post('parent_id', null);
            'sort_order' => (int)$this->post('sort_order', 0);
            'active' => (bool)$this->post('active', true);
        ];

        // Validate input
        $validator = $this->validator->withData($data)->withRules([
            'name' => 'required|min:2|max:100|unique:categories',
            'description' => 'max:500',
            'parent_id' => 'nullable|integer',
            'sort_order' => 'integer|min:0',
        ])->withMessages([
            'name.required' => 'Bitte geben Sie einen Kategorienamen ein.',
            'name.min' => 'Der Kategoriename muss mindestens 2 Zeichen lang sein.',
            'name.max' => 'Der Kategoriename darf maximal 100 Zeichen lang sein.',
            'name.unique' => 'Diese Kategorie existiert bereits.',
            'description.max' => 'Die Beschreibung darf maximal 500 Zeichen lang sein.',
        ]);

        if (!$validator->validate()) {
            $this->flash('errors', $validator->getErrors());
            $this->flash('old', $data);
            $this->redirectToRoute('categories.create');
        }

        // Create category
        try {
            $categoryId = $this->categoryModel->createCategory($data);

            // Log the action
            $this->audit(
                'create',
                'categories',
                $categoryId,
                'Kategorie erstellt: ' . $data['name']
            );

            $this->flash('success', 'Kategorie erfolgreich erstellt.');
            $this->redirectToRoute('categories.show', ['id' => $categoryId]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to create category: {error}', ['error' => $e->getMessage()]);
            $this->flash('error', 'Fehler beim Erstellen der Kategorie: ' . $e->getMessage());
            $this->redirectToRoute('categories.create');
        }
    }

    /**
     * Show a category
     * 
     * @param int $id The category ID
     */
    public function show(int $id): void
    {
        $this->ensureAuthenticated();

        $category = $this->categoryModel->getCategoryById($id);

        if ($category === null) {
            $this->flash('error', 'Kategorie nicht gefunden.');
            $this->redirectToRoute('categories');
        }

        // Get child categories
        $childCategories = $this->categoryModel->getChildCategories($category['id'], true);

        // Get devices in this category
        $devices = $this->categoryModel->database->select(
            'SELECT * FROM devices WHERE category_id = ? AND deleted_at IS NULL ORDER BY name ASC',
            [$category['id']]
        );

        $this->view('inventory/categories/show', [
            'category' => $category,
            'childCategories' => $childCategories,
            'devices' => $devices,
            'title' => 'Kategorie: ' . e($category['name']),
        ]);
    }

    /**
     * Show the edit category form
     * 
     * @param int $id The category ID
     */
    public function edit(int $id): void
    {
        $this->ensureAdmin();

        $category = $this->categoryModel->getCategoryById($id);

        if ($category === null) {
            $this->flash('error', 'Kategorie nicht gefunden.');
            $this->redirectToRoute('categories');
        }

        // Get parent category options (exclude self and children)
        $parentOptions = $this->categoryModel->getCategoryOptions(true);
        
        // Remove self and children from parent options
        $excludeIds = [$category['id']];
        $children = $this->categoryModel->getChildCategories($category['id']);
        foreach ($children as $child) {
            $excludeIds[] = $child['id'];
        }

        $filteredParentOptions = array_filter(
            $parentOptions,
            fn($key) => !in_array((int)$key, $excludeIds, true),
            ARRAY_FILTER_USE_KEY
        );

        $this->view('inventory/categories/edit', [
            'category' => $category,
            'parentOptions' => $filteredParentOptions,
            'title' => 'Kategorie bearbeiten: ' . e($category['name']),
        ]);
    }

    /**
     * Update a category
     * 
     * @param int $id The category ID
     */
    public function update(int $id): void
    {
        $this->ensureAdmin();

        $category = $this->categoryModel->getCategoryById($id);

        if ($category === null) {
            $this->flash('error', 'Kategorie nicht gefunden.');
            $this->redirectToRoute('categories');
        }

        $data = [
            'name' => trim($this->post('name', ''));
            'description' => trim($this->post('description', ''));
            'parent_id' => $this->post('parent_id', null);
            'sort_order' => (int)$this->post('sort_order', 0);
            'active' => (bool)$this->post('active', true);
        ];

        // Validate input
        $validator = $this->validator->withData($data)->withRules([
            'name' => 'required|min:2|max:100|unique:categories,' . $id,
            'description' => 'max:500',
            'parent_id' => 'nullable|integer',
            'sort_order' => 'integer|min:0',
        ])->withMessages([
            'name.required' => 'Bitte geben Sie einen Kategorienamen ein.',
            'name.min' => 'Der Kategoriename muss mindestens 2 Zeichen lang sein.',
            'name.max' => 'Der Kategoriename darf maximal 100 Zeichen lang sein.',
            'name.unique' => 'Diese Kategorie existiert bereits.',
            'description.max' => 'Die Beschreibung darf maximal 500 Zeichen lang sein.',
        ]);

        if (!$validator->validate()) {
            $this->flash('errors', $validator->getErrors());
            $this->flash('old', $data);
            $this->redirectToRoute('categories.edit', ['id' => $id]);
        }

        // Prevent circular references
        if ($data['parent_id'] === $id) {
            $this->flash('error', 'Eine Kategorie kann nicht ihre eigene übergeordnete Kategorie sein.');
            $this->redirectToRoute('categories.edit', ['id' => $id]);
        }

        // Check if parent would create circular reference
        if ($this->wouldCreateCircularReference($id, $data['parent_id'])) {
            $this->flash('error', 'Diese Zuordnung würde eine zirkuläre Referenz erzeugen.');
            $this->redirectToRoute('categories.edit', ['id' => $id]);
        }

        // Update category
        try {
            $this->categoryModel->updateCategory($id, $data);

            // Log the action
            $this->audit(
                'update',
                'categories',
                $id,
                'Kategorie aktualisiert: ' . $data['name']
            );

            $this->flash('success', 'Kategorie erfolgreich aktualisiert.');
            $this->redirectToRoute('categories.show', ['id' => $id]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to update category: {error}', ['error' => $e->getMessage()]);
            $this->flash('error', 'Fehler beim Aktualisieren der Kategorie: ' . $e->getMessage());
            $this->redirectToRoute('categories.edit', ['id' => $id]);
        }
    }

    /**
     * Delete a category
     * 
     * @param int $id The category ID
     */
    public function destroy(int $id): void
    {
        $this->ensureAdmin();

        $category = $this->categoryModel->getCategoryById($id);

        if ($category === null) {
            $this->flash('error', 'Kategorie nicht gefunden.');
            $this->redirectToRoute('categories');
        }

        // Check if category has devices
        if ($this->categoryModel->hasDevices($category['id'])) {
            $this->flash('error', 'Diese Kategorie enthält Geräte und kann nicht gelöscht werden. Verschieben Sie zuerst alle Geräte in eine andere Kategorie.');
            $this->redirectToRoute('categories.show', ['id' => $id]);
        }

        // Check if category has children
        if ($this->categoryModel->hasChildren($category['id'])) {
            $this->flash('error', 'Diese Kategorie enthält Unterkategorien und kann nicht gelöscht werden. Verschieben Sie zuerst alle Unterkategorien.');
            $this->redirectToRoute('categories.show', ['id' => $id]);
        }

        // Delete category
        try {
            $this->categoryModel->deleteCategory($category['id']);

            // Log the action
            $this->audit(
                'delete',
                'categories',
                $id,
                'Kategorie gelöscht: ' . $category['name']
            );

            $this->flash('success', 'Kategorie erfolgreich gelöscht.');
        } catch (\Exception $e) {
            $this->logger->error('Failed to delete category: {error}', ['error' => $e->getMessage()]);
            $this->flash('error', 'Fehler beim Löschen der Kategorie: ' . $e->getMessage());
        }

        $this->redirectToRoute('categories');
    }

    /**
     * Ensure the current user is authenticated
     */
    private function ensureAuthenticated(): void
    {
        if (!$this->isAuthenticated()) {
            $this->redirectToRoute('login');
        }
    }

    /**
     * Ensure the current user is an admin
     */
    private function ensureAdmin(): void
    {
        if (!$this->isAdmin()) {
            $this->forbidden('Keine Berechtigung für diese Aktion.');
        }
    }

    /**
     * Check if setting parent_id would create a circular reference
     * 
     * @param int $categoryId The category ID
     * @param int|null $newParentId The new parent ID
     * @return bool True if it would create a circular reference
     */
    private function wouldCreateCircularReference(int $categoryId, ?int $newParentId): bool
    {
        if ($newParentId === null) {
            return false;
        }

        // Check if the new parent is a child of the category
        $currentId = $newParentId;
        
        while ($currentId !== null) {
            if ($currentId === $categoryId) {
                return true;
            }

            $parent = $this->categoryModel->getCategoryById($currentId);
            $currentId = $parent['parent_id'] ?? null;
        }

        return false;
    }
}
