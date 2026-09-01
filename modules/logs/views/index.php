<?php

/** @var array $logs */
/** @var array $pagination */
/** @var array $filters */
/** @var array $statistics */
/** @var array $users */
/** @var array $actionOptions */

use DDWB\Modules\Logs\Models\Log;

$title = 'Protokoll / Audit Trail - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Protokoll / Audit Trail</h1>
            <p class="text-muted mb-0">Verlauf aller Systemaktivitäten</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/logs/recent" class="btn btn-outline-primary">
                <i class="bi bi-clock-history"></i> Letzte Aktivitäten
            </a>
            <a href="/logs/today" class="btn btn-outline-primary">
                <i class="bi bi-calendar-day"></i> Heute
            </a>
            <a href="/logs" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-clockwise"></i> Aktualisieren
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-journal-text text-primary fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Gesamt</h6>
                            <h3 class="mb-0"><?= e($statistics['total'] ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card h-100 border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-calendar-day text-success fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Heute</h6>
                            <h3 class="mb-0"><?= e($statistics['today'] ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card h-100 border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-calendar-week text-info fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Diese Woche</h6>
                            <h3 class="mb-0"><?= e($statistics['this_week'] ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card h-100 border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-calendar-month text-warning fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Dieser Monat</h6>
                            <h3 class="mb-0"><?= e($statistics['this_month'] ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Aktionen nach Typ</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($statistics['actions'])): ?>
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-inbox"></i> Keine Aktionsdaten verfügbar
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-wrap gap-2">
                            <?php
                            $actionColors = Log::getActionColors();
                            $actionLabels = Log::getActionLabels();
                            arsort($statistics['actions']);
                            $count = 0;
                            foreach ($statistics['actions'] as $action => $count):
                                if ($count > 0):
                            ?>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-<?= $actionColors[$action] ?? 'secondary' ?> me-2">
                                        <?= e($actionLabels[$action] ?? $action) ?>
                                    </span>
                                    <span class="text-muted small"><?= e($count) ?></span>
                                </div>
                            <?php
                                endif;
                                if (++$count >= 10) break; // Limit to top 10
                            endforeach;
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter & Suche</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="/logs" class="row g-3">
                <div class="col-md-3">
                    <label for="user_id" class="form-label">Benutzer</label>
                    <select class="form-select" id="user_id" name="user_id">
                        <option value="">Alle Benutzer</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= e($user['id']) ?>" 
                                <?= ($filters['user_id'] ?? '') === (string)$user['id'] ? 'selected' : '' ?>>
                                <?= e($user['name']) ?> (<?= e($user['email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="action" class="form-label">Aktion</label>
                    <select class="form-select" id="action" name="action">
                        <option value="">Alle Aktionen</option>
                        <?php foreach ($actionOptions as $value => $label): ?>
                            <option value="<?= e($value) ?>" 
                                <?= ($filters['action'] ?? '') === $value ? 'selected' : '' ?>>
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="entity_type" class="form-label">Entitätstyp</label>
                    <input type="text" 
                           class="form-control" 
                           id="entity_type" 
                           name="entity_type" 
                           value="<?= e($filters['entity_type'] ?? '') ?>" 
                           placeholder="z.B. device, rental, user">
                </div>
                <div class="col-md-3">
                    <label for="entity_id" class="form-label">Entitäts-ID</label>
                    <input type="text" 
                           class="form-control" 
                           id="entity_id" 
                           name="entity_id" 
                           value="<?= e($filters['entity_id'] ?? '') ?>" 
                           placeholder="z.B. 123">
                </div>
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Startdatum</label>
                    <input type="date" 
                           class="form-control" 
                           id="start_date" 
                           name="start_date" 
                           value="<?= e($filters['start_date'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Enddatum</label>
                    <input type="date" 
                           class="form-control" 
                           id="end_date" 
                           name="end_date" 
                           value="<?= e($filters['end_date'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Suche</label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           value="<?= e($filters['search'] ?? '') ?>" 
                           placeholder="Beschreibung, Aktion, etc.">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Suchen
                    </button>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="/logs" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle"></i> Zurücksetzen
                    </a>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="/logs/export?<?= http_build_query($filters) ?>" class="btn btn-outline-dark w-100">
                        <i class="bi bi-download"></i> Exportieren
                    </a>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
                        <i class="bi bi-trash"></i> Alte löschen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Protokolleinträge</h5>
            <span class="badge bg-secondary"><?= e(count($logs)) ?> Einträge</span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($logs)): ?>
                <div class="p-5 text-center">
                    <i class="bi bi-journal-text fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">Keine Protokolleinträge gefunden</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Zeitstempel</th>
                                <th>Benutzer</th>
                                <th>Aktion</th>
                                <th>Entität</th>
                                <th>Beschreibung</th>
                                <th>IP-Adresse</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td>
                                        <div><?= format_datetime($log['timestamp']) ?></div>
                                        <div class="text-muted small"><?= $this->calculateDaysAgo($log['timestamp']) ?></div>
                                    </td>
                                    <td>
                                        <?php if ($log['user_id'] > 0): ?>
                                            <div><?= e($log['user_name'] ?? 'Unbekannt') ?></div>
                                            <div class="text-muted small"><?= e($log['user_email'] ?? '') ?></div>
                                        <?php else: ?>
                                            <span class="text-muted">System</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= Log::getActionColor($log['action']) ?>">
                                            <?= e(Log::getActionLabel($log['action'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div><?= e($log['entity_type'] ?? '-') ?></div>
                                        <div class="text-muted small">#<?= e($log['entity_id'] ?? '-') ?></div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="<?= e($log['description'] ?? '') ?>">
                                            <?= e($log['description'] ?? '-') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-muted small"><?= e($log['ip_address'] ?? '-') ?></div>
                                    </td>
                                    <td>
                                        <a href="/logs/<?= e($log['id']) ?>" 
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
        <div class="card-footer">
            <!-- Pagination -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <nav aria-label="Logs Pagination">
                    <ul class="pagination mb-0 justify-content-center">
                        <?php if ($pagination['current_page'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="?page=<?= $pagination['current_page'] - 1 ?>&<?= http_build_query(array_filter($filters)) ?>">
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
                                   href="?page=<?= $i ?>&<?= http_build_query(array_filter($filters)) ?>">
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
                                   href="?page=<?= $pagination['current_page'] + 1 ?>&<?= http_build_query(array_filter($filters)) ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <div class="text-center text-muted mt-2">
                    Seite <?= $pagination['current_page'] ?> von <?= $pagination['total_pages'] ?>
                    (<?= $pagination['total'] ?> Protokolleinträge insgesamt)
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Clear Logs Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1" aria-labelledby="clearLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/logs/clear-old">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="clearLogsModalLabel">Alte Protokolleinträge löschen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Achtung:</strong> Diese Aktion kann nicht rückgängig gemacht werden!
                    </div>
                    <div class="mb-3">
                        <label for="days" class="form-label">Protokolleinträge älter als (Tage)</label>
                        <input type="number" 
                               class="form-control" 
                               id="days" 
                               name="days" 
                               value="365" 
                               min="1" 
                               max="3650">
                        <small class="form-text text-muted">
                            Geben Sie die Anzahl der Tage ein, die Protokolleinträge behalten werden sollen
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Abbrechen
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Alte Protokolleinträge löschen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->stop(); ?>
