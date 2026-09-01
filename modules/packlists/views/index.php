<?php

/** @var array $packlists */
/** @var array $pagination */
/** @var array $filters */
/** @var array $statistics */
/** @var array $statusOptions */

use DDWB\Modules\Packlists\Models\Packlist;

$title = 'Packlisten - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Packlisten</h1>
            <p class="text-muted mb-0">Verwaltung der Packlisten</p>
        </div>
        <div class="d-flex gap-2">
            <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                <a href="/packlists/create" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Neue Packliste
                </a>
            <?php endif; ?>
            <a href="/packlists" class="btn btn-outline-secondary">
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
                            <i class="bi bi-clipboard-check text-primary fs-4"></i>
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
            <div class="card h-100 border-secondary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-file-earmark text-secondary fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Entwürfe</h6>
                            <h3 class="mb-0"><?= e($statistics['draft'] ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card h-100 border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-clipboard text-primary fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Aktiv</h6>
                            <h3 class="mb-0"><?= e($statistics['active'] ?? 0) ?></h3>
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
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Abgeschlossen</h6>
                            <h3 class="mb-0"><?= e($statistics['completed'] ?? 0) ?></h3>
                        </div>
                    </div>
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
            <form method="GET" action="/packlists" class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label">Suche</label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           value="<?= e($filters['search'] ?? '') ?>" 
                           placeholder="Name oder Beschreibung">
                </div>
                <div class="col-md-4">
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
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Suchen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Packlists Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Packlisten Liste</h5>
            <span class="badge bg-secondary"><?= e(count($packlists)) ?> Packlisten</span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($packlists)): ?>
                <div class="p-5 text-center">
                    <i class="bi bi-clipboard-check fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">Keine Packlisten gefunden</p>
                    <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                        <a href="/packlists/create" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Erste Packliste erstellen
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Beschreibung</th>
                                <th>Status</th>
                                <th>Artikel</th>
                                <th>Erstellt von</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($packlists as $packlist): ?>
                                <tr>
                                    <td>
                                        <strong>#<?= e($packlist['id']) ?></strong>
                                    </td>
                                    <td>
                                        <a href="/packlists/<?= e($packlist['id']) ?>" class="text-decoration-none">
                                            <?= e($packlist['name']) ?>
                                        </a>
                                    </td>
                                    <td><?= e($packlist['description'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge bg-<?= Packlist::getStatusColor($packlist['status']) ?>">
                                            <?= e(Packlist::getStatusLabel($packlist['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= e($packlist['item_count'] ?? 0) ?></span>
                                    </td>
                                    <td><?= e($packlist['created_by_name'] ?? '-') ?></td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="/packlists/<?= e($packlist['id']) ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Anzeigen">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="/packlists/<?= e($packlist['id']) ?>/print" 
                                               class="btn btn-sm btn-outline-dark" 
                                               title="Drucken" 
                                               target="_blank">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                            <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                                                <a href="/packlists/<?= e($packlist['id']) ?>/edit" 
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
                <nav aria-label="Packlists Pagination">
                    <ul class="pagination mb-0 justify-content-center">
                        <?php if ($pagination['current_page'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="?page=<?= $pagination['current_page'] - 1 ?>&search=<?= e($filters['search'] ?? '') ?>&status=<?= e($filters['status'] ?? '') ?>">
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
                                   href="?page=<?= $i ?>&search=<?= e($filters['search'] ?? '') ?>&status=<?= e($filters['status'] ?? '') ?>">
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
                                   href="?page=<?= $pagination['current_page'] + 1 ?>&search=<?= e($filters['search'] ?? '') ?>&status=<?= e($filters['status'] ?? '') ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <div class="text-center text-muted mt-2">
                    Seite <?= $pagination['current_page'] ?> von <?= $pagination['total_pages'] ?>
                    (<?= $pagination['total'] ?> Packlisten insgesamt)
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $this->stop(); ?>
