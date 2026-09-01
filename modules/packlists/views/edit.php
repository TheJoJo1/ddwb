<?php

/** @var array $packlist */
/** @var array $errors */
/** @var array $statusOptions */

$title = 'Packliste bearbeiten: ' . e($packlist['name']) . ' - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Packliste bearbeiten</h1>
            <p class="text-muted mb-0">
                <?= e($packlist['name']) ?> 
                <span class="badge bg-secondary ms-2">#<?= e($packlist['id']) ?></span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="/packlists/<?= e($packlist['id']) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Zurück
            </a>
            <a href="/packlists" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Alle Packlisten
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Packliste bearbeiten</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="/packlists/<?= e($packlist['id']) ?>" class="needs-validation" novalidate>
                <?= csrf_field() ?>
                <?= method_field('PUT') ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label required">Name</label>
                        <input type="text" 
                               class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                               id="name" 
                               name="name" 
                               value="<?= e($packlist['name'] ?? '') ?>" 
                               placeholder="Name der Packliste" 
                               required>
                        <div class="invalid-feedback">
                            <?= $errors['name'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" 
                                id="status" 
                                name="status">
                            <?php foreach ($statusOptions as $value => $label): ?>
                                <option value="<?= e($value) ?>" 
                                    <?= ($packlist['status'] ?? '') === $value ? 'selected' : '' ?>>
                                    <?= e($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $errors['status'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Beschreibung</label>
                        <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                  id="description" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="Beschreibung der Packliste..."><?= e($packlist['description'] ?? '') ?></textarea>
                        <div class="invalid-feedback">
                            <?= $errors['description'][0] ?? '' ?>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label for="notes" class="form-label">Notizen</label>
                        <textarea class="form-control <?= isset($errors['notes']) ? 'is-invalid' : '' ?>" 
                                  id="notes" 
                                  name="notes" 
                                  rows="4" 
                                  placeholder="Zusätzliche Notizen zur Packliste..."><?= e($packlist['notes'] ?? '') ?></textarea>
                        <div class="invalid-feedback">
                            <?= $errors['notes'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-3">
                            <a href="/packlists/<?= e($packlist['id']) ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Abbrechen
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Änderungen speichern
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->stop(); ?>
