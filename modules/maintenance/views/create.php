<?php

/** @var array $maintenanceData */
/** @var array $errors */
/** @var array|null $device */
/** @var array $devices */
/** @var array $typeOptions */

$title = 'Neue Wartungsprüfung - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Neue Wartungsprüfung</h1>
            <p class="text-muted mb-0">Erstellen Sie eine neue Wartungsprüfung (DGUV3)</p>
        </div>
        <a href="/maintenance" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Zurück zur Liste
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Prüfungsdaten</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="/maintenance" class="needs-validation" novalidate>
                <?= csrf_field() ?>

                <div class="row g-3">
                    <!-- Device Selection -->
                    <div class="col-md-6">
                        <label for="device_id" class="form-label required">Gerät</label>
                        <select class="form-select <?= isset($errors['device_id']) ? 'is-invalid' : '' ?>" 
                                id="device_id" 
                                name="device_id" 
                                required>
                            <option value="" selected disabled>Bitte wählen Sie ein Gerät aus</option>
                            <?php foreach ($devices as $deviceItem): ?>
                                <option value="<?= e($deviceItem['id']) ?>" 
                                    <?= ($maintenanceData['device_id'] ?? ($device['id'] ?? 0)) === $deviceItem['id'] ? 'selected' : '' ?>>
                                    <?= e($deviceItem['internal_id']) ?> - <?= e($deviceItem['name']) ?>
                                    <?php if (!empty($deviceItem['serial_number'])): ?>
                                        (SN: <?= e($deviceItem['serial_number']) ?>)
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
                                    <?= ($maintenanceData['type'] ?? Maintenance::TYPE_DGUV3) === $value ? 'selected' : '' ?>>
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
                               value="<?= e($maintenanceData['last_inspection_date'] ?? date('Y-m-d')) ?>" 
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
                               value="<?= e($maintenanceData['interval_months'] ?? 12) ?>" 
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
                               value="<?= e($maintenanceData['next_inspection_date'] ?? '') ?>" 
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
                               value="<?= e($maintenanceData['inspector'] ?? '') ?>" 
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
                                  placeholder="Notizen zur Prüfung..."><?= e($maintenanceData['notes'] ?? '') ?></textarea>
                        <div class="invalid-feedback">
                            <?= $errors['notes'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-3">
                            <a href="/maintenance" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Abbrechen
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Prüfung erstellen
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

// Auto-calculate next date on page load if not already set
window.addEventListener('DOMContentLoaded', function() {
    const lastDateInput = document.getElementById('last_inspection_date');
    const intervalInput = document.getElementById('interval_months');
    const nextDateInput = document.getElementById('next_inspection_date');

    if (!nextDateInput.value && lastDateInput.value && intervalInput.value) {
        calculateNextDate();
    }
});
</script>

<?php $this->stop(); ?>
