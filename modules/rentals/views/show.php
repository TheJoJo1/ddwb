<?php

/** @var array $rental */
/** @var array $devicesInCase */
/** @var array $statusOptions */

use DDWB\Modules\Rentals\Models\Rental;

$title = 'Ausleihe: #' . e($rental['id']) . ' - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Ausleihe #<?= e($rental['id']) ?></h1>
            <p class="text-muted mb-0">
                <span class="badge bg-<?= Rental::getStatusColor($rental['status']) ?>">
                    <?= e(Rental::getStatusLabel($rental['status'])) ?>
                </span>
                <span class="ms-2">
                    <?= !empty($rental['device_name']) ? e($rental['device_name']) : (e($rental['case_name'] ?? 'Unbekannt')) ?>
                </span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="/rentals" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Zurück
            </a>
            <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                <?php if ($rental['status'] === Rental::STATUS_ACTIVE): ?>
                    <a href="/rentals/<?= e($rental['id']) ?>/edit" class="btn btn-outline-primary">
                        <i class="bi bi-pencil"></i> Bearbeiten
                    </a>
                    <form method="POST" action="/rentals/<?= e($rental['id']) ?>/return" 
                          onsubmit="return confirm('Sind Sie sicher, dass Sie diese Ausleihe als zurückgegeben markieren möchten?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Als zurückgegeben markieren
                        </button>
                    </form>
                <?php endif; ?>
                <div class="dropdown">
                    <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i> Mehr
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php if ($rental['status'] === Rental::STATUS_ACTIVE): ?>
                            <li>
                                <a class="dropdown-item" href="/rentals/<?= e($rental['id']) ?>/extend">
                                    <i class="bi bi-calendar-plus"></i> Ausleihe verlängern
                                </a>
                            </li>
                        <?php endif; ?>
                        <li>
                            <form method="POST" action="/rentals/<?= e($rental['id']) ?>/delete" 
                                  onsubmit="return confirm('Sind Sie sicher, dass Sie diese Ausleihe löschen möchten?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-trash"></i> Ausleihe löschen
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4">
        <!-- Rental Details -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Ausleihdetails</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Ausleih-ID:</dt>
                        <dd class="col-sm-8">#<?= e($rental['id']) ?></dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-<?= Rental::getStatusColor($rental['status']) ?>">
                                <?= e(Rental::getStatusLabel($rental['status'])) ?>
                            </span>
                        </dd>

                        <dt class="col-sm-4">Ausleihdatum:</dt>
                        <dd class="col-sm-8"><?= format_datetime($rental['date_out']) ?></dd>

                        <dt class="col-sm-4">Erwartete Rückgabe:</dt>
                        <dd class="col-sm-8">
                            <?= format_datetime($rental['expected_return']) ?>
                            <?php if (strtotime($rental['expected_return']) < time() && $rental['status'] === Rental::STATUS_ACTIVE): ?>
                                <span class="badge bg-danger ms-2">Überfällig!</span>
                            <?php endif; ?>
                        </dd>

                        <?php if (!empty($rental['actual_return'])): ?>
                            <dt class="col-sm-4">Tatsächliche Rückgabe:</dt>
                            <dd class="col-sm-8"><?= format_datetime($rental['actual_return']) ?></dd>
                        <?php endif; ?>

                        <dt class="col-sm-4">Erstellt von:</dt>
                        <dd class="col-sm-8"><?= e($rental['created_by_name'] ?? 'Unbekannt') ?></dd>

                        <?php if (!empty($rental['returned_by_name'])): ?>
                            <dt class="col-sm-4">Zurückgegeben von:</dt>
                            <dd class="col-sm-8"><?= e($rental['returned_by_name']) ?></dd>
                        <?php endif; ?>

                        <dt class="col-sm-4">Erstellt:</dt>
                        <dd class="col-sm-8"><?= format_datetime($rental['created_at']) ?></dd>

                        <dt class="col-sm-4">Aktualisiert:</dt>
                        <dd class="col-sm-8"><?= format_datetime($rental['updated_at']) ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Borrower Information -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Entleiher Informationen</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Name:</dt>
                        <dd class="col-sm-8"><?= e($rental['borrower']) ?></dd>

                        <dt class="col-sm-4">E-Mail:</dt>
                        <dd class="col-sm-8"><?= e($rental['borrower_email'] ?? '-') ?></dd>

                        <dt class="col-sm-4">Telefon:</dt>
                        <dd class="col-sm-8"><?= e($rental['borrower_phone'] ?? '-') ?></dd>
                    </dl>

                    <?php if (!empty($rental['notes'])): ?>
                        <hr>
                        <h6>Notizen:</h6>
                        <p class="mb-0"><?= e($rental['notes']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Rented Items -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Ausgeliehene Artikel</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Typ</th>
                            <th>Name</th>
                            <th>Interne ID</th>
                            <th>Status</th>
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rental['device_id']) && !empty($rental['device_name'])): ?>
                            <tr>
                                <td><span class="badge bg-info">Gerät</span></td>
                                <td>
                                    <a href="/devices/<?= e($rental['device_id']) ?>" class="text-decoration-none">
                                        <?= e($rental['device_name']) ?>
                                    </a>
                                </td>
                                <td><?= e($rental['device_internal_id'] ?? '-') ?></td>
                                <td>
                                    <span class="badge bg-<?= \DDWB\Modules\Inventory\Models\Device::getStatusColor($rental['device_status'] ?? 'available') ?>">
                                        <?= e(\DDWB\Modules\Inventory\Models\Device::getStatusLabel($rental['device_status'] ?? 'available')) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/devices/<?= e($rental['device_id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Anzeigen
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php if (!empty($rental['case_id']) && !empty($rental['case_name'])): ?>
                            <tr>
                                <td><span class="badge bg-warning">Case</span></td>
                                <td>
                                    <a href="/cases/<?= e($rental['case_id']) ?>" class="text-decoration-none">
                                        <?= e($rental['case_name']) ?>
                                    </a>
                                </td>
                                <td><?= e($rental['case_internal_id'] ?? '-') ?></td>
                                <td>
                                    <span class="badge bg-<?= \DDWB\Modules\Cases\Models\CaseModel::getStatusColor($rental['case_status'] ?? 'available') ?>">
                                        <?= e(\DDWB\Modules\Cases\Models\CaseModel::getStatusLabel($rental['case_status'] ?? 'available')) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/cases/<?= e($rental['case_id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Anzeigen
                                    </a>
                                </td>
                            </tr>

                            <?php foreach ($devicesInCase as $deviceInCase): ?>
                                <tr class="table-active">
                                    <td></td>
                                    <td>
                                        <a href="/devices/<?= e($deviceInCase['id']) ?>" class="text-decoration-none">
                                            <?= e($deviceInCase['name']) ?>
                                        </a>
                                    </td>
                                    <td><?= e($deviceInCase['internal_id']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= \DDWB\Modules\Inventory\Models\Device::getStatusColor($deviceInCase['status']) ?>">
                                            <?= e(\DDWB\Modules\Inventory\Models\Device::getStatusLabel($deviceInCase['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/devices/<?= e($deviceInCase['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Anzeigen
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (empty($rental['device_id']) && empty($rental['case_id'])): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-exclamation-circle"></i> Keine Artikel mit dieser Ausleihe verknüpft
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $this->stop(); ?>
