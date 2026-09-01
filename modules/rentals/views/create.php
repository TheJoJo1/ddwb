<?php

/** @var array $rentalData */
/** @var array $errors */
/** @var array|null $device */
/** @var array|null $case */
/** @var array $availableDevices */
/** @var array $availableCases */
/** @var array $devicesInCase */

$title = 'Neue Ausleihe - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Neue Ausleihe</h1>
            <p class="text-muted mb-0">Erstellen Sie eine neue Ausleihe</p>
        </div>
        <a href="/rentals" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Zurück zur Liste
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Ausleihe Informationen</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="/rentals" class="needs-validation" novalidate>
                <?= csrf_field() ?>

                <div class="row g-3">
                    <!-- Device or Case Selection -->
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Gerät oder Case auswählen</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="device_id" class="form-label">Gerät (optional)</label>
                                        <select class="form-select" id="device_id" name="device_id">
                                            <option value="">Kein Gerät auswählen</option>
                                            <?php foreach ($availableDevices as $availableDevice): ?>
                                                <option value="<?= e($availableDevice['id']) ?>" 
                                                    <?= ($rentalData['device_id'] ?? ($device['id'] ?? 0)) === $availableDevice['id'] ? 'selected' : '' ?>>
                                                    <?= e($availableDevice['internal_id']) ?> - <?= e($availableDevice['name']) ?>
                                                    <?php if (!empty($availableDevice['serial_number'])): ?>
                                                        (SN: <?= e($availableDevice['serial_number']) ?>)
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="case_id" class="form-label">Case (optional)</label>
                                        <select class="form-select" id="case_id" name="case_id">
                                            <option value="">Kein Case auswählen</option>
                                            <?php foreach ($availableCases as $availableCase): ?>
                                                <option value="<?= e($availableCase['id']) ?>" 
                                                    <?= ($rentalData['case_id'] ?? ($case['id'] ?? 0)) === $availableCase['id'] ? 'selected' : '' ?>>
                                                    <?= e($availableCase['internal_id']) ?> - <?= e($availableCase['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <?php if (!empty($devicesInCase)): ?>
                                    <div class="mt-3">
                                        <h6>Geräte im ausgewählten Case:</h6>
                                        <ul class="list-group list-group-flush">
                                            <?php foreach ($devicesInCase as $deviceInCase): ?>
                                                <li class="list-group-item">
                                                    <?= e($deviceInCase['internal_id']) ?> - <?= e($deviceInCase['name']) ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <div class="alert alert-info mt-3">
                                    <i class="bi bi-info-circle"></i> 
                                    Wählen Sie entweder ein einzelnes Gerät oder einen gesamten Case aus. 
                                    Wenn Sie einen Case auswählen, werden alle Geräte im Case ausgeliehen.
                                </div>
                            </div>
                        </div>
                    </div>

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
                                               value="<?= e($rentalData['borrower'] ?? ($device['name'] ?? ($case['name'] ?? ''))) ?>" 
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
                                               value="<?= e($rentalData['borrower_email'] ?? '') ?>" 
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
                                               value="<?= e($rentalData['borrower_phone'] ?? '') ?>" 
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
                                        <label for="date_out" class="form-label required">Ausleihdatum</label>
                                        <input type="datetime-local" 
                                               class="form-control <?= isset($errors['date_out']) ? 'is-invalid' : '' ?>" 
                                               id="date_out" 
                                               name="date_out" 
                                               value="<?= e($rentalData['date_out'] ?? date('Y-m-d\TH:i')) ?>" 
                                               required>
                                        <div class="invalid-feedback">
                                            <?= $errors['date_out'][0] ?? '' ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="expected_return" class="form-label required">Erwartete Rückgabe</label>
                                        <input type="datetime-local" 
                                               class="form-control <?= isset($errors['expected_return']) ? 'is-invalid' : '' ?>" 
                                               id="expected_return" 
                                               name="expected_return" 
                                               value="<?= e($rentalData['expected_return'] ?? date('Y-m-d\TH:i', strtotime('+7 days'))) ?>" 
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
                                  placeholder="Zusätzliche Notizen zur Ausleihe..."><?= e($rentalData['notes'] ?? '') ?></textarea>
                        <div class="invalid-feedback">
                            <?= $errors['notes'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-3">
                            <a href="/rentals" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Abbrechen
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Ausleihe erstellen
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->stop(); ?>
