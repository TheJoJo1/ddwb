<div class="categories-index">
    <div class="page-header">
        <h1 class="page-title">Kategorien</h1>
        
        <div class="page-actions">
            <?php if (auth()->isAdmin()): ?>
                <a href="<?= route('categories.create') ?>" class="btn btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 5V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M5 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Kategorie erstellen
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Statistics -->
    <div class="stats-grid" style="margin-bottom: <?= config('spacing.lg', '1.5rem') ?>;">
        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon-primary">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20.5 12H3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 3.5V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 20.5V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18.1 6.3L20.5 3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M5.5 17.7L3.1 20.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18.1 17.7L20.5 20.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M5.5 6.3L3.1 3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= count($categories) ?></span>
                <span class="stat-card-label">Gesamt</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon-success">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 16V8C21 7.46957 20.7893 7.03914 20.4142 6.78929C20.0391 6.53948 19.5304 6.46957 19 6.46957H5C4.46957 6.46957 3.96086 6.53948 3.58579 6.78929C3.21071 7.03914 3 7.46957 3 8V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3 16L7 20H17L21 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 20V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17 20V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">
                    <?= array_sum(array_column($categories, 'device_count')) ?>
                </span>
                <span class="stat-card-label">Geräte</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon-info">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7V17L12 22L22 17V7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 7L12 12L22 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 22V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value">
                    <?= count(array_filter($categories, fn($c) => $c['parent_id'] === null)) ?>
                </span>
                <span class="stat-card-label">Hauptkategorien</span>
            </div>
        </div>
    </div>

    <!-- Category Tree -->
    <div class="categories-tree">
        <h2 class="categories-tree-title">Kategorienstruktur</h2>
        
        <?php if (empty($categoryTree)): ?>
            <p class="text-muted">Keine Kategorien gefunden.</p>
        <?php else: ?>
            <div class="category-tree">
                <?= $this->renderCategoryTree($categoryTree) ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Category List -->
    <div class="categories-list">
        <h2 class="categories-list-title">Alle Kategorien</h2>
        
        <div class="table-container">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Beschreibung</th>
                        <th>Übergeordnet</th>
                        <th>Geräte</th>
                        <th>Status</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                Keine Kategorien gefunden.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= e($category['id']) ?></td>
                                <td>
                                    <a href="<?= route('categories.show', ['id' => $category['id']]) ?>" class="text-primary">
                                        <?= e($category['name']) ?>
                                    </a>
                                </td>
                                <td><?= e(str_limit($category['description'] ?? '', 50)) ?></td>
                                <td>
                                    <?php if ($category['parent_id']): ?>
                                        <?php $parent = $this->getParentCategory($category['parent_id'], $categories); ?>
                                        <?= $parent ? e($parent['name']) : '-' ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $category['device_count'] > 0 ? 'primary' : 'secondary' ?>">
                                        <?= e($category['device_count']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $category['active'] ? 'success' : 'secondary' ?>">
                                        <?= $category['active'] ? 'Aktiv' : 'Inaktiv' ?>
                                    </span>
                                </td>
                                <td class="table-actions">
                                    <a 
                                        href="<?= route('categories.show', ['id' => $category['id']]) ?>" 
                                        class="table-action"
                                        title="Anzeigen"
                                    >
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14.5 2H6C5.46957 2 5 2.46957 5 3V5C5 5.53043 5.46957 6 6 6H14C14.5304 6 15 5.53043 15 5V3C15 2.46957 14.5304 2 14.5 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M14.5 10H6C5.46957 10 5 10.4696 5 11V19C5 19.5304 5.46957 20 6 20H14.5C15.0304 20 15.5 19.5304 15.5 19V11C15.5 10.4696 15.0304 10 14.5 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M14.5 10H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M19 10V19C19 19.5304 18.7893 20.0391 18.4142 20.4142C18.0391 20.7893 17.5304 21 17 21H12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <circle cx="17" cy="14" r="2" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                    </a>
                                    
                                    <?php if (auth()->isAdmin()): ?>
                                        <a 
                                            href="<?= route('categories.edit', ['id' => $category['id']]) ?>" 
                                            class="table-action"
                                            title="Bearbeiten"
                                        >
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M11 4H4C3.46957 4 3 4.46957 3 5V19C3 19.5304 3.46957 20 4 20H11C11.5304 20 12 19.5304 12 19V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M18 13L23 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M23 8V16C23 16.5304 22.7893 17.0391 22.4142 17.4142C22.0391 17.7893 21.5304 18 21 18H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M18 13L16 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M18 13L20 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                        
                                        <a 
                                            href="<?= route('categories.delete', ['id' => $category['id']]) ?>" 
                                            class="table-action table-action-danger"
                                            title="Löschen"
                                            onclick="return confirm('Möchten Sie diese Kategorie wirklich löschen?')"
                                        >
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M8 6V4C8 3.46957 8.46957 3 9 3H15C15.5304 3 16 3.46957 16 4V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php

/**
 * Helper methods for rendering category tree
 */
class CategoryTreeHelper
{
    public function renderCategoryTree(array $categories, int $level = 0): string
    {
        $html = '';
        
        foreach ($categories as $category) {
            $indent = str_repeat('  ', $level);
            $hasChildren = isset($category['children']) && !empty($category['children']);
            
            $html .= '<div class="category-tree-item" data-level="' . $level . '">';
            $html .= '<div class="category-tree-content">';
            $html .= '<span class="category-tree-indent">' . $indent . '</span>';
            
            if ($hasChildren) {
                $html .= '<span class="category-tree-toggle" onclick="toggleCategory(this)">';
                $html .= '<svg class="category-tree-toggle-icon" width="16" height="16" viewBox="0 0 24 24" fill="none">';
                $html .= '<path d="M9 6L12 9L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
                $html .= '</svg>';
                $html .= '</span>';
            } else {
                $html .= '<span class="category-tree-spacer"></span>';
            }
            
            $html .= '<a href="' . route('categories.show', ['id' => $category['id']]) . '" class="category-tree-link">';
            $html .= '<span class="category-tree-name">' . e($category['name']) . '</span>';
            $html .= '<span class="category-tree-count">(' . ($category['device_count'] ?? 0) . ')</span>';
            $html .= '</a>';
            $html .= '</div>';
            
            if ($hasChildren) {
                $html .= '<div class="category-tree-children" style="display: none;">';
                $html .= $this->renderCategoryTree($category['children'], $level + 1);
                $html .= '</div>';
            }
            
            $html .= '</div>';
        }
        
        return $html;
    }
    
    public function getParentCategory(int $parentId, array $categories): ?array
    {
        foreach ($categories as $category) {
            if ($category['id'] === $parentId) {
                return $category;
            }
        }
        return null;
    }
}

// Create helper instance
$treeHelper = new CategoryTreeHelper();

// Add methods to controller instance
if (isset($this) && is_object($this)) {
    $this->renderCategoryTree = function(array $categories, int $level = 0) use ($treeHelper) {
        return $treeHelper->renderCategoryTree($categories, $level);
    };
    
    $this->getParentCategory = function(int $parentId, array $categories) use ($treeHelper) {
        return $treeHelper->getParentCategory($parentId, $categories);
    };
}

?>

<script>
// Toggle category tree items
function toggleCategory(element) {
    const children = element.closest('.category-tree-item').querySelector('.category-tree-children');
    const icon = element.querySelector('.category-tree-toggle-icon');
    
    if (children.style.display === 'none') {
        children.style.display = 'block';
        icon.innerHTML = '<path d="M6 18L9 15L12 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
    } else {
        children.style.display = 'none';
        icon.innerHTML = '<path d="M9 6L12 9L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
    }
}

// Expand all categories by default
function expandAllCategories() {
    document.querySelectorAll('.category-tree-children').forEach(function(element) {
        element.style.display = 'block';
    });
    
    document.querySelectorAll('.category-tree-toggle-icon').forEach(function(element) {
        element.innerHTML = '<path d="M6 18L9 15L12 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
    });
}

// Call expandAllCategories on page load
window.addEventListener('DOMContentLoaded', expandAllCategories);
</script>
