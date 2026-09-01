<?php

/** @var array $case */
/** @var array $availableDevices */
/** @var array $statusOptions */

use DDWB\Modules\Cases\Models\CaseModel;

$title = 'Case: ' . e($case['name']) . ' - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Case: <?= e($case['name']) ?></h1>
            <p class="text-muted mb-0">
                <span class="badge bg-<?= CaseModel::getStatusColor($case['status']) ?>">
                    <?= e(CaseModel::getStatusLabel($case['status'])) ?>
                </span>
                <span class="ms-2">ID: <?= e($case['internal_id']) ?></span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="/cases" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Zurück
            </a>
            <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                <a href="/cases/<?= e($case['id']) ?>/edit" class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i> Bearbeiten
                </a>
                <div class="dropdown">
                    <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i> Mehr
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="/cases/<?= e($case['id']) ?>/qr">
                                <i class="bi bi-qr-code"></i> QR-Code anzeigen
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/cases/<?= e($case['id']) ?>/barcode">
                                <i class="bi bi-barcode"></i> Barcode anzeigen
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/cases/<?= e($case['id']) ?>/label">
                                <i class="bi bi-tags"></i> Label anzeigen
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="/cases/<?= e($case['id']) ?>/delete" 
                                  onsubmit="return confirm('Sind Sie sicher, dass Sie diesen Case löschen möchten?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-trash"></i> Case löschen
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4">
        <!-- Case Details -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Case Details</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Interne ID:</dt>
                        <dd class="col-sm-8"><?= e($case['internal_id']) ?></dd>

                        <dt class="col-sm-4">Name:</dt>
                        <dd class="col-sm-8"><?= e($case['name']) ?></dd>

                        <dt class="col-sm-4">Beschreibung:</dt>
                        <dd class="col-sm-8"><?= e($case['description'] ?? '-') ?></dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-<?= CaseModel::getStatusColor($case['status']) ?>">
                                <?= e(CaseModel::getStatusLabel($case['status'])) ?>
                            </span>
                        </dd>

                        <dt class="col-sm-4">Standort:</dt>
                        <dd class="col-sm-8"><?= e($case['location'] ?? '-') ?></dd>

                        <dt class="col-sm-4">Erstellt:</dt>
                        <dd class="col-sm-8"><?= format_date($case['created_at']) ?></dd>

                        <dt class="col-sm-4">Aktualisiert:</dt>
                        <dd class="col-sm-8"><?= format_date($case['updated_at']) ?></dd>

                        <dt class="col-sm-4">Notizen:</dt>
                        <dd class="col-sm-8"><?= e($case['notes'] ?? '-') ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Schnellaktionen</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                            <a href="/cases/<?= e($case['id']) ?>/qr" class="btn btn-outline-primary">
                                <i class="bi bi-qr-code"></i> QR-Code anzeigen
                            </a>
                            <a href="/cases/<?= e($case['id']) ?>/barcode" class="btn btn-outline-primary">
                                <i class="bi bi-barcode"></i> Barcode anzeigen
                            </a>
                            <a href="/cases/<?= e($case['id']) ?>/label" class="btn btn-outline-primary">
                                <i class="bi bi-tags"></i> Label anzeigen
                            </a>
                            <a href="/cases/<?= e($case['id']) ?>/edit" class="btn btn-outline-secondary">
                                <i class="bi bi-pencil"></i> Case bearbeiten
                            </a>
                        <?php endif; ?>
                        <a href="/rentals/create?case_id=<?= e($case['id']) ?>" class="btn btn-success">
                            <i class="bi bi-box-arrow-up-right"></i> Case ausleihen
                        </a>
                    </div>

                    <div class="mt-4">
                        <h6>Statistik</h6>
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="text-muted small">Geräte im Case:</div>
                                <div class="fw-bold"><?= e($case['device_count'] ?? 0) ?></div>
                            </div>
                            <div>
                                <div class="text-muted small">Status:</div>
                                <div class="fw-bold">
                                    <span class="badge bg-<?= CaseModel::getStatusColor($case['status']) ?>">
                                        <?= e(CaseModel::getStatusLabel($case['status'])) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contained Devices -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Enthaltene Geräte</h5>
            <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
                    <i class="bi bi-plus-circle"></i> Gerät hinzufügen
                </button>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <?php if (empty($case['devices'] ?? [])): ?>
                <div class="p-5 text-center">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">Keine Geräte in diesem Case</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Gerät</th>
                                <th>Interne ID</th>
                                <th>Seriennummer</th>
                                <th>Status</th>
                                <th>Hinzugefügt</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($case['devices'] ?? [] as $device): ?>
                                <tr>
                                    <td>
                                        <a href="/devices/<?= e($device['id']) ?>" class="text-decoration-none">
                                            <?= e($device['name']) ?>
                                        </a>
                                    </td>
                                    <td><?= e($device['internal_id']) ?></td>
                                    <td><?= e($device['serial_number'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge bg-<?= \DDWB\Modules\Inventory\Models\Device::getStatusColor($device['status']) ?>">
                                            <?= e(\DDWB\Modules\Inventory\Models\Device::getStatusLabel($device['status'])) ?>
                                        </span>
                                    </td>
                                    <td><?= format_date($device['assigned_at']) ?></td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="/devices/<?= e($device['id']) ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Anzeigen">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                                                <form method="POST" 
                                                      action="/cases/<?= e($case['id']) ?>/remove-device/<?= e($device['id']) ?>" 
                                                      onsubmit="return confirm('Sind Sie sicher, dass Sie dieses Gerät aus dem Case entfernen möchten?')">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Entfernen">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Device Modal -->
    <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
        <div class="modal fade" id="addDeviceModal" tabindex="-1" aria-labelledby="addDeviceModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST" action="/cases/<?= e($case['id']) ?>/add-device">
                        <?= csrf_field() ?>
                        <div class="modal-header">
                            <h5 class="modal-title" id="addDeviceModalLabel">Gerät zum Case hinzufügen</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
                        </div>
                        <div class="modal-body">
                            <?php if (empty($availableDevices)): ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Keine verfügbaren Geräte zum Hinzufügen
                                </div>
                            <?php else: ?>
                                <div class="mb-3">
                                    <label for="device_id" class="form-label">Gerät auswählen</label>
                                    <select class="form-select" id="device_id" name="device_id" required>
                                        <option value="" selected disabled>Bitte wählen Sie ein Gerät aus</option>
                                        <?php foreach ($availableDevices as $device): ?>
                                            <option value="<?= e($device['id']) ?>">
                                                <?= e($device['internal_id']) ?> - <?= e($device['name']) ?>
                                                <?php if (!empty($device['serial_number'])): ?>
                                                    (SN: <?= e($device['serial_number']) ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notizen (optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Notizen zur Zuordnung..."></textarea>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Abbrechen
                            </button>
                            <button type="submit" class="btn btn-primary" <?= empty($availableDevices) ? 'disabled' : '' ?>>
                                <i class="bi bi-check-circle"></i> Gerät hinzufügen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php $this->stop(); ?>
