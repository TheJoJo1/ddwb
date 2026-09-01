<?php

/** @var array $packlistData */
/** @var array $errors */
/** @var array $availableDevices */
/** @var array $availableCases */
/** @var array $statusOptions */
/** @var array $itemTypeOptions */

$title = 'Neue Packliste - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Neue Packliste</h1>
            <p class="text-muted mb-0">Erstellen Sie eine neue Packliste</p>
        </div>
        <a href="/packlists" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Zurück zur Liste
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Packlisten Informationen</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="/packlists" class="needs-validation" novalidate>
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label required">Name</label>
                        <input type="text" 
                               class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                               id="name" 
                               name="name" 
                               value="<?= e($packlistData['name'] ?? '') ?>" 
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
                                    <?= ($packlistData['status'] ?? Packlist::STATUS_DRAFT) === $value ? 'selected' : '' ?>>
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
                                  placeholder="Beschreibung der Packliste..."><?= e($packlistData['description'] ?? '') ?></textarea>
                        <div class="invalid-feedback">
                            <?= $errors['description'][0] ?? '' ?>
                        </div>
                    </div>

                    <!-- Add First Item -->
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Ersten Artikel hinzufügen (optional)</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="item_type" class="form-label">Typ</label>
                                        <select class="form-select" id="item_type" name="item_type">
                                            <?php foreach ($itemTypeOptions as $value => $label): ?>
                                                <option value="<?= e($value) ?>">
                                                    <?= e($label) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-5">
                                        <label for="item_id" class="form-label">Artikel</label>
                                        <select class="form-select" id="item_id" name="item_id">
                                            <option value="" selected disabled>Bitte wählen Sie einen Artikel aus</option>
                                            <optgroup label="Geräte">
                                                <?php foreach ($availableDevices as $device): ?>
                                                    <option value="device-<?= e($device['id']) ?>">
                                                        <?= e($device['internal_id']) ?> - <?= e($device['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                            <optgroup label="Cases">
                                                <?php foreach ($availableCases as $case): ?>
                                                    <option value="case-<?= e($case['id']) ?>">
                                                        <?= e($case['internal_id']) ?> - <?= e($case['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="quantity" class="form-label">Menge</label>
                                        <input type="number" 
                                               class="form-control" 
                                               id="quantity" 
                                               name="quantity" 
                                               value="1" 
                                               min="1">
                                    </div>

                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-primary w-100" onclick="updateItemSelection()">
                                            <i class="bi bi-check"></i> Auswählen
                                        </button>
                                    </div>

                                    <div class="col-12">
                                        <label for="item_notes" class="form-label">Notizen zum Artikel</label>
                                        <textarea class="form-control" 
                                                  id="item_notes" 
                                                  name="item_notes" 
                                                  rows="2" 
                                                  placeholder="Notizen zum Artikel..."></textarea>
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
                                  placeholder="Zusätzliche Notizen zur Packliste..."><?= e($packlistData['notes'] ?? '') ?></textarea>
                        <div class="invalid-feedback">
                            <?= $errors['notes'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-3">
                            <a href="/packlists" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Abbrechen
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Packliste erstellen
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateItemSelection() {
    const itemTypeSelect = document.getElementById('item_type');
    const itemIdSelect = document.getElementById('item_id');
    const itemIdInput = document.createElement('input');
    
    itemIdInput.type = 'hidden';
    itemIdInput.name = 'item_id';
    
    const selectedOption = itemIdSelect.options[itemIdSelect.selectedIndex];
    if (selectedOption && selectedOption.value) {
        const parts = selectedOption.value.split('-');
        if (parts.length === 2) {
            itemIdInput.value = parts[1];
            itemTypeSelect.value = parts[0];
            
            // Remove existing hidden input if any
            const existingInput = document.querySelector('input[name="item_id"][type="hidden"]');
            if (existingInput) {
                existingInput.remove();
            }
            
            document.getElementById('item_type').parentNode.appendChild(itemIdInput);
        }
    }
}

// Initialize item selection on page load
window.addEventListener('DOMContentLoaded', function() {
    const itemTypeSelect = document.getElementById('item_type');
    const itemIdSelect = document.getElementById('item_id');
    
    // Update item ID input when selection changes
    itemIdSelect.addEventListener('change', function() {
        updateItemSelection();
    });
    
    // Also update when item type changes
    itemTypeSelect.addEventListener('change', function() {
        updateItemSelection();
    });
});
</script>

<?php $this->stop(); ?>
