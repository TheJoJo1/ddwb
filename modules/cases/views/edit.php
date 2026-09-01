<?php

/** @var array $case */
/** @var array $errors */
/** @var array $statusOptions */

$title = 'Case bearbeiten: ' . e($case['name']) . ' - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Case bearbeiten</h1>
            <p class="text-muted mb-0">
                <?= e($case['name']) ?> 
                <span class="badge bg-secondary ms-2">ID: <?= e($case['internal_id']) ?></span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="/cases/<?= e($case['id']) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Zurück
            </a>
            <a href="/cases" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Alle Cases
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Case bearbeiten</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="/cases/<?= e($case['id']) ?>" class="needs-validation" novalidate>
                <?= csrf_field() ?>
                <?= method_field('PUT') ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label required">Name</label>
                        <input type="text" 
                               class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                               id="name" 
                               name="name" 
                               value="<?= e($case['name'] ?? '') ?>" 
                               placeholder="Name des Cases" 
                               required>
                        <div class="invalid-feedback">
                            <?= $errors['name'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="internal_id" class="form-label">Interne ID</label>
                        <input type="text" 
                               class="form-control" 
                               id="internal_id" 
                               name="internal_id" 
                               value="<?= e($case['internal_id'] ?? '') ?>" 
                               readonly>
                        <small class="form-text text-muted">
                            Die interne ID kann nicht geändert werden
                        </small>
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Beschreibung</label>
                        <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                  id="description" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="Beschreibung des Cases..."><?= e($case['description'] ?? '') ?></textarea>
                        <div class="invalid-feedback">
                            <?= $errors['description'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" 
                                id="status" 
                                name="status">
                            <?php foreach ($statusOptions as $value => $label): ?>
                                <option value="<?= e($value) ?>" 
                                    <?= ($case['status'] ?? '') === $value ? 'selected' : '' ?>>
                                    <?= e($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $errors['status'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="location" class="form-label">Standort</label>
                        <input type="text" 
                               class="form-control <?= isset($errors['location']) ? 'is-invalid' : '' ?>" 
                               id="location" 
                               name="location" 
                               value="<?= e($case['location'] ?? '') ?>" 
                               placeholder="z.B. Lager Raum A">
                        <div class="invalid-feedback">
                            <?= $errors['location'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle"></i> Änderungen speichern
                        </button>
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label">Notizen</label>
                        <textarea class="form-control <?= isset($errors['notes']) ? 'is-invalid' : '' ?>" 
                                  id="notes" 
                                  name="notes" 
                                  rows="4" 
                                  placeholder="Zusätzliche Notizen..."><?= e($case['notes'] ?? '') ?></textarea>
                        <div class="invalid-feedback">
                            <?= $errors['notes'][0] ?? '' ?>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Erstellt:</label>
                            <div class="form-control-plaintext">
                                <?= format_date($case['created_at']) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Aktualisiert:</label>
                            <div class="form-control-plaintext">
                                <?= format_date($case['updated_at']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->stop(); ?>
