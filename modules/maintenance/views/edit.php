<?php

/** @var array $maintenance */
/** @var array $errors */
/** @var array $devices */
/** @var array $statusOptions */
/** @var array $typeOptions */

$title = 'Wartungsprüfung bearbeiten: #' . e($maintenance['id']) . ' - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Wartungsprüfung bearbeiten</h1>
            <p class="text-muted mb-0">
                #<?= e($maintenance['id']) ?> - 
                <span class="badge bg-<?= \DDWB\Modules\Maintenance\Models\Maintenance::getStatusColor($maintenance['status']) ?>">
                    <?= e(\DDWB\Modules\Maintenance\Models\Maintenance::getStatusLabel($maintenance['status'])) ?>
                </span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="/maintenance/<?= e($maintenance['id']) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Zurück
            </a>
            <a href="/maintenance" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Alle Prüfungen
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Prüfung bearbeiten</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="/maintenance/<?= e($maintenance['id']) ?>" class="needs-validation" novalidate>
                <?= csrf_field() ?>
                <?= method_field('PUT') ?>

                <div class="row g-3">
                    <!-- Device Selection -->
                    <div class="col-md-6">
                        <label for="device_id" class="form-label required">Gerät</label>
                        <select class="form-select <?= isset($errors['device_id']) ? 'is-invalid' : '' ?>" 
                                id="device_id" 
                                name="device_id" 
                                required>
                            <option value="" selected disabled>Bitte wählen Sie ein Gerät aus</option>
                            <?php foreach ($devices as $device): ?>
                                <option value="<?= e($device['id']) ?>" 
                                    <?= ($maintenance['device_id'] ?? 0) === $device['id'] ? 'selected' : '' ?>>
                                    <?= e($device['internal_id']) ?> - <?= e($device['name']) ?>
                                    <?php if (!empty($device['serial_number'])): ?>
                                        (SN: <?= e($device['serial_number']) ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $errors['device_id'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="type" class="form-label">Prüfungstyp</label>
                        <select class="form-select <?= isset($errors['type']) ? 'is-invalid' : '' ?>" 
                                id="type" 
                                name="type">
                            <?php foreach ($typeOptions as $value => $label): ?>
                                <option value="<?= e($value) ?>" 
                                    <?= ($maintenance['type'] ?? '') === $value ? 'selected' : '' ?>>
                                    <?= e($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $errors['type'][0] ?? '' ?>
                        </div>
                    </div>

                    <!-- Inspection Dates -->
                    <div class="col-md-4">
                        <label for="last_inspection_date" class="form-label required">Letzte Prüfung</label>
                        <input type="date" 
                               class="form-control <?= isset($errors['last_inspection_date']) ? 'is-invalid' : '' ?>" 
                               id="last_inspection_date" 
                               name="last_inspection_date" 
                               value="<?= e($maintenance['last_inspection_date'] ?? '') ?>" 
                               required>
                        <div class="invalid-feedback">
                            <?= $errors['last_inspection_date'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="interval_months" class="form-label required">Prüfintervall (Monate)</label>
                        <input type="number" 
                               class="form-control <?= isset($errors['interval_months']) ? 'is-invalid' : '' ?>" 
                               id="interval_months" 
                               name="interval_months" 
                               value="<?= e($maintenance['interval_months'] ?? 12) ?>" 
                               min="1" 
                               required>
                        <div class="invalid-feedback">
                            <?= $errors['interval_months'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="next_inspection_date" class="form-label">Nächste Prüfung (optional)</label>
                        <input type="date" 
                               class="form-control <?= isset($errors['next_inspection_date']) ? 'is-invalid' : '' ?>" 
                               id="next_inspection_date" 
                               name="next_inspection_date" 
                               value="<?= e($maintenance['next_inspection_date'] ?? '') ?>" 
                               placeholder="Wird automatisch berechnet">
                        <div class="invalid-feedback">
                            <?= $errors['next_inspection_date'][0] ?? '' ?>
                        </div>
                        <small class="form-text text-muted">
                            Wird automatisch aus der letzten Prüfung + Intervall berechnet
                        </small>
                    </div>

                    <!-- Inspector -->
                    <div class="col-md-6">
                        <label for="inspector" class="form-label">Prüfer</label>
                        <input type="text" 
                               class="form-control <?= isset($errors['inspector']) ? 'is-invalid' : '' ?>" 
                               id="inspector" 
                               name="inspector" 
                               value="<?= e($maintenance['inspector'] ?? '') ?>" 
                               placeholder="Name des Prüfers">
                        <div class="invalid-feedback">
                            <?= $errors['inspector'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-secondary w-100" onclick="calculateNextDate()">
                            <i class="bi bi-calculator"></i> Nächste Prüfung berechnen
                        </button>
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label for="notes" class="form-label">Notizen</label>
                        <textarea class="form-control <?= isset($errors['notes']) ? 'is-invalid' : '' ?>" 
                                  id="notes" 
                                  name="notes" 
                                  rows="4" 
                                  placeholder="Notizen zur Prüfung..."><?= e($maintenance['notes'] ?? '') ?></textarea>
                        <div class="invalid-feedback">
                            <?= $errors['notes'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-3">
                            <a href="/maintenance/<?= e($maintenance['id']) ?>" class="btn btn-outline-secondary">
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

<script>
function calculateNextDate() {
    const lastDateInput = document.getElementById('last_inspection_date');
    const intervalInput = document.getElementById('interval_months');
    const nextDateInput = document.getElementById('next_inspection_date');

    const lastDate = new Date(lastDateInput.value);
    const interval = parseInt(intervalInput.value) || 12;

    if (isNaN(lastDate.getTime())) {
        alert('Bitte geben Sie ein gültiges Datum für die letzte Prüfung ein!');
        return;
    }

    const nextDate = new Date(lastDate);
    nextDate.setMonth(nextDate.getMonth() + interval);

    const formattedDate = nextDate.toISOString().split('T')[0];
    nextDateInput.value = formattedDate;
}
</script>

<?php $this->stop(); ?>
