<?php

/** @var array $templates */
/** @var array $filters */
/** @var array $statistics */
/** @var array $typeOptions */

use DDWB\Modules\Labels\Models\LabelTemplate;

$title = 'Labelvorlagen - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Labelvorlagen</h1>
            <p class="text-muted mb-0">Verwaltung der Labelvorlagen für Geräte und Cases</p>
        </div>
        <div class="d-flex gap-2">
            <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                <a href="/labels/templates/create" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Neue Vorlage
                </a>
                <form method="POST" action="/labels/templates/create-standard" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-secondary" title="Standardvorlagen erstellen">
                        <i class="bi bi-stars"></i> Standardvorlagen
                    </button>
                </form>
            <?php endif; ?>
            <a href="/labels/designer" class="btn btn-outline-primary">
                <i class="bi bi-palette"></i> Label-Designer
            </a>
            <a href="/labels/templates" class="btn btn-outline-secondary">
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
                            <i class="bi bi-tags text-primary fs-4"></i>
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
            <div class="card h-100 border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-phone text-info fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Für Geräte</h6>
                            <h3 class="mb-0"><?= e($statistics['device'] ?? 0) ?></h3>
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
                            <i class="bi bi-box-seam text-warning fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Für Cases</h6>
                            <h3 class="mb-0"><?= e($statistics['case'] ?? 0) ?></h3>
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
                            <h6 class="mb-1">Standard</h6>
                            <h3 class="mb-0"><?= e($statistics['default'] ?? 0) ?></h3>
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
            <form method="GET" action="/labels/templates" class="row g-3">
                <div class="col-md-6">
                    <label for="type" class="form-label">Typ</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">Alle Typen</option>
                        <?php foreach ($typeOptions as $value => $label): ?>
                            <option value="<?= e($value) ?>" 
                                <?= ($filters['type'] ?? '') === $value ? 'selected' : '' ?>>
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filtern
                    </button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="/labels/templates" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle"></i> Zurücksetzen
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Templates Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Labelvorlagen Liste</h5>
            <span class="badge bg-secondary"><?= e(count($templates)) ?> Vorlagen</span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($templates)): ?>
                <div class="p-5 text-center">
                    <i class="bi bi-tags fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">Keine Labelvorlagen gefunden</p>
                    <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="/labels/templates/create" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Erste Vorlage erstellen
                            </a>
                            <form method="POST" action="/labels/templates/create-standard" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="bi bi-stars"></i> Standardvorlagen erstellen
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Typ</th>
                                <th>Größe</th>
                                <th>Ausrichtung</th>
                                <th>Standard</th>
                                <th>Enthält</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($templates as $template): ?>
                                <tr>
                                    <td>
                                        <a href="/labels/templates/<?= e($template['id']) ?>" class="text-decoration-none">
                                            <?= e($template['name']) ?>
                                        </a>
                                        <div class="text-muted small"><?= e($template['description'] ?? '-') ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= e(LabelTemplate::getTypeLabel($template['type'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= e($template['width']) ?> × <?= e($template['height']) ?> <?= e(LabelTemplate::getUnitLabel($template['unit'])) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?= e(LabelTemplate::getOrientationLabel($template['orientation'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($template['is_default']): ?>
                                            <span class="badge bg-success">Ja</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Nein</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <?php if ($template['include_qr']): ?>
                                                <span class="badge bg-dark">QR</span>
                                            <?php endif; ?>
                                            <?php if ($template['include_barcode']): ?>
                                                <span class="badge bg-dark">Barcode</span>
                                            <?php endif; ?>
                                            <?php if ($template['include_name']): ?>
                                                <span class="badge bg-primary">Name</span>
                                            <?php endif; ?>
                                            <?php if ($template['include_internal_id']): ?>
                                                <span class="badge bg-primary">ID</span>
                                            <?php endif; ?>
                                            <?php if ($template['include_serial_number']): ?>
                                                <span class="badge bg-primary">SN</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="/labels/templates/<?= e($template['id']) ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Anzeigen">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                                                <a href="/labels/templates/<?= e($template['id']) ?>/edit" 
                                                   class="btn btn-sm btn-outline-secondary" 
                                                   title="Bearbeiten">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" 
                                                      action="/labels/templates/<?= e($template['id']) ?>/delete" 
                                                      onsubmit="return confirm('Sind Sie sicher, dass Sie diese Labelvorlage löschen möchten?')">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Löschen">
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
</div>

<?php $this->stop(); ?>
