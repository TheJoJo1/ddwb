<?php
/**
 * @var array $template
 * @var array $errors
 * @var array $typeOptions
 * @var array $unitOptions
 * @var array $orientationOptions
 * @var array $barcodeTypeOptions
 */

$title = 'Labelvorlage bearbeiten: ' . e($template['name']);
?>

<?php $this->start('content'); ?>

<div class="page-header">
    <div class="page-header-content">
        <h1>
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.53929C4.21071 2.86785 4 3.29815 4 3.79815V20.2019C4 20.7019 4.21071 21.1321 4.58579 21.4608C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4608C19.7893 21.1321 20 20.7019 20 20.2019V7.79815C20 7.29815 19.7893 6.86785 19.4142 6.53929C19.0391 6.21071 18.5304 6 18 6L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M14 2V6H18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16 10H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16 14H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16 18H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Labelvorlage bearbeiten
        </h1>
        <p class="page-subtitle"><?= e($template['name']) ?></p>
    </div>
    <div class="page-header-actions">
        <a href="<?= route('labels.templates.show', ['id' => $template['id']]) ?>" class="btn btn-ghost">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M1 12C1 12 5 20 12 20C19 20 23 12 23 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M1 12L23 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Anzeigen
        </a>
    </div>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
        <path d="M15 9L9 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M9 9L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <strong>Fehler:</strong> Bitte korrigieren Sie die markierten Felder.
</div>
<?php endif; ?>

