<?php

/** @var array $rental */
/** @var array $errors */

use DDWB\Modules\Rentals\Models\Rental;

$title = 'Ausleihe bearbeiten: #' . e($rental['id']) . ' - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Ausleihe bearbeiten</h1>
            <p class="text-muted mb-0">
                #<?= e($rental['id']) ?> - 
                <span class="badge bg-<?= Rental::getStatusColor($rental['status']) ?>">
                    <?= e(Rental::getStatusLabel($rental['status'])) ?>
                </span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="/rentals/<?= e($rental['id']) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Zurück
            </a>
            <a href="/rentals" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Alle Ausleihen
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Ausleihe bearbeiten</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="/rentals/<?= e($rental['id']) ?>" class="needs-validation" novalidate>
                <?= csrf_field() ?>
                <?= method_field('PUT') ?>

                <div class="row g-3">
                    <!-- Borrower Information -->
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Entleiher Informationen</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="borrower" class="form-label required">Name des Entleihers</label>
                                        <input type="text" 
                                               class="form-control <?= isset($errors['borrower']) ? 'is-invalid' : '' ?>" 
                                               id="borrower" 
                                               name="borrower" 
                                               value="<?= e($rental['borrower'] ?? '') ?>" 
                                               placeholder="Vollständiger Name" 
                                               required>
                                        <div class="invalid-feedback">
                                            <?= $errors['borrower'][0] ?? '' ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="borrower_email" class="form-label">E-Mail</label>
                                        <input type="email" 
                                               class="form-control <?= isset($errors['borrower_email']) ? 'is-invalid' : '' ?>" 
                                               id="borrower_email" 
                                               name="borrower_email" 
                                               value="<?= e($rental['borrower_email'] ?? '') ?>" 
                                               placeholder="E-Mail Adresse">
                                        <div class="invalid-feedback">
                                            <?= $errors['borrower_email'][0] ?? '' ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="borrower_phone" class="form-label">Telefon</label>
                                        <input type="tel" 
                                               class="form-control <?= isset($errors['borrower_phone']) ? 'is-invalid' : '' ?>" 
                                               id="borrower_phone" 
                                               name="borrower_phone" 
                                               value="<?= e($rental['borrower_phone'] ?? '') ?>" 
                                               placeholder="Telefonnummer">
                                        <div class="invalid-feedback">
                                            <?= $errors['borrower_phone'][0] ?? '' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Date Information -->
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Ausleihdaten</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="date_out" class="form-label">Ausleihdatum</label>
                                        <input type="datetime-local" 
                                               class="form-control <?= isset($errors['date_out']) ? 'is-invalid' : '' ?>" 
                                               id="date_out" 
                                               name="date_out" 
                                               value="<?= e($rental['date_out'] ?? '') ?>" 
                                               readonly>
                                        <div class="invalid-feedback">
                                            <?= $errors['date_out'][0] ?? '' ?>
                                        </div>
                                        <small class="form-text text-muted">
                                            Das Ausleihdatum kann nicht geändert werden
                                        </small>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="expected_return" class="form-label required">Erwartete Rückgabe</label>
                                        <input type="datetime-local" 
                                               class="form-control <?= isset($errors['expected_return']) ? 'is-invalid' : '' ?>" 
                                               id="expected_return" 
                                               name="expected_return" 
                                               value="<?= e($rental['expected_return'] ?? '') ?>" 
                                               required>
                                        <div class="invalid-feedback">
                                            <?= $errors['expected_return'][0] ?? '' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label for="notes" class="form-label">Notizen</label>
                        <textarea class="form-control <?= isset($errors['notes']) ? 'is-invalid' : '' ?>" 
                                  id="notes" 
                                  name="notes" 
                                  rows="4" 
                                  placeholder="Zusätzliche Notizen zur Ausleihe..."><?= e($rental['notes'] ?? '') ?></textarea>
                        <div class="invalid-feedback">
                            <?= $errors['notes'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-3">
                            <a href="/rentals/<?= e($rental['id']) ?>" class="btn btn-outline-secondary">
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
