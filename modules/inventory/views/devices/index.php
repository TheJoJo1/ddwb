<div class="devices-index">
    <div class="page-header">
        <h1 class="page-title">Geräte</h1>
        <p class="page-subtitle">Verwaltung aller Geräte im Inventar</p>
        
        <div class="page-actions">
            <?php if (auth()->isAdmin()): ?>
                <a href="<?= route('devices.create') ?>" class="btn btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 5V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M5 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Gerät erstellen
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
                <span class="stat-card-value"><?= e($stats['total'] ?? 0) ?></span>
                <span class="stat-card-label">Gesamt</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon-success">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= e($stats['available'] ?? 0) ?></span>
                <span class="stat-card-label">Verfügbar</span>
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
                <span class="stat-card-value"><?= e($stats['in_case'] ?? 0) ?></span>
                <span class="stat-card-label">Im Case</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon-warning">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7V17L12 22L22 17V7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 7L12 12L22 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17 21V19C17 17.9391 16.7893 17.0391 16.4142 16.4142C16.0391 15.7893 15.5304 15.5304 15 15.5304H9C8.46957 15.5304 7.96086 15.7893 7.58579 16.4142C7.21071 17.0391 7 17.9391 7 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 11C7 10.4696 7.21071 10.0391 7.58579 9.78929C7.96086 9.53948 8.46957 9.46957 9 9.46957H15C15.5304 9.46957 16.0391 9.53948 16.4142 9.78929C16.7893 10.0391 17 10.4696 17 11V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= e($stats['lent_out'] ?? 0) ?></span>
                <span class="stat-card-label">Ausgeliehen</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon-error">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 2V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 21V22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 12H3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M19.07 4.93L20.12 6.38" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16.88 20.12L18.33 18.67" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M4.93 19.07L6.38 17.62" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3.52 6.38L4.57 4.93" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= e($stats['maintenance'] ?? 0) ?></span>
                <span class="stat-card-label">In Wartung</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions" style="margin-bottom: <?= config('spacing.lg', '1.5rem') ?>;">
        <div class="quick-actions-grid">
            <a href="<?= route('devices', ['status' => \DDWB\Modules\Inventory\Models\Device::STATUS_AVAILABLE]) ?>" class="quick-action-card">
                <div class="quick-action-icon quick-action-icon-success">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
                <div class="quick-action-content">
                    <span class="quick-action-title">Verfügbare Geräte</span>
                    <span class="quick-action-count"><?= e($stats['available'] ?? 0) ?></span>
                </div>
            </a>

            <a href="<?= route('devices', ['status' => \DDWB\Modules\Inventory\Models\Device::STATUS_IN_CASE]) ?>" class="quick-action-card">
                <div class="quick-action-icon quick-action-icon-info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7V17L12 22L22 17V7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 7L12 12L22 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 22V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="quick-action-content">
                    <span class="quick-action-title">Geräte im Case</span>
                    <span class="quick-action-count"><?= e($stats['in_case'] ?? 0) ?></span>
                </div>
            </a>

            <a href="<?= route('devices', ['status' => \DDWB\Modules\Inventory\Models\Device::STATUS_LENT_OUT]) ?>" class="quick-action-card">
                <div class="quick-action-icon quick-action-icon-warning">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17 21V19C17 17.9391 16.7893 17.0391 16.4142 16.4142C16.0391 15.7893 15.5304 15.5304 15 15.5304H9C8.46957 15.5304 7.96086 15.7893 7.58579 16.4142C7.21071 17.0391 7 17.9391 7 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7 11C7 10.4696 7.21071 10.0391 7.58579 9.78929C7.96086 9.53948 8.46957 9.46957 9 9.46957H15C15.5304 9.46957 16.0391 9.53948 16.4142 9.78929C16.7893 10.0391 17 10.4696 17 11V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="quick-action-content">
                    <span class="quick-action-title">Ausgeliehene Geräte</span>
                    <span class="quick-action-count"><?= e($stats['lent_out'] ?? 0) ?></span>
                </div>
            </a>

            <a href="<?= route('devices', ['status' => \DDWB\Modules\Inventory\Models\Device::STATUS_MAINTENANCE]) ?>" class="quick-action-card">
                <div class="quick-action-icon quick-action-icon-error">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="quick-action-content">
                    <span class="quick-action-title">Geräte in Wartung</span>
                    <span class="quick-action-count"><?= e($stats['maintenance'] ?? 0) ?></span>
                </div>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-bar">
        <form method="GET" action="<?= route('devices') ?>" class="filters-form">
            <div class="filters-group">
                <div class="form-group filters-group-item">
                    <label for="search" class="form-label">Suche</label>
                    <div class="form-input-wrapper">
                        <svg class="form-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                            <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input 
                            type="text" 
                            id="search" 
                            name="search" 
                            class="form-input"
                            placeholder="Interne ID, Name, Seriennummer..."
                            value="<?= e($filters['search'] ?? '') ?>"
                        >
                    </div>
                </div>

                <div class="form-group filters-group-item">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Alle Status</option>
                        <?php foreach ($statusOptions as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= ($filters['status'] ?? '') === $value ? 'selected' : '' ?>>
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group filters-group-item">
                    <label for="category_id" class="form-label">Kategorie</label>
                    <select id="category_id" name="category_id" class="form-select">
                        <option value="">Alle Kategorien</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= e($category['id']) ?>" <?= ($filters['category_id'] ?? null) === (int)$category['id'] ? 'selected' : '' ?>>
                                <?= e($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group filters-group-item">
                    <label for="per_page" class="form-label">Pro Seite</label>
                    <select id="per_page" name="per_page" class="form-select">
                        <option value="10" <?= ($pagination['per_page'] ?? 25) === 10 ? 'selected' : '' ?>>10</option>
                        <option value="25" <?= ($pagination['per_page'] ?? 25) === 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= ($pagination['per_page'] ?? 25) === 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= ($pagination['per_page'] ?? 25) === 100 ? 'selected' : '' ?>>100</option>
                    </select>
                </div>

                <div class="filters-group-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                            <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Filtern
                    </button>
                    
                    <a href="<?= route('devices') ?>" class="btn btn-ghost">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Zurücksetzen
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Devices Table -->
    <div class="table-container">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Interne ID</th>
                    <th>Name</th>
                    <th>Kategorie</th>
                    <th>Seriennummer</th>
                    <th>Status</th>
                    <th>Standort</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($devices)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            Keine Geräte gefunden.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($devices as $device): ?>
                        <tr>
                            <td>
                                <a href="<?= route('devices.show', ['id' => $device['id']]) ?>" class="text-primary">
                                    <code><?= e($device['internal_id']) ?></code>
                                </a>
                            </td>
                            <td>
                                <a href="<?= route('devices.show', ['id' => $device['id']]) ?>" class="text-primary">
                                    <?= e($device['name']) ?>
                                </a>
                            </td>
                            <td>
                                <?php if ($device['category_name']): ?>
                                    <a href="<?= route('categories.show', ['id' => $device['category_id']]) ?>" class="text-info">
                                        <?= e($device['category_name']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($device['serial_number']): ?>
                                    <code><?= e($device['serial_number']) ?></code>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= \DDWB\Modules\Inventory\Models\Device::getStatusColor($device['status']) ?>">
                                    <?= e(\DDWB\Modules\Inventory\Models\Device::getStatusLabel($device['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <?= e($device['location'] ?? '-') ?>
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

                                    <a 
                                        href="<?= route('devices.qr', ['id' => $device['id']]) ?>" 
                                        class="table-action"
                                        title="QR-Code"
                                    >
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 3H10V10H3V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M14 3H21V10H14V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M14 14H21V21H14V14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M3 14H10V21H3V14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M10 10H14V14H10V10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>

                                    <a 
                                        href="<?= route('devices.barcode', ['id' => $device['id']]) ?>" 
                                        class="table-action"
                                        title="Barcode"
                                    >
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2 6H4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M6 4V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M6 18V20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M4 18H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M18 4V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M20 4H18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M18 20V18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M20 20H18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M10 4H14V6H10V4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M10 18H14V20H10V18Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M8 10H16V12H8V10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M8 14H16V16H8V14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>

                                    <a 
                                        href="<?= route('devices.delete', ['id' => $device['id']]) ?>" 
                                        class="table-action table-action-danger"
                                        title="Löschen"
                                        onclick="return confirm('Möchten Sie dieses Gerät wirklich löschen?')"
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

    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
        <div class="pagination">
            <?php if ($pagination['current_page'] > 1): ?>
                <a 
                    href="<?= route('devices', array_merge(['page' => $pagination['current_page'] - 1], $filters)) ?>" 
                    class="pagination-item"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Zurück
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <?php if ($i === $pagination['current_page']): ?>
                    <span class="pagination-item active"><?= $i ?></span>
                <?php else: ?>
                    <a 
                        href="<?= route('devices', array_merge(['page' => $i], $filters)) ?>" 
                        class="pagination-item"
                    ><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                <a 
                    href="<?= route('devices', array_merge(['page' => $pagination['current_page'] + 1], $filters)) ?>" 
                    class="pagination-item"
                >
                    Nächste
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
