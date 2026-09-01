<div class="devices-show">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">
                <?= e($device['name']) ?>
                <span class="badge badge-<?= \DDWB\Modules\Inventory\Models\Device::getStatusColor($device['status']) ?>">
                    <?= e(\DDWB\Modules\Inventory\Models\Device::getStatusLabel($device['status'])) ?>
                </span>
            </h1>
            <p class="page-subtitle">
                <code><?= e($device['internal_id']) ?></code>
                <?php if ($device['serial_number']): ?>
                    | Seriennummer: <code><?= e($device['serial_number']) ?></code>
                <?php endif; ?>
            </p>
        </div>
        
        <div class="page-actions">
            <?php if (auth()->isAdmin()): ?>
                <a href="<?= route('devices.edit', ['id' => $device['id']]) ?>" class="btn btn-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11 4H4C3.46957 4 3 4.46957 3 5V19C3 19.5304 3.46957 20 4 20H11C11.5304 20 12 19.5304 12 19V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18 13L23 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M23 8V16C23 16.5304 22.7893 17.0391 22.4142 17.4142C22.0391 17.7893 21.5304 18 21 18H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18 13L16 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18 13L20 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Bearbeiten
                </a>

                <a href="<?= route('devices.qr', ['id' => $device['id']]) ?>" class="btn btn-info">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 3H10V10H3V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 3H21V10H14V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 14H21V21H14V14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 14H10V21H3V14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10 10H14V14H10V10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    QR-Code
                </a>

                <a href="<?= route('devices.barcode', ['id' => $device['id']]) ?>" class="btn btn-info">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                    Barcode
                </a>

                <a href="<?= route('devices.label', ['id' => $device['id']]) ?>" class="btn btn-info">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 2H6C5.46957 2 5 2.46957 5 3V5C5 5.53043 5.46957 6 6 6H14C14.5304 6 15 5.53043 15 5V3C15 2.46957 14.5304 2 14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 10H6C5.46957 10 5 10.4696 5 11V19C5 19.5304 5.46957 20 6 20H14.5C15.0304 20 15.5 19.5304 15.5 19V11C15.5 10.4696 15.0304 10 14.5 10H14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 10H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M19 10V19C19 19.5304 18.7893 20.0391 18.4142 20.4142C18.0391 20.7893 17.5304 21 17 21H12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="17" cy="14" r="2" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Label
                </a>

                <a 
                    href="<?= route('devices.delete', ['id' => $device['id']]) ?>" 
                    class="btn btn-danger"
                    onclick="return confirm('Möchten Sie dieses Gerät wirklich löschen?')"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 6V4C8 3.46957 8.46957 3 9 3H15C15.5304 3 16 3.46957 16 4V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Löschen
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="device-show-grid">
        <!-- Main Info Card -->
        <div class="card device-show-card">
            <div class="card-header">
                <h2 class="card-title">Allgemeine Informationen</h2>
            </div>
            <div class="card-body">
                <div class="device-info-grid">
                    <div class="device-info-item">
                        <div class="device-info-label">Interne ID</div>
                        <div class="device-info-value"><code><?= e($device['internal_id']) ?></code></div>
                    </div>

                    <div class="device-info-item">
                        <div class="device-info-label">Name</div>
                        <div class="device-info-value"><?= e($device['name']) ?></div>
                    </div>

                    <div class="device-info-item">
                        <div class="device-info-label">Kategorie</div>
                        <div class="device-info-value">
                            <?php if ($category): ?>
                                <a href="<?= route('categories.show', ['id' => $category['id']]) ?>" class="text-info">
                                    <?= e($category['name']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="device-info-item">
                        <div class="device-info-label">Seriennummer</div>
                        <div class="device-info-value">
                            <?php if ($device['serial_number']): ?>
                                <code><?= e($device['serial_number']) ?></code>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="device-info-item">
                        <div class="device-info-label">Status</div>
                        <div class="device-info-value">
                            <span class="badge badge-<?= \DDWB\Modules\Inventory\Models\Device::getStatusColor($device['status']) ?>">
                                <?= e(\DDWB\Modules\Inventory\Models\Device::getStatusLabel($device['status'])) ?>
                            </span>
                        </div>
                    </div>

                    <div class="device-info-item">
                        <div class="device-info-label">Standort</div>
                        <div class="device-info-value"><?= e($device['location'] ?? '-') ?></div>
                    </div>

                    <div class="device-info-item">
                        <div class="device-info-label">Kaufdatum</div>
                        <div class="device-info-value">
                            <?= $device['purchase_date'] ? format_date($device['purchase_date'], 'd.m.Y') : '-' ?>
                        </div>
                    </div>

                    <div class="device-info-item">
                        <div class="device-info-label">Kaufpreis</div>
                        <div class="device-info-value">
                            <?= $device['purchase_price'] ? format_currency($device['purchase_price']) : '-' ?>
                        </div>
                    </div>

                    <div class="device-info-item">
                        <div class="device-info-label">Garantieablauf</div>
                        <div class="device-info-value">
                            <?php if ($device['warranty_expires']): ?>
                                <span class="<?= strtotime($device['warranty_expires']) < time() ? 'text-error' : '' ?>">
                                    <?= format_date($device['warranty_expires'], 'd.m.Y') ?>
                                </span>
                                <?php if (strtotime($device['warranty_expires']) < time()): ?>
                                    <span class="badge badge-error" style="margin-left: 0.5rem;">Abgelaufen</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="device-info-item device-info-item-full">
                        <div class="device-info-label">Beschreibung</div>
                        <div class="device-info-value">
                            <?= e($device['description'] ?? '-') ?>
                        </div>
                    </div>

                    <div class="device-info-item device-info-item-full">
                        <div class="device-info-label">Notizen</div>
                        <div class="device-info-value">
                            <?= nl2br(e($device['notes'] ?? '-')) ?>
                        </div>
                    </div>

                    <div class="device-info-item">
                        <div class="device-info-label">Erstellt am</div>
                        <div class="device-info-value">
                            <?= $device['created_at'] ? format_date($device['created_at'], 'd.m.Y H:i') : '-' ?>
                        </div>
                    </div>

                    <div class="device-info-item">
                        <div class="device-info-label">Zuletzt aktualisiert</div>
                        <div class="device-info-value">
                            <?= $device['updated_at'] ? format_date($device['updated_at'], 'd.m.Y H:i') : '-' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Status Card -->
        <div class="card device-show-card">
            <div class="card-header">
                <h2 class="card-title">Aktueller Status</h2>
            </div>
            <div class="card-body">
                <div class="device-status-info">
                    <?php if ($case): ?>
                        <div class="device-status-item">
                            <div class="device-status-icon device-status-icon-info">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2L2 7V17L12 22L22 17V7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M2 7L12 12L22 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 22V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="device-status-content">
                                <span class="device-status-title">Im Case</span>
                                <a href="<?= route('cases.show', ['id' => $case['id']]) ?>" class="device-status-value">
                                    <?= e($case['name']) ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($rental): ?>
                        <div class="device-status-item">
                            <div class="device-status-icon device-status-icon-warning">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17 21V19C17 17.9391 16.7893 17.0391 16.4142 16.4142C16.0391 15.7893 15.5304 15.5304 15 15.5304H9C8.46957 15.5304 7.96086 15.7893 7.58579 16.4142C7.21071 17.0391 7 17.9391 7 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M7 11C7 10.4696 7.21071 10.0391 7.58579 9.78929C7.96086 9.53948 8.46957 9.46957 9 9.46957H15C15.5304 9.46957 16.0391 9.53948 16.4142 9.78929C16.7893 10.0391 17 10.4696 17 11V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="device-status-content">
                                <span class="device-status-title">Ausgeliehen an</span>
                                <span class="device-status-value">
                                    <?= e($rental['user_name'] ?? $rental['borrower_name'] ?? 'Unbekannt') ?>
                                </span>
                                <span class="device-status-date">
                                    seit <?= format_date($rental['date_out'], 'd.m.Y') ?>
                                </span>
                                <?php if ($rental['due_date'] && strtotime($rental['due_date']) < time()): ?>
                                    <span class="badge badge-error" style="margin-left: 0.5rem;">Überfällig</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($case) && empty($rental) && $device['status'] === \DDWB\Modules\Inventory\Models\Device::STATUS_AVAILABLE): ?>
                        <div class="device-status-item">
                            <div class="device-status-icon device-status-icon-success">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="device-status-content">
                                <span class="device-status-title">Verfügbar</span>
                                <span class="device-status-value">Das Gerät ist einsatzbereit</span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($device['status'] === \DDWB\Modules\Inventory\Models\Device::STATUS_MAINTENANCE): ?>
                        <div class="device-status-item">
                            <div class="device-status-icon device-status-icon-error">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="device-status-content">
                                <span class="device-status-title">In Wartung</span>
                                <span class="device-status-value">Das Gerät befindet sich in der Wartung</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Maintenance Card -->
        <div class="card device-show-card">
            <div class="card-header">
                <h2 class="card-title">Wartungsprotokoll</h2>
                <?php if (auth()->isAdmin() && $this->isModuleLoaded('maintenance')): ?>
                    <div class="card-actions">
                        <a href="<?= route('maintenance.create', ['device_id' => $device['id']]) ?>" class="btn btn-sm btn-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 5V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Neu
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($maintenanceRecords)): ?>
                    <p class="text-muted text-center">Keine Wartungseinträge gefunden.</p>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table table-compact">
                            <thead>
                                <tr>
                                    <th>Typ</th>
                                    <th>Datum</th>
                                    <th>Nächste Prüfung</th>
                                    <th>Status</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($maintenanceRecords as $record): ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-info">
                                                <?= e($record['maintenance_type'] ?? 'DGUV3') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= format_date($record['last_inspection_date'] ?? $record['created_at'], 'd.m.Y') ?>
                                        </td>
                                        <td>
                                            <?php if ($record['next_inspection_date']): ?>
                                                <span class="<?= strtotime($record['next_inspection_date']) < time() ? 'text-error' : '' ?>">
                                                    <?= format_date($record['next_inspection_date'], 'd.m.Y') ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $record['status'] === 'passed' ? 'success' : ($record['status'] === 'failed' ? 'error' : 'warning') ?>">
                                                <?= e(ucfirst($record['status'] ?? 'unknown')) ?>
                                            </span>
                                        </td>
                                        <td class="table-actions">
                                            <a 
                                                href="<?= route('maintenance.show', ['id' => $record['id']]) ?>" 
                                                class="table-action"
                                                title="Anzeigen"
                                            >
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M14.5 2H6C5.46957 2 5 2.46957 5 3V5C5 5.53043 5.46957 6 6 6H14C14.5304 6 15 5.53043 15 5V3C15 2.46957 14.5304 2 14.5 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M14.5 10H6C5.46957 10 5 10.4696 5 11V19C5 19.5304 5.46957 20 6 20H14.5C15.0304 20 15.5 19.5304 15.5 19V11C15.5 10.4696 15.0304 10 14.5 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M14.5 10H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M19 10V19C19 19.5304 18.7893 20.0391 18.4142 20.4142C18.0391 20.7893 17.5304 21 17 21H12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <circle cx="17" cy="14" r="2" stroke="currentColor" stroke-width="2"/>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Rental History Card -->
        <div class="card device-show-card">
            <div class="card-header">
                <h2 class="card-title">Ausleihhistorie</h2>
            </div>
            <div class="card-body">
                <?php if (empty($rentalHistory)): ?>
                    <p class="text-muted text-center">Keine Ausleihen gefunden.</p>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table table-compact">
                            <thead>
                                <tr>
                                    <th>Ausgeliehen an</th>
                                    <th>Ausleihdatum</th>
                                    <th>Rückgabedatum</th>
                                    <th>Status</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rentalHistory as $rental): ?>
                                    <tr>
                                        <td>
                                            <?= e($rental['user_name'] ?? $rental['borrower_name'] ?? 'Unbekannt') ?>
                                        </td>
                                        <td>
                                            <?= format_date($rental['date_out'], 'd.m.Y H:i') ?>
                                        </td>
                                        <td>
                                            <?php if ($rental['date_in']): ?>
                                                <?= format_date($rental['date_in'], 'd.m.Y H:i') ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $rental['status'] === 'returned' ? 'success' : ($rental['status'] === 'active' ? 'warning' : 'secondary') ?>">
                                                <?= e(ucfirst($rental['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="table-actions">
                                            <a 
                                                href="<?= route('rentals.show', ['id' => $rental['id']]) ?>" 
                                                class="table-action"
                                                title="Anzeigen"
                                            >
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M14.5 2H6C5.46957 2 5 2.46957 5 3V5C5 5.53043 5.46957 6 6 6H14C14.5304 6 15 5.53043 15 5V3C15 2.46957 14.5304 2 14.5 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M14.5 10H6C5.46957 10 5 10.4696 5 11V19C5 19.5304 5.46957 20 6 20H14.5C15.0304 20 15.5 19.5304 15.5 19V11C15.5 10.4696 15.0304 10 14.5 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M14.5 10H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M19 10V19C19 19.5304 18.7893 20.0391 18.4142 20.4142C18.0391 20.7893 17.5304 21 17 21H12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <circle cx="17" cy="14" r="2" stroke="currentColor" stroke-width="2"/>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Case History Card -->
        <div class="card device-show-card">
            <div class="card-header">
                <h2 class="card-title">Case-Historie</h2>
            </div>
            <div class="card-body">
                <?php if (empty($caseHistory)): ?>
                    <p class="text-muted text-center">Keine Case-Zuweisungen gefunden.</p>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table table-compact">
                            <thead>
                                <tr>
                                    <th>Case</th>
                                    <th>Zugewiesen am</th>
                                    <th>Entfernt am</th>
                                    <th>Zugewiesen von</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($caseHistory as $assignment): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= route('cases.show', ['id' => $assignment['case_id']]) ?>" class="text-info">
                                                <?= e($assignment['case_name']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?= format_date($assignment['assigned_at'], 'd.m.Y H:i') ?>
                                        </td>
                                        <td>
                                            <?php if ($assignment['removed_at']): ?>
                                                <?= format_date($assignment['removed_at'], 'd.m.Y H:i') ?>
                                            <?php else: ?>
                                                <span class="text-muted">Aktiv</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= e($assignment['user_name'] ?? 'System') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
