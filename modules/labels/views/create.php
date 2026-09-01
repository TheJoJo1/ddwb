<?php

/** @var array $templateData */
/** @var array $errors */
/** @var array $typeOptions */
/** @var array $unitOptions */
/** @var array $orientationOptions */
/** @var array $barcodeTypeOptions */
/** @var array $standardSizeOptions */

$title = 'Neue Labelvorlage - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Neue Labelvorlage</h1>
            <p class="text-muted mb-0">Erstellen Sie eine neue Labelvorlage</p>
        </div>
        <a href="/labels/templates" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Zurück zur Liste
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Labelvorlagen Informationen</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="/labels/templates" class="needs-validation" novalidate>
                <?= csrf_field() ?>

                <div class="row g-3">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <label for="name" class="form-label required">Name</label>
                        <input type="text" 
                               class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                               id="name" 
                               name="name" 
                               value="<?= e($templateData['name'] ?? '') ?>" 
                               placeholder="Name der Labelvorlage" 
                               required>
                        <div class="invalid-feedback">
                            <?= $errors['name'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="type" class="form-label required">Typ</label>
                        <select class="form-select <?= isset($errors['type']) ? 'is-invalid' : '' ?>" 
                                id="type" 
                                name="type" 
                                required>
                            <?php foreach ($typeOptions as $value => $label): ?>
                                <option value="<?= e($value) ?>" 
                                    <?= ($templateData['type'] ?? LabelTemplate::TYPE_DEVICE) === $value ? 'selected' : '' ?>>
                                    <?= e($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $errors['type'][0] ?? '' ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Beschreibung</label>
                        <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                  id="description" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="Beschreibung der Labelvorlage..."><?= e($templateData['description'] ?? '') ?></textarea>
                        <div class="invalid-feedback">
                            <?= $errors['description'][0] ?? '' ?>
                        </div>
                    </div>

                    <!-- Dimensions -->
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Abmessungen</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="width" class="form-label required">Breite</label>
                                        <input type="number" 
                                               class="form-control <?= isset($errors['width']) ? 'is-invalid' : '' ?>" 
                                               id="width" 
                                               name="width" 
                                               value="<?= e($templateData['width'] ?? 70) ?>" 
                                               step="0.01" 
                                               min="10" 
                                               required>
                                        <div class="invalid-feedback">
                                            <?= $errors['width'][0] ?? '' ?>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="height" class="form-label required">Höhe</label>
                                        <input type="number" 
                                               class="form-control <?= isset($errors['height']) ? 'is-invalid' : '' ?>" 
                                               id="height" 
                                               name="height" 
                                               value="<?= e($templateData['height'] ?? 40) ?>" 
                                               step="0.01" 
                                               min="10" 
                                               required>
                                        <div class="invalid-feedback">
                                            <?= $errors['height'][0] ?? '' ?>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="unit" class="form-label required">Einheit</label>
                                        <select class="form-select <?= isset($errors['unit']) ? 'is-invalid' : '' ?>" 
                                                id="unit" 
                                                name="unit" 
                                                required>
                                            <?php foreach ($unitOptions as $value => $label): ?>
                                                <option value="<?= e($value) ?>" 
                                                    <?= ($templateData['unit'] ?? LabelTemplate::UNIT_MM) === $value ? 'selected' : '' ?>>
                                                    <?= e($label) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            <?= $errors['unit'][0] ?? '' ?>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="orientation" class="form-label required">Ausrichtung</label>
                                        <select class="form-select <?= isset($errors['orientation']) ? 'is-invalid' : '' ?>" 
                                                id="orientation" 
                                                name="orientation" 
                                                required>
                                            <?php foreach ($orientationOptions as $value => $label): ?>
                                                <option value="<?= e($value) ?>" 
                                                    <?= ($templateData['orientation'] ?? LabelTemplate::ORIENTATION_PORTRAIT) === $value ? 'selected' : '' ?>>
                                                    <?= e($label) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            <?= $errors['orientation'][0] ?? '' ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="standard_size" class="form-label">Standardgröße (optional)</label>
                                        <select class="form-select" id="standard_size" onchange="loadStandardSize()">
                                            <option value="" selected disabled>Standardgröße auswählen</option>
                                            <?php foreach ($standardSizeOptions as $value => $label): ?>
                                                <option value="<?= e($value) ?>">
                                                    <?= e($label) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Options -->
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Inhalt-Optionen</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="include_qr" 
                                                   name="include_qr" 
                                                   value="1" 
                                                   <?= ($templateData['include_qr'] ?? true) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="include_qr">
                                                QR-Code
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="include_barcode" 
                                                   name="include_barcode" 
                                                   value="1" 
                                                   <?= ($templateData['include_barcode'] ?? true) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="include_barcode">
                                                Barcode
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="include_name" 
                                                   name="include_name" 
                                                   value="1" 
                                                   <?= ($templateData['include_name'] ?? true) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="include_name">
                                                Name
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="include_internal_id" 
                                                   name="include_internal_id" 
                                                   value="1" 
                                                   <?= ($templateData['include_internal_id'] ?? true) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="include_internal_id">
                                                Interne ID
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="include_serial_number" 
                                                   name="include_serial_number" 
                                                   value="1" 
                                                   <?= ($templateData['include_serial_number'] ?? false) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="include_serial_number">
                                                Seriennummer
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Styling Options -->
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Stil-Optionen</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="font_size" class="form-label required">Schriftgröße (pt)</label>
                                        <input type="number" 
                                               class="form-control <?= isset($errors['font_size']) ? 'is-invalid' : '' ?>" 
                                               id="font_size" 
                                               name="font_size" 
                                               value="<?= e($templateData['font_size'] ?? 10) ?>" 
                                               step="0.1" 
                                               min="6" 
                                               max="24" 
                                               required>
                                        <div class="invalid-feedback">
                                            <?= $errors['font_size'][0] ?? '' ?>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="qr_size" class="form-label required">QR-Code Größe (mm)</label>
                                        <input type="number" 
                                               class="form-control <?= isset($errors['qr_size']) ? 'is-invalid' : '' ?>" 
                                               id="qr_size" 
                                               name="qr_size" 
                                               value="<?= e($templateData['qr_size'] ?? 20) ?>" 
                                               step="0.1" 
                                               min="10" 
                                               max="100" 
                                               required>
                                        <div class="invalid-feedback">
                                            <?= $errors['qr_size'][0] ?? '' ?>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="barcode_type" class="form-label required">Barcode-Typ</label>
                                        <select class="form-select <?= isset($errors['barcode_type']) ? 'is-invalid' : '' ?>" 
                                                id="barcode_type" 
                                                name="barcode_type" 
                                                required>
                                            <?php foreach ($barcodeTypeOptions as $value => $label): ?>
                                                <option value="<?= e($value) ?>" 
                                                    <?= ($templateData['barcode_type'] ?? LabelTemplate::BARCODE_CODE128) === $value ? 'selected' : '' ?>>
                                                    <?= e($label) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            <?= $errors['barcode_type'][0] ?? '' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Default Template -->
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_default" 
                                           name="is_default" 
                                           value="1" 
                                           <?= ($templateData['is_default'] ?? false) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_default">
                                        <strong>Als Standard-Labelvorlage festlegen</strong>
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Wenn aktiviert, wird diese Vorlage als Standard für neue Label verwendet
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-3">
                            <a href="/labels/templates" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Abbrechen
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Labelvorlage erstellen
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Standard size configurations
const standardSizes = <?= json_encode(LabelTemplate::standardSizes) ?>;

function loadStandardSize() {
    const select = document.getElementById('standard_size');
    const sizeKey = select.value;
    
    if (sizeKey && standardSizes[sizeKey]) {
        const config = standardSizes[sizeKey];
        
        document.getElementById('width').value = config.width;
        document.getElementById('height').value = config.height;
        document.getElementById('unit').value = config.unit;
        document.getElementById('font_size').value = config.font_size;
        document.getElementById('qr_size').value = config.qr_size;
    }
}
</script>

<?php $this->stop(); ?>
