<?php

/** @var array $rentals */
/** @var array $pagination */
/** @var array $filters */
/** @var array $statistics */
/** @var array $statusOptions */

use DDWB\Modules\Rentals\Models\Rental;

$title = 'Ausleihen - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Ausleihen</h1>
            <p class="text-muted mb-0">Verwaltung der Ausleihen</p>
        </div>
        <div class="d-flex gap-2">
            <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                <a href="/rentals/create" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Neue Ausleihe
                </a>
            <?php endif; ?>
            <a href="/rentals" class="btn btn-outline-secondary">
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
                            <i class="bi bi-box-arrow-up-right text-primary fs-4"></i>
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
            <div class="card h-100 border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-box-arrow-up text-primary fs-4"></i>
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
                            <h6 class="mb-1">Zurückgegeben</h6>
                            <h3 class="mb-0"><?= e($statistics['returned'] ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card h-100 border-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Überfällig</h6>
                            <h3 class="mb-0"><?= e($statistics['overdue'] ?? 0) ?></h3>
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
            <form method="GET" action="/rentals" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Suche</label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           value="<?= e($filters['search'] ?? '') ?>" 
                           placeholder="Name, ID oder Entleiher">
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
                    <label for="borrower" class="form-label">Entleiher</label>
                    <input type="text" 
                           class="form-control" 
                           id="borrower" 
                           name="borrower" 
                           value="<?= e($filters['borrower'] ?? '') ?>" 
                           placeholder="Name oder E-Mail">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Suchen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Rentals Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Ausleihen Liste</h5>
            <span class="badge bg-secondary"><?= e(count($rentals)) ?> Ausleihen</span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($rentals)): ?>
                <div class="p-5 text-center">
                    <i class="bi bi-box-arrow-up-right fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">Keine Ausleihen gefunden</p>
                    <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                        <a href="/rentals/create" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Erste Ausleihe erstellen
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Gerät/Case</th>
                                <th>Entleiher</th>
                                <th>Ausleihdatum</th>
                                <th>Rückgabe bis</th>
                                <th>Status</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rentals as $rental): ?>
                                <tr class="<?= $rental['status'] === Rental::STATUS_OVERDUE ? 'table-danger' : '' ?>">
                                    <td>
                                        <strong>#<?= e($rental['id']) ?></strong>
                                    </td>
                                    <td>
                                        <?php if (!empty($rental['device_name'])): ?>
                                            <a href="/devices/<?= e($rental['device_id']) ?>" class="text-decoration-none">
                                                <?= e($rental['device_name']) ?>
                                            </a>
                                            <div class="text-muted small"><?= e($rental['device_internal_id'] ?? '') ?></div>
                                        <?php elseif (!empty($rental['case_name'])): ?>
                                            <a href="/cases/<?= e($rental['case_id']) ?>" class="text-decoration-none">
                                                <?= e($rental['case_name']) ?>
                                            </a>
                                            <div class="text-muted small"><?= e($rental['case_internal_id'] ?? '') ?></div>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div><?= e($rental['borrower']) ?></div>
                                        <?php if (!empty($rental['borrower_email'])): ?>
                                            <div class="text-muted small"><?= e($rental['borrower_email']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div><?= format_date($rental['date_out']) ?></div>
                                        <div class="text-muted small"><?= format_time($rental['date_out']) ?></div>
                                    </td>
                                    <td>
                                        <div class="<?= strtotime($rental['expected_return']) < time() && $rental['status'] === Rental::STATUS_ACTIVE ? 'text-danger' : '' ?>">
                                            <?= format_date($rental['expected_return']) ?>
                                        </div>
                                        <div class="text-muted small"><?= format_time($rental['expected_return']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= Rental::getStatusColor($rental['status']) ?>">
                                            <?= e(Rental::getStatusLabel($rental['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="/rentals/<?= e($rental['id']) ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Anzeigen">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                                                <?php if ($rental['status'] === Rental::STATUS_ACTIVE): ?>
                                                    <a href="/rentals/<?= e($rental['id']) ?>/edit" 
                                                       class="btn btn-sm btn-outline-secondary" 
                                                       title="Bearbeiten">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="POST" 
                                                          action="/rentals/<?= e($rental['id']) ?>/return" 
                                                          onsubmit="return confirm('Sind Sie sicher, dass Sie diese Ausleihe als zurückgegeben markieren möchten?')">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-sm btn-success" title="Zurückgeben">
                                                            <i class="bi bi-check-circle"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
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
                <nav aria-label="Rentals Pagination">
                    <ul class="pagination mb-0 justify-content-center">
                        <?php if ($pagination['current_page'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="?page=<?= $pagination['current_page'] - 1 ?>&search=<?= e($filters['search'] ?? '') ?>&status=<?= e($filters['status'] ?? '') ?>&borrower=<?= e($filters['borrower'] ?? '') ?>">
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
                                   href="?page=<?= $i ?>&search=<?= e($filters['search'] ?? '') ?>&status=<?= e($filters['status'] ?? '') ?>&borrower=<?= e($filters['borrower'] ?? '') ?>">
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
                                   href="?page=<?= $pagination['current_page'] + 1 ?>&search=<?= e($filters['search'] ?? '') ?>&status=<?= e($filters['status'] ?? '') ?>&borrower=<?= e($filters['borrower'] ?? '') ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <div class="text-center text-muted mt-2">
                    Seite <?= $pagination['current_page'] ?> von <?= $pagination['total_pages'] ?>
                    (<?= $pagination['total'] ?> Ausleihen insgesamt)
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $this->stop(); ?>
