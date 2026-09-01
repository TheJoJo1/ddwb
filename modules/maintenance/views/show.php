<?php

/** @var array $maintenance */
/** @var array $deviceMaintenance */
/** @var array $statusOptions */
/** @var array $typeOptions */

use DDWB\Modules\Maintenance\Models\Maintenance;

$title = 'Wartungsprüfung: #' . e($maintenance['id']) . ' - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Wartungsprüfung #<?= e($maintenance['id']) ?></h1>
            <p class="text-muted mb-0">
                <span class="badge bg-<?= Maintenance::getStatusColor($maintenance['status']) ?>">
                    <?= e(Maintenance::getStatusLabel($maintenance['status'])) ?>
                </span>
                <span class="ms-2">
                    <?= e($maintenance['type']) ?> für <?= e($maintenance['device_name']) ?>
                </span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="/maintenance" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Zurück
            </a>
            <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                <a href="/maintenance/<?= e($maintenance['id']) ?>/edit" class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i> Bearbeiten
                </a>
                <div class="dropdown">
                    <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i> Mehr
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form method="POST" action="/maintenance/<?= e($maintenance['id']) ?>/delete" 
                                  onsubmit="return confirm('Sind Sie sicher, dass Sie diesen Wartungsdatensatz löschen möchten?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-trash"></i> Wartungsdatensatz löschen
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4">
        <!-- Maintenance Details -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Prüfungsdetails</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Prüfungs-ID:</dt>
                        <dd class="col-sm-8">#<?= e($maintenance['id']) ?></dd>

                        <dt class="col-sm-4">Typ:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-info"><?= e($maintenance['type']) ?></span>
                        </dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-<?= Maintenance::getStatusColor($maintenance['status']) ?>">
                                <?= e(Maintenance::getStatusLabel($maintenance['status'])) ?>
                            </span>
                        </dd>

                        <dt class="col-sm-4">Letzte Prüfung:</dt>
                        <dd class="col-sm-8"><?= format_date($maintenance['last_inspection_date']) ?></dd>

                        <dt class="col-sm-4">Nächste Prüfung:</dt>
                        <dd class="col-sm-8">
                            <div class="<?= strtotime($maintenance['next_inspection_date']) < time() ? 'text-danger fw-bold' : '' ?>">
                                <?= format_date($maintenance['next_inspection_date']) ?>
                            </div>
                            <div class="text-muted small">
                                <?= $this->calculateDaysUntil($maintenance['next_inspection_date']) ?>
                            </div>
                        </dd>

                        <dt class="col-sm-4">Prüfintervall:</dt>
                        <dd class="col-sm-8"><?= e($maintenance['interval_months']) ?> Monate</dd>

                        <dt class="col-sm-4">Prüfer:</dt>
                        <dd class="col-sm-8"><?= e($maintenance['inspector'] ?? '-') ?></dd>

                        <dt class="col-sm-4">Erstellt von:</dt>
                        <dd class="col-sm-8"><?= e($maintenance['created_by_name'] ?? 'Unbekannt') ?></dd>

                        <dt class="col-sm-4">Erstellt:</dt>
                        <dd class="col-sm-8"><?= format_datetime($maintenance['created_at']) ?></dd>

                        <dt class="col-sm-4">Aktualisiert:</dt>
                        <dd class="col-sm-8"><?= format_datetime($maintenance['updated_at']) ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Device Information -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Geräteinformationen</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Gerät:</dt>
                        <dd class="col-sm-8">
                            <a href="/devices/<?= e($maintenance['device_id']) ?>" class="text-decoration-none">
                                <?= e($maintenance['device_name']) ?>
                            </a>
                        </dd>

                        <dt class="col-sm-4">Interne ID:</dt>
                        <dd class="col-sm-8"><?= e($maintenance['device_internal_id']) ?></dd>

                        <dt class="col-sm-4">Geräte-Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-<?= \DDWB\Modules\Inventory\Models\Device::getStatusColor($maintenance['device_status'] ?? 'available') ?>">
                                <?= e(\DDWB\Modules\Inventory\Models\Device::getStatusLabel($maintenance['device_status'] ?? 'available')) ?>
                            </span>
                        </dd>

                        <dt class="col-sm-4">Seriennummer:</dt>
                        <dd class="col-sm-8">
                            <?php
                            $device = $this->deviceModel->find($maintenance['device_id']);
                            echo e($device['serial_number'] ?? '-');
                            ?>
                        </dd>
                    </dl>

                    <hr>

                    <h6>Schnellaktionen</h6>
                    <div class="d-grid gap-2">
                        <a href="/devices/<?= e($maintenance['device_id']) ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> Gerät anzeigen
                        </a>
                        <a href="/maintenance?device_id=<?= e($maintenance['device_id']) ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-list"></i> Alle Prüfungen für dieses Gerät
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes -->
    <?php if (!empty($maintenance['notes'])): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Notizen</h5>
            </div>
            <div class="card-body">
                <p class="mb-0"><?= nl2br(e($maintenance['notes'])) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Device Maintenance History -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Prüfungshistorie für dieses Gerät</h5>
            <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                <a href="/maintenance/create?device_id=<?= e($maintenance['device_id']) ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle"></i> Neue Prüfung
                </a>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <?php if (empty($deviceMaintenance)): ?>
                <div class="p-4 text-center">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">Keine weiteren Prüfungen für dieses Gerät gefunden</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Typ</th>
                                <th>Letzte Prüfung</th>
                                <th>Nächste Prüfung</th>
                                <th>Status</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deviceMaintenance as $record): ?>
                                <tr class="<?= $record['id'] === $maintenance['id'] ? 'table-active' : '' ?>">
                                    <td>
                                        #<?= e($record['id']) ?>
                                        <?php if ($record['id'] === $maintenance['id']): ?>
                                            <span class="badge bg-primary ms-2">Aktuell</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= e($record['type']) ?></span>
                                    </td>
                                    <td>
                                        <div><?= format_date($record['last_inspection_date']) ?></div>
                                        <div class="text-muted small"><?= format_time($record['last_inspection_date']) ?></div>
                                    </td>
                                    <td>
                                        <div class="<?= strtotime($record['next_inspection_date']) < time() ? 'text-danger fw-bold' : '' ?>">
                                            <?= format_date($record['next_inspection_date']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= Maintenance::getStatusColor($record['status']) ?>">
                                            <?= e(Maintenance::getStatusLabel($record['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/maintenance/<?= e($record['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Anzeigen">
                                            <i class="bi bi-eye"></i>
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
</div>

<?php $this->stop(); ?>
