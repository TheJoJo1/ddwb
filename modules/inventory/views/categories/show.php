<div class="categories-show">
    <div class="page-header">
        <h1 class="page-title">Kategorie: <?= e($category['name']) ?></h1>
        
        <div class="page-actions">
            <a href="<?= route('categories') ?>" class="btn btn-ghost">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Zurück
            </a>
            
            <?php if (auth()->isAdmin()): ?>
                <a href="<?= route('categories.edit', ['id' => $category['id']]) ?>" class="btn btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11 4H4C3.46957 4 3 4.46957 3 5V19C3 19.5304 3.46957 20 4 20H11C11.5304 20 12 19.5304 12 19V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18 13L23 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M23 8V16C23 16.5304 22.7893 17.0391 22.4142 17.4142C22.0391 17.7893 21.5304 18 21 18H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18 13L16 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18 13L20 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Bearbeiten
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="categories-show-grid">
        <!-- Category Details Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Kategoriedetails</h2>
            </div>
            
            <div class="card-body">
                <div class="category-details">
                    <div class="category-detail">
                        <span class="category-detail-label">Name:</span>
                        <span class="category-detail-value"><?= e($category['name']) ?></span>
                    </div>
                    
                    <div class="category-detail">
                        <span class="category-detail-label">Kategorie-ID:</span>
                        <span class="category-detail-value"><?= e($category['id']) ?></span>
                    </div>
                    
                    <div class="category-detail">
                        <span class="category-detail-label">Beschreibung:</span>
                        <span class="category-detail-value">
                            <?= e($category['description'] ?? '-') ?>
                        </span>
                    </div>
                    
                    <div class="category-detail">
                        <span class="category-detail-label">Übergeordnete Kategorie:</span>
                        <span class="category-detail-value">
                            <?php if ($category['parent_id']): ?>
                                <?php $parent = $this->getParentCategory($category['parent_id']); ?>
                                <?php if ($parent): ?>
                                    <a href="<?= route('categories.show', ['id' => $parent['id']]) ?>">
                                        <?= e($parent['name']) ?>
                                    </a>
                                <?php else: ?>
                                    Nicht gefunden
                                <?php endif; ?>
                            <?php else: ?>
                                Keine (Hauptkategorie)
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <div class="category-detail">
                        <span class="category-detail-label">Sortierreihenfolge:</span>
                        <span class="category-detail-value"><?= e($category['sort_order']) ?></span>
                    </div>
                    
                    <div class="category-detail">
                        <span class="category-detail-label">Status:</span>
                        <span class="category-detail-value">
                            <span class="badge badge-<?= $category['active'] ? 'success' : 'secondary' ?>">
                                <?= $category['active'] ? 'Aktiv' : 'Inaktiv' ?>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="card-footer">
                <div class="category-timestamps">
                    <div class="category-timestamp">
                        <span class="category-timestamp-label">Erstellt:</span>
                        <span class="category-timestamp-value">
                            <?= format_date($category['created_at'], 'd.m.Y H:i') ?>
                        </span>
                    </div>
                    <div class="category-timestamp">
                        <span class="category-timestamp-label">Aktualisiert:</span>
                        <span class="category-timestamp-value">
                            <?= format_date($category['updated_at'], 'd.m.Y H:i') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Child Categories Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Unterkategorien</h2>
                <div class="card-header-actions">
                    <?php if (auth()->isAdmin()): ?>
                        <a href="<?= route('categories.create') ?>?parent_id=<?= $category['id'] ?>" class="btn btn-sm btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 5V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Unterkategorie erstellen
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card-body">
                <?php if (empty($childCategories)): ?>
                    <p class="text-muted text-center">Keine Unterkategorien gefunden.</p>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Beschreibung</th>
                                    <th>Geräte</th>
                                    <th>Status</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($childCategories as $child): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= route('categories.show', ['id' => $child['id']]) ?>" class="text-primary">
                                                <?= e($child['name']) ?>
                                            </a>
                                        </td>
                                        <td><?= e(str_limit($child['description'] ?? '', 50)) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $child['device_count'] > 0 ? 'primary' : 'secondary' ?>">
                                                <?= e($child['device_count'] ?? 0) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $child['active'] ? 'success' : 'secondary' ?>">
                                                <?= $child['active'] ? 'Aktiv' : 'Inaktiv' ?>
                                            </span>
                                        </td>
                                        <td class="table-actions">
                                            <a 
                                                href="<?= route('categories.show', ['id' => $child['id']]) ?>" 
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
                                                    href="<?= route('categories.edit', ['id' => $child['id']]) ?>" 
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
                                                    href="<?= route('categories.delete', ['id' => $child['id']]) ?>" 
                                                    class="table-action table-action-danger"
                                                    title="Löschen"
                                                    onclick="return confirm('Möchten Sie diese Unterkategorie wirklich löschen?')"
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
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Devices Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Geräte in dieser Kategorie</h2>
                <div class="card-header-actions">
                    <?php if (auth()->isAdmin()): ?>
                        <a href="<?= route('devices.create') ?>?category_id=<?= $category['id'] ?>" class="btn btn-sm btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 5V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Gerät hinzufügen
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card-body">
                <?php if (empty($devices)): ?>
                    <p class="text-muted text-center">Keine Geräte in dieser Kategorie gefunden.</p>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Interne ID</th>
                                    <th>Name</th>
                                    <th>Seriennummer</th>
                                    <th>Status</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($devices as $device): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= route('devices.show', ['id' => $device['id']]) ?>" class="text-primary">
                                                <?= e($device['internal_id']) ?>
                                            </a>
                                        </td>
                                        <td><?= e($device['name']) ?></td>
                                        <td><?= e($device['serial_number'] ?? '-') ?></td>
                                        <td>
                                            <span class="badge badge-<?= DeviceModel::getStatusColor($device['status']) ?>">
                                                <?= DeviceModel::getStatusLabel($device['status']) ?>
                                            </span>
                                        </td>
                                        <td class="table-actions">
                                            <a 
                                                href="<?= route('devices.show', ['id' => $device['id']]) ?>" 
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
                                                    href="<?= route('devices.edit', ['id' => $device['id']]) ?>" 
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
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <?php if (auth()->isAdmin()): ?>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Schnellaktionen</h2>
                </div>
                
                <div class="card-body">
                    <div class="quick-actions">
                        <a href="<?= route('categories.edit', ['id' => $category['id']]) ?>" class="quick-action">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11 4H4C3.46957 4 3 4.46957 3 5V19C3 19.5304 3.46957 20 4 20H11C11.5304 20 12 19.5304 12 19V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18 13L23 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M23 8V16C23 16.5304 22.7893 17.0391 22.4142 17.4142C22.0391 17.7893 21.5304 18 21 18H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18 13L16 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18 13L20 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Bearbeiten</span>
                        </a>
                        
                        <a 
                            href="<?= route('categories.delete', ['id' => $category['id']]) ?>" 
                            class="quick-action quick-action-danger"
                            onclick="return confirm('Möchten Sie diese Kategorie wirklich löschen?')"
                        >
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 6V4C8 3.46957 8.46957 3 9 3H15C15.5304 3 16 3.46957 16 4V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Löschen</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php

/**
 * Helper method to get parent category
 */
class CategoryShowHelper
{
    public function getParentCategory(int $parentId): ?array
    {
        $db = db();
        return $db->selectOne(
            'SELECT * FROM categories WHERE id = ? AND deleted_at IS NULL',
            [$parentId]
        );
    }
}

// Create helper instance
$helper = new CategoryShowHelper();

// Add method to controller instance
if (isset($this) && is_object($this)) {
    $this->getParentCategory = function(int $parentId) use ($helper) {
        return $helper->getParentCategory($parentId);
    };
}

?>