<form action="<?= route('labels.templates.update', ['id' => $template['id']]) ?>" method="POST" class="form">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="PUT">

    <div class="card">
        <div class="card-header">
            <h2>Allgemeine Informationen</h2>
        </div>
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group <?= isset($errors['name']) ? 'has-error' : '' ?>">
                    <label for="name" class="form-label">Name *</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-input" 
                        value="<?= e($template['name'] ?? old('name')) ?>"
                        required
                        placeholder="z.B. Geräte-Label A4"
                    >
                    <?php if (isset($errors['name'])): ?>
                        <div class="form-error"><?= e($errors['name']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group <?= isset($errors['type']) ? 'has-error' : '' ?>">
                    <label for="type" class="form-label">Typ *</label>
                    <select id="type" name="type" class="form-select" required>
                        <option value="">Bitte auswählen</option>
                        <?php foreach ($typeOptions as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= ($template['type'] ?? old('type')) === $value ? 'selected' : '' ?>>
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['type'])): ?>
                        <div class="form-error"><?= e($errors['type']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Beschreibung</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        class="form-textarea"
                        rows="3"
                        placeholder="z.B. Label für Geräte mit QR-Code und Barcode"
                    ><?= e($template['description'] ?? old('description') ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Standardvorlage</label>
                    <div class="form-toggle">
                        <label class="toggle-switch">
                            <input 
                                type="checkbox" 
                                name="is_default" 
                                value="1" 
                                <?= ($template['is_default'] ?? false) ? 'checked' : '' ?>
                            >
                            <span class="toggle-slider"></span>
                        </label>
                        <span class="toggle-label">Ist Standardvorlage für diesen Typ</span>
                    </div>
                    <p class="form-hint">Wenn aktiviert, wird diese Vorlage automatisch für neue Labels verwendet.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Abmessungen</h2>
        </div>
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group <?= isset($errors['width']) ? 'has-error' : '' ?>">
                    <label for="width" class="form-label">Breite *</label>
                    <div class="form-input-group">
                        <input 
                            type="number" 
                            id="width" 
                            name="width" 
                            class="form-input" 
                            value="<?= e($template['width'] ?? old('width') ?? 50) ?>"
                            step="0.1"
                            min="10"
                            required
                        >
                        <select id="unit" name="unit" class="form-select" style="width: 100px;" required>
                            <?php foreach ($unitOptions as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= ($template['unit'] ?? old('unit') ?? 'mm') === $value ? 'selected' : '' ?>>
                                    <?= e($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if (isset($errors['width'])): ?>
                        <div class="form-error"><?= e($errors['width']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group <?= isset($errors['height']) ? 'has-error' : '' ?>">
                    <label for="height" class="form-label">Höhe *</label>
                    <div class="form-input-group">
                        <input 
                            type="number" 
                            id="height" 
                            name="height" 
                            class="form-input" 
                            value="<?= e($template['height'] ?? old('height') ?? 30) ?>"
                            step="0.1"
                            min="10"
                            required
                        >
                        <span class="form-input-suffix">mm</span>
                    </div>
                    <?php if (isset($errors['height'])): ?>
                        <div class="form-error"><?= e($errors['height']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group <?= isset($errors['orientation']) ? 'has-error' : '' ?>">
                    <label for="orientation" class="form-label">Ausrichtung *</label>
                    <select id="orientation" name="orientation" class="form-select" required>
                        <?php foreach ($orientationOptions as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= ($template['orientation'] ?? old('orientation') ?? 'portrait') === $value ? 'selected' : '' ?>>
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['orientation'])): ?>
                        <div class="form-error"><?= e($errors['orientation']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Inhalte</h2>
        </div>
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">QR-Code einbeziehen</label>
                    <div class="form-toggle">
                        <label class="toggle-switch">
                            <input 
                                type="checkbox" 
                                name="include_qr" 
                                value="1" 
                                <?= ($template['include_qr'] ?? true) ? 'checked' : '' ?>
                            >
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Barcode einbeziehen</label>
                    <div class="form-toggle">
                        <label class="toggle-switch">
                            <input 
                                type="checkbox" 
                                name="include_barcode" 
                                value="1" 
                                <?= ($template['include_barcode'] ?? true) ? 'checked' : '' ?>
                            >
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>

                <div class="form-group <?= isset($errors['barcode_type']) ? 'has-error' : '' ?>">
                    <label for="barcode_type" class="form-label">Barcode-Typ *</label>
                    <select id="barcode_type" name="barcode_type" class="form-select" required>
                        <?php foreach ($barcodeTypeOptions as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= ($template['barcode_type'] ?? old('barcode_type') ?? 'CODE128') === $value ? 'selected' : '' ?>>
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['barcode_type'])): ?>
                        <div class="form-error"><?= e($errors['barcode_type']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label">Name einbeziehen</label>
                    <div class="form-toggle">
                        <label class="toggle-switch">
                            <input 
                                type="checkbox" 
                                name="include_name" 
                                value="1" 
                                <?= ($template['include_name'] ?? true) ? 'checked' : '' ?>
                            >
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Interne ID einbeziehen</label>
                    <div class="form-toggle">
                        <label class="toggle-switch">
                            <input 
                                type="checkbox" 
                                name="include_internal_id" 
                                value="1" 
                                <?= ($template['include_internal_id'] ?? true) ? 'checked' : '' ?>
                            >
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Seriennummer einbeziehen</label>
                    <div class="form-toggle">
                        <label class="toggle-switch">
                            <input 
                                type="checkbox" 
                                name="include_serial_number" 
                                value="1" 
                                <?= ($template['include_serial_number'] ?? false) ? 'checked' : '' ?>
                            >
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Stil</h2>
        </div>
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group <?= isset($errors['font_size']) ? 'has-error' : '' ?>">
                    <label for="font_size" class="form-label">Schriftgröße * (pt)</label>
                    <input 
                        type="number" 
                        id="font_size" 
                        name="font_size" 
                        class="form-input" 
                        value="<?= e($template['font_size'] ?? old('font_size') ?? 10) ?>"
                        step="0.5"
                        min="6"
                        max="24"
                        required
                    >
                    <?php if (isset($errors['font_size'])): ?>
                        <div class="form-error"><?= e($errors['font_size']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group <?= isset($errors['qr_size']) ? 'has-error' : '' ?>">
                    <label for="qr_size" class="form-label">QR-Code-Größe *</label>
                    <div class="form-input-group">
                        <input 
                            type="number" 
                            id="qr_size" 
                            name="qr_size" 
                            class="form-input" 
                            value="<?= e($template['qr_size'] ?? old('qr_size') ?? 30) ?>"
                            step="0.1"
                            min="10"
                            max="100"
                            required
                        >
                        <span class="form-input-suffix">mm</span>
                    </div>
                    <?php if (isset($errors['qr_size'])): ?>
                        <div class="form-error"><?= e($errors['qr_size']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($template['template_json'] ?? null): ?>
    <div class="card">
        <div class="card-header">
            <h2>Benutzerdefiniertes Template (JSON)</h2>
        </div>
        <div class="card-body">
            <div class="form-group">
                <textarea 
                    id="template_json" 
                    name="template_json" 
                    class="form-textarea code-input"
                    rows="10"
                    placeholder="JSON-Template für fortgeschrittene Layouts"
                ><?= e($template['template_json'] ?? old('template_json') ?? '') ?></textarea>
                <p class="form-hint">Lassen Sie dieses Feld leer, um das Standard-Layout zu verwenden.</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Speichern
        </button>
        <a href="<?= route('labels.templates.show', ['id' => $template['id']]) ?>" class="btn btn-ghost">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Abbrechen
        </a>
    </div>
</form>

<?php $this->end(); ?>
