<?php

/** @var array $maintenanceRecords */
/** @var array $pagination */
/** @var array $filters */
/** @var array $statistics */
/** @var array $statusOptions */
/** @var array $typeOptions */
/** @var array $devices */

use DDWB\Modules\Maintenance\Models\Maintenance;

$title = 'Wartung (DGUV3) - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Wartung (DGUV3)</h1>
            <p class="text-muted mb-0">Verwaltung der Wartungsprüfungen</p>
        </div>
        <div class="d-flex gap-2">
            <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                <a href="/maintenance/create" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Neue Prüfung
                </a>
                <form method="POST" action="/maintenance/update-statuses" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-secondary" title="Alle Status aktualisieren">
                        <i class="bi bi-arrow-clockwise"></i> Status aktualisieren
                    </button>
                </form>
            <?php endif; ?>
            <a href="/maintenance" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-clockwise"></i> Aktualisieren
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2 col-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-tools text-primary fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Gesamt</h6>
                            <h3 class="mb-0"><?= e($statistics['total'] ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card h-100 border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">OK</h6>
                            <h3 class="mb-0"><?= e($statistics['ok'] ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card h-100 border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-circle text-warning fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Bald fällig</h6>
                            <h3 class="mb-0"><?= e($statistics['upcoming'] ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card h-100 border-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Fällig</h6>
                            <h3 class="mb-0"><?= e($statistics['due'] ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card h-100 border-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-x-circle text-danger fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Überfällig</h6>
                            <h3 class="mb-0"><?= e($statistics['overdue'] ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <a href="/maintenance/upcoming" class="text-decoration-none">
                                <i class="bi bi-calendar-check text-info fs-4"></i>
                            </a>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1"><a href="/maintenance/upcoming" class="text-decoration-none">Anstehend</a></h6>
                            <h3 class="mb-0"><?= e(($statistics['upcoming'] ?? 0) + ($statistics['due'] ?? 0)) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Buttons -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-3">
                <a href="/maintenance/overdue" class="btn btn-outline-danger">
                    <i class="bi bi-exclamation-triangle"></i> 
                    Überfällige Prüfungen 
                    <span class="badge bg-danger ms-2"><?= e($statistics['overdue'] ?? 0) ?></span>
                </a>
                <a href="/maintenance/upcoming" class="btn btn-outline-warning">
                    <i class="bi bi-calendar-check"></i> 
                    Anstehende Prüfungen 
                    <span class="badge bg-warning ms-2"><?= e(($statistics['upcoming'] ?? 0) + ($statistics['due'] ?? 0)) ?></span>
                </a>
                <a href="/maintenance?status=ok" class="btn btn-outline-success">
                    <i class="bi bi-check-circle"></i> 
                    OK Prüfungen 
                    <span class="badge bg-success ms-2"><?= e($statistics['ok'] ?? 0) ?></span>
                </a>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter & Suche</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="/maintenance" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Suche</label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           value="<?= e($filters['search'] ?? '') ?>" 
                           placeholder="Gerät, Prüfer oder Notizen">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Alle Status</option>
                        <?php foreach ($statusOptions as $value => $label): ?>
                            <option value="<?= e($value) ?>" 
                                <?= ($filters['status'] ?? '') === $value ? 'selected' : '' ?>>
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="device_id" class="form-label">Gerät</label>
                    <select class="form-select" id="device_id" name="device_id">
                        <option value="">Alle Geräte</option>
                        <?php foreach ($devices as $device): ?>
                            <option value="<?= e($device['id']) ?>" 
                                <?= ($filters['device_id'] ?? '') === (string)$device['id'] ? 'selected' : '' ?>>
                                <?= e($device['internal_id']) ?> - <?= e($device['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Suchen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Maintenance Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Wartungsprüfungen Liste</h5>
            <span class="badge bg-secondary"><?= e(count($maintenanceRecords)) ?> Prüfungen</span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($maintenanceRecords)): ?>
                <div class="p-5 text-center">
                    <i class="bi bi-tools fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">Keine Wartungsprüfungen gefunden</p>
                    <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                        <a href="/maintenance/create" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Erste Prüfung erstellen
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Gerät</th>
                                <th>Typ</th>
                                <th>Letzte Prüfung</th>
                                <th>Nächste Prüfung</th>
                                <th>Intervall</th>
                                <th>Prüfer</th>
                                <th>Status</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($maintenanceRecords as $record): ?>
                                <tr class="<?= $record['status'] === Maintenance::STATUS_OVERDUE ? 'table-danger' : ($record['status'] === Maintenance::STATUS_DUE ? 'table-warning' : '') ?>">
                                    <td>
                                        <a href="/devices/<?= e($record['device_id']) ?>" class="text-decoration-none">
                                            <?= e($record['device_name']) ?>
                                        </a>
                                        <div class="text-muted small"><?= e($record['device_internal_id']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= e($record['type']) ?></span>
                                    </td>
                                    <td>
                                        <div><?= format_date($record['last_inspection_date']) ?></div>
                                    </td>
                                    <td>
                                        <div class="<?= strtotime($record['next_inspection_date']) < time() ? 'text-danger fw-bold' : '' ?>">
                                            <?= format_date($record['next_inspection_date']) ?>
                                        </div>
                                        <div class="text-muted small">
                                            <?= $this->calculateDaysUntil($record['next_inspection_date']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?= e($record['interval_months']) ?> Monate
                                    </td>
                                    <td>
                                        <?= e($record['inspector'] ?? '-') ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= Maintenance::getStatusColor($record['status']) ?>">
                                            <?= e(Maintenance::getStatusLabel($record['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="/maintenance/<?= e($record['id']) ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Anzeigen">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                                                <a href="/maintenance/<?= e($record['id']) ?>/edit" 
                                                   class="btn btn-sm btn-outline-secondary" 
                                                   title="Bearbeiten">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
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
        <div class="card-footer">
            <!-- Pagination -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <nav aria-label="Maintenance Pagination">
                    <ul class="pagination mb-0 justify-content-center">
                        <?php if ($pagination['current_page'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="?page=<?= $pagination['current_page'] - 1 ?>&search=<?= e($filters['search'] ?? '') ?>&status=<?= e($filters['status'] ?? '') ?>&device_id=<?= e($filters['device_id'] ?? '') ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php
                        $startPage = max(1, $pagination['current_page'] - 2);
                        $endPage = min($pagination['total_pages'], $pagination['current_page'] + 2);
                        
                        if ($startPage > 1):
                            echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                            if ($startPage > 2):
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" 
                                   href="?page=<?= $i ?>&search=<?= e($filters['search'] ?? '') ?>&status=<?= e($filters['status'] ?? '') ?>&device_id=<?= e($filters['device_id'] ?? '') ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($endPage < $pagination['total_pages']):
                            if ($endPage < $pagination['total_pages'] - 1):
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            echo '<li class="page-item"><a class="page-link" href="?page=' . $pagination['total_pages'] . '">' . $pagination['total_pages'] . '</a></li>';
                        endif;
                        ?>

                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="?page=<?= $pagination['current_page'] + 1 ?>&search=<?= e($filters['search'] ?? '') ?>&status=<?= e($filters['status'] ?? '') ?>&device_id=<?= e($filters['device_id'] ?? '') ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <div class="text-center text-muted mt-2">
                    Seite <?= $pagination['current_page'] ?> von <?= $pagination['total_pages'] ?>
                    (<?= $pagination['total'] ?> Prüfungen insgesamt)
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $this->stop(); ?>
