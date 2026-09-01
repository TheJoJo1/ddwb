<?php

/** @var array $caseData */
/** @var array $errors */

$title = 'Neuer Case - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Neuer Case</h1>
            <p class="text-muted mb-0">Erstellen Sie einen neuen Case</p>
        </div>
        <a href="/cases" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Zurück zur Liste
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Case Informationen</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="/cases" class="needs-validation" novalidate>
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="internal_id" class="form-label">Interne ID</label>
                        <input type="text" 
                               class="form-control <?= isset($errors['internal_id']) ? 'is-invalid' : '' ?>" 
                               id="internal_id" 
                               name="internal_id" 
                               value="<?= e($caseData['internal_id'] ?? '') ?>" 
                               placeholder="CASE-0001">
                        <div class="invalid-feedback">
                            <?= $errors['internal_id'][0] ?? '' ?>
                        </div>
                        <small class="form-text text-muted">
                            Wird automatisch generiert, wenn nicht angegeben
                        </small>
                    </div>

                    <div class="col-md-6">
                        <label for="name" class="form-label required">Name</label>
                        <input type="text" 
                               class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                               id="name" 
                               name="name" 
                               value="<?= e($caseData['name'] ?? '') ?>" 
                               placeholder="z.B. Kamera-Case" 
                               required>
                        <div class="invalid-feedback">
                            <?= $errors['name'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Beschreibung</label>
                        <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                  id="description" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="Beschreibung des Cases..."><?= e($caseData['description'] ?? '') ?></textarea>
                        <div class="invalid-feedback">
                            <?= $errors['description'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" 
                                id="status" 
                                name="status">
                            <?php
                            $statusOptions = \DDWB\Modules\Cases\Models\CaseModel::getStatusOptions();
                            foreach ($statusOptions as $value => $label):
                            ?>
                                <option value="<?= e($value) ?>" 
                                    <?= ($caseData['status'] ?? 'available') === $value ? 'selected' : '' ?>>
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
                               value="<?= e($caseData['location'] ?? '') ?>" 
                               placeholder="z.B. Lager Raum A">
                        <div class="invalid-feedback">
                            <?= $errors['location'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="internal_id" class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle"></i> Case erstellen
                        </button>
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label">Notizen</label>
                        <textarea class="form-control <?= isset($errors['notes']) ? 'is-invalid' : '' ?>" 
                                  id="notes" 
                                  name="notes" 
                                  rows="4" 
                                  placeholder="Zusätzliche Notizen..."><?= e($caseData['notes'] ?? '') ?></textarea>
                        <div class="invalid-feedback">
                            <?= $errors['notes'][0] ?? '' ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->stop(); ?>
