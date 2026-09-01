<?php
/**
 * @var array|null $template
 * @var array $templates
 * @var array $typeOptions
 * @var array $unitOptions
 * @var array $orientationOptions
 * @var array $barcodeTypeOptions
 * @var array $standardSizeOptions
 */

$title = 'Label-Designer' . ($template ? ': ' . e($template['name']) : '');
?>

<?php $this->start('content'); ?>

<div class="page-header">
    <div class="page-header-content">
        <h1>
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Label-Designer
        </h1>
        <p class="page-subtitle">Erstellen und testen Sie Label-Vorlagen</p>
    </div>
    <div class="page-header-actions">
        <a href="<?= route('labels.templates.create') ?>" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 5V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M5 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Neue Vorlage
        </a>
    </div>
</div>

<div class="designer-layout">
    <!-- Sidebar -->
    <div class="designer-sidebar">
        <div class="card">
            <div class="card-header">
                <h2>Vorlagen</h2>
            </div>
            <div class="card-body">
                <div class="template-list">
                    <a href="<?= route('labels.designer') ?>" class="template-item <?= $template === null ? 'active' : '' ?>">
                        <span class="template-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="template-name">Neue Vorlage</span>
                    </a>
                    <?php foreach ($templates as $tpl): ?>
                    <a href="<?= route('labels.designer', ['template_id' => $tpl['id']]) ?>" class="template-item <?= ($template && $template['id'] == $tpl['id']) ? 'active' : '' ?>">
                        <span class="template-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                                <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </span>
                        <span class="template-name"><?= e($tpl['name']) ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Standardgrößen</h2>
            </div>
            <div class="card-body">
                <div class="size-presets">
                    <?php foreach ($standardSizeOptions as $key => $size): ?>
                    <button type="button" class="btn btn-ghost btn-size-preset" 
                            data-width="<?= $size['width'] ?>"
                            data-height="<?= $size['height'] ?>"
                            data-unit="<?= $size['unit'] ?>"
                            onclick="applySizePreset(this)">
                        <?= e($size['name']) ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Elemente</h2>
            </div>
            <div class="card-body">
                <div class="element-palette">
                    <div class="element-item" draggable="true" data-type="text" onclick="addElement('text')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 21H9.5C8.67157 21 8.03914 20.8176 7.58579 20.4608C7.13242 20.104 6.91421 19.5989 6.91421 19.0114V5.98858C6.91421 5.40107 7.13242 4.89602 7.58579 4.53923C8.03914 4.18244 8.67157 4 9.5 4H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M19 4V12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17 16H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17 20H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Text</span>
                    </div>
                    <div class="element-item" draggable="true" data-type="qr" onclick="addElement('qr')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                            <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2"/>
                            <path d="M9 1H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M1 9H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 23H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M23 19V23" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M19 15V19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15 15H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>QR-Code</span>
                    </div>
                    <div class="element-item" draggable="true" data-type="barcode" onclick="addElement('barcode')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 9H22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 15H22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 21H8V3H2V21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M10 3V21H16V3H10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 3V21H22V3H18Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Barcode</span>
                    </div>
                    <div class="element-item" draggable="true" data-type="image" onclick="addElement('image')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5" fill="currentColor"/>
                            <path d="M21 15L16 10L5 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Bild</span>
                    </div>
                    <div class="element-item" draggable="true" data-type="line" onclick="addElement('line')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L12 22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Linie</span>
                    </div>
                    <div class="element-item" draggable="true" data-type="rect" onclick="addElement('rect')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <span>Rechteck</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="designer-main">
        <div class="card">
            <div class="card-header">
                <h2>Label-Canvas</h2>
                <div class="card-actions">
                    <button type="button" class="btn btn-ghost" onclick="clearCanvas()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8 6V4C8 3.46957 8.21071 3.03914 8.58579 2.78929C8.96086 2.53948 9.46957 2.46957 10 2.46957H14C14.5304 2.46957 15.0391 2.53948 15.4142 2.78929C15.7893 3.03914 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Leeren
                    </button>
                    <button type="button" class="btn btn-ghost" onclick="resetCanvas()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 12C3 12 5 4 12 4C19 4 21 12 21 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 12C3 12 5 20 12 20C19 20 21 12 21 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 12L21 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Zurücksetzen
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="canvas-container" id="canvas-container">
                    <div class="canvas" id="label-canvas" 
                         style="width: <?= $template ? e($template['width']) : '100' ?>mm; 
                                height: <?= $template ? e($template['height']) : '60' ?>mm;
                                background: white;
                                position: relative;
                                border: 1px dashed #ccc;
                                margin: 0 auto;">
                        <!-- Elements will be added here dynamically -->
                        <?php if ($template && $template['template_json']): ?>
                            <?php 
                            $elements = json_decode($template['template_json'], true);
                            if (is_array($elements)):
                                foreach ($elements as $element):
                            ?>
                            <div class="canvas-element" 
                                 data-type="<?= e($element['type']) ?>"
                                 style="position: absolute; 
                                        left: <?= $element['x'] ?? 10 ?>mm; 
                                        top: <?= $element['y'] ?? 10 ?>mm;
                                        width: <?= $element['width'] ?? 50 ?>mm;
                                        height: <?= $element['height'] ?? 20 ?>mm;">
                                <?php if ($element['type'] === 'text'): ?>
                                    <span style="font-size: <?= $element['fontSize'] ?? 10 ?>pt; 
                                          font-family: <?= $element['fontFamily'] ?? 'Arial' ?>;
                                          font-weight: <?= $element['fontWeight'] ?? 'normal' ?>;
                                          color: <?= $element['color'] ?? '#000' ?>;
                                          text-align: <?= $element['textAlign'] ?? 'left' ?>;">
                                        <?= e($element['text'] ?? '[Text]') ?>
                                    </span>
                                <?php elseif ($element['type'] === 'qr'): ?>
                                    <div style="width: 100%; height: 100%; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center;">
                                        <span style="color: #666;">[QR-Code]</span>
                                    </div>
                                <?php elseif ($element['type'] === 'barcode'): ?>
                                    <div style="width: 100%; height: 100%; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center;">
                                        <span style="color: #666;">[Barcode]</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; endif; ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Ruler -->
                    <div class="canvas-ruler canvas-ruler-horizontal">
                        <div class="ruler-scale"></div>
                    </div>
                    <div class="canvas-ruler canvas-ruler-vertical">
                        <div class="ruler-scale"></div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="footer-meta">
                    <span>Größe: <strong id="canvas-size">
                        <?= $template ? e($template['width']) : '100' ?> × <?= $template ? e($template['height']) : '60' ?> mm
                    </strong></span>
                    <span>| Elemente: <strong id="element-count">0</strong></span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Eigenschaften</h2>
            </div>
            <div class="card-body" id="properties-panel" style="display: none;">
                <p class="text-muted">Wählen Sie ein Element aus, um dessen Eigenschaften zu bearbeiten.</p>
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="designer-sidebar-right">
        <div class="card">
            <div class="card-header">
                <h2>Vorschau & Export</h2>
            </div>
            <div class="card-body">
                <div class="preview-section">
                    <label class="form-label">Artikel auswählen</label>
                    <select id="item-type" class="form-select" onchange="loadItems(this.value)">
                        <option value="device">Geräte</option>
                        <option value="case">Cases</option>
                    </select>
                </div>
                <div class="preview-section">
                    <label class="form-label">Verfügbare Artikel</label>
                    <select id="item-ids" class="form-select" multiple size="5">
                        <option value="">Laden...</option>
                    </select>
                </div>
                <div class="preview-section">
                    <label class="form-label">Vorlage</label>
                    <select id="template-select" class="form-select">
                        <option value="">Standard</option>
                        <?php foreach ($templates as $tpl): ?>
                            <option value="<?= $tpl['id'] ?>" <?= ($template && $template['id'] == $tpl['id']) ? 'selected' : '' ?>>
                                <?= e($tpl['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-primary" onclick="previewLabels()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Vorschau
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="generateLabels()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.53929C4.21071 2.86785 4 3.29815 4 3.79815V20.2019C4 20.7019 4.21071 21.1321 4.58579 21.4608C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4608C19.7893 21.1321 20 20.7019 20 20.2019V7.79815C20 7.29815 19.7893 6.86785 19.4142 6.53929C19.0391 6.21071 18.5304 6 18 6L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14 2V6H18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Labels generieren
                    </button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Einstellungen</h2>
            </div>
            <div class="card-body">
                <form id="template-settings-form" onsubmit="saveTemplateSettings(event)">
                    <?= csrf_field() ?>
                    <input type="hidden" name="template_id" value="<?= $template ? e($template['id']) : '0' ?>">
                    
                    <div class="form-group">
                        <label for="template-name" class="form-label">Name</label>
                        <input 
                            type="text" 
                            id="template-name" 
                            name="name" 
                            class="form-input"
                            value="<?= $template ? e($template['name']) : '' ?>"
                            placeholder="Name der Vorlage"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="template-width" class="form-label">Breite (mm)</label>
                        <input 
                            type="number" 
                            id="template-width" 
                            name="width" 
                            class="form-input"
                            value="<?= $template ? e($template['width']) : '100' ?>"
                            step="0.1"
                            min="10"
                            onchange="updateCanvasSize()"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="template-height" class="form-label">Höhe (mm)</label>
                        <input 
                            type="number" 
                            id="template-height" 
                            name="height" 
                            class="form-input"
                            value="<?= $template ? e($template['height']) : '60' ?>"
                            step="0.1"
                            min="10"
                            onchange="updateCanvasSize()"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="template-unit" class="form-label">Einheit</label>
                        <select id="template-unit" name="unit" class="form-select">
                            <?php foreach ($unitOptions as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= ($template && $template['unit'] === $value) ? 'selected' : '' ?>>
                                    <?= e($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="template-orientation" class="form-label">Ausrichtung</label>
                        <select id="template-orientation" name="orientation" class="form-select">
                            <?php foreach ($orientationOptions as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= ($template && $template['orientation'] === $value) ? 'selected' : '' ?>>
                                    <?= e($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Speichern
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Element Properties Modal -->
<div class="modal" id="element-properties-modal" style="display: none;">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h2>Element-Eigenschaften</h2>
            <button type="button" class="modal-close" onclick="closeElementProperties()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
        <div class="modal-body" id="element-properties-content">
            <!-- Content will be loaded dynamically -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeElementProperties()">Abbrechen</button>
            <button type="button" class="btn btn-primary" onclick="saveElementProperties()">Speichern</button>
        </div>
    </div>
</div>

<script>
// Designer state
let selectedElement = null;
let elements = [];
let currentTemplateId = <?= $template ? $template['id'] : '0' ?>;

// Initialize designer
function initDesigner() {
    // Load existing elements if template has JSON
    <?php if ($template && $template['template_json']): ?>
    elements = <?= $template['template_json'] ?>;
    updateElementCount();
    <?php endif; ?>
    
    // Set up canvas event listeners
    setupCanvasListeners();
    
    // Load items for preview
    loadItems('device');
}

// Set up canvas event listeners
function setupCanvasListeners() {
    const canvas = document.getElementById('label-canvas');
    
    // Click on canvas to deselect
    canvas.addEventListener('click', function(e) {
        if (e.target === canvas) {
            deselectElement();
        }
    });
}

// Add element to canvas
function addElement(type) {
    const canvas = document.getElementById('label-canvas');
    const canvasWidth = parseFloat(canvas.style.width) || 100;
    const canvasHeight = parseFloat(canvas.style.height) || 60;
    
    const elementId = 'element-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
    
    let elementHtml = '';
    let elementData = {
        id: elementId,
        type: type,
        x: 10,
        y: 10,
        width: 50,
        height: 20
    };
    
    switch (type) {
        case 'text':
            elementHtml = `
                <div class="canvas-element" 
                     data-id="${elementId}"
                     data-type="text"
                     style="position: absolute; left: 10mm; top: 10mm; width: 50mm; height: 20mm;"
                     onclick="selectElement('${elementId}')">
                    <span style="font-size: 10pt; font-family: Arial; color: #000;">Neuer Text</span>
                </div>
            `;
            elementData.text = 'Neuer Text';
            elementData.fontSize = 10;
            elementData.fontFamily = 'Arial';
            elementData.color = '#000';
            elementData.textAlign = 'left';
            break;
            
        case 'qr':
            elementHtml = `
                <div class="canvas-element" 
                     data-id="${elementId}"
                     data-type="qr"
                     style="position: absolute; left: 10mm; top: 10mm; width: 30mm; height: 30mm;"
                     onclick="selectElement('${elementId}')">
                    <div style="width: 100%; height: 100%; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center;">
                        <span style="color: #666;">QR-Code</span>
                    </div>
                </div>
            `;
            elementData.width = 30;
            elementData.height = 30;
            elementData.data = '{{internal_id}}';
            break;
            
        case 'barcode':
            elementHtml = `
                <div class="canvas-element" 
                     data-id="${elementId}"
                     data-type="barcode"
                     style="position: absolute; left: 10mm; top: 10mm; width: 50mm; height: 20mm;"
                     onclick="selectElement('${elementId}')">
                    <div style="width: 100%; height: 100%; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center;">
                        <span style="color: #666;">Barcode</span>
                    </div>
                </div>
            `;
            elementData.width = 50;
            elementData.data = '{{internal_id}}';
            elementData.type = 'CODE128';
            break;
            
        case 'image':
            elementHtml = `
                <div class="canvas-element" 
                     data-id="${elementId}"
                     data-type="image"
                     style="position: absolute; left: 10mm; top: 10mm; width: 20mm; height: 20mm;"
                     onclick="selectElement('${elementId}')">
                    <div style="width: 100%; height: 100%; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center;">
                        <span style="color: #666;">Bild</span>
                    </div>
                </div>
            `;
            elementData.width = 20;
            elementData.height = 20;
            elementData.src = '';
            break;
            
        case 'line':
            elementHtml = `
                <div class="canvas-element" 
                     data-id="${elementId}"
                     data-type="line"
                     style="position: absolute; left: 10mm; top: 10mm; width: 30mm; height: 1px;"
                     onclick="selectElement('${elementId}')">
                    <div style="width: 100%; height: 1px; background: #000;"></div>
                </div>
            `;
            elementData.width = 30;
            elementData.height = 1;
            elementData.color = '#000';
            elementData.thickness = 1;
            break;
            
        case 'rect':
            elementHtml = `
                <div class="canvas-element" 
                     data-id="${elementId}"
                     data-type="rect"
                     style="position: absolute; left: 10mm; top: 10mm; width: 40mm; height: 20mm;"
                     onclick="selectElement('${elementId}')">
                    <div style="width: 100%; height: 100%; border: 1px solid #000;"></div>
                </div>
            `;
            elementData.width = 40;
            elementData.height = 20;
            elementData.color = '#000';
            elementData.fill = false;
            break;
    }
    
    canvas.insertAdjacentHTML('beforeend', elementHtml);
    elements.push(elementData);
    updateElementCount();
    selectElement(elementId);
}

// Select element
function selectElement(elementId) {
    deselectElement();
    
    const element = document.querySelector(`[data-id="${elementId}"]`);
    if (element) {
        element.classList.add('selected');
        selectedElement = elementId;
        
        // Show properties panel
        const propertiesPanel = document.getElementById('properties-panel');
        propertiesPanel.style.display = 'block';
        propertiesPanel.innerHTML = getElementProperties(elementId);
    }
}

// Deselect element
function deselectElement() {
    if (selectedElement) {
        const element = document.querySelector(`[data-id="${selectedElement}"]`);
        if (element) {
            element.classList.remove('selected');
        }
        selectedElement = null;
    }
    
    const propertiesPanel = document.getElementById('properties-panel');
    if (propertiesPanel) {
        propertiesPanel.style.display = 'none';
    }
}

// Get element properties HTML
function getElementProperties(elementId) {
    const element = elements.find(e => e.id === elementId);
    if (!element) return '<p>Element nicht gefunden</p>';
    
    let html = '<div class="form">';
    
    // Common properties
    html += `
        <div class="form-group">
            <label class="form-label">Position X (mm)</label>
            <input type="number" class="form-input" value="${element.x}" 
                   onchange="updateElementProperty('${elementId}', 'x', this.value)" step="0.1">
        </div>
        <div class="form-group">
            <label class="form-label">Position Y (mm)</label>
            <input type="number" class="form-input" value="${element.y}" 
                   onchange="updateElementProperty('${elementId}', 'y', this.value)" step="0.1">
        </div>
        <div class="form-group">
            <label class="form-label">Breite (mm)</label>
            <input type="number" class="form-input" value="${element.width}" 
                   onchange="updateElementProperty('${elementId}', 'width', this.value); updateElementStyle('${elementId}')" step="0.1">
        </div>
        <div class="form-group">
            <label class="form-label">Höhe (mm)</label>
            <input type="number" class="form-input" value="${element.height}" 
                   onchange="updateElementProperty('${elementId}', 'height', this.value); updateElementStyle('${elementId}')" step="0.1">
        </div>
    `;
    
    // Type-specific properties
    switch (element.type) {
        case 'text':
            html += `
                <div class="form-group">
                    <label class="form-label">Text</label>
                    <textarea class="form-textarea" rows="3" 
                              onchange="updateElementProperty('${elementId}', 'text', this.value); updateElementContent('${elementId}')">${element.text || ''}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Schriftgröße (pt)</label>
                    <input type="number" class="form-input" value="${element.fontSize || 10}" 
                           onchange="updateElementProperty('${elementId}', 'fontSize', this.value); updateElementStyle('${elementId}')" step="0.5">
                </div>
                <div class="form-group">
                    <label class="form-label">Schriftart</label>
                    <select class="form-select" onchange="updateElementProperty('${elementId}', 'fontFamily', this.value); updateElementStyle('${elementId}')">
                        <option value="Arial" ${(element.fontFamily || 'Arial') === 'Arial' ? 'selected' : ''}>Arial</option>
                        <option value="Helvetica" ${(element.fontFamily || 'Arial') === 'Helvetica' ? 'selected' : ''}>Helvetica</option>
                        <option value="Times New Roman" ${(element.fontFamily || 'Arial') === 'Times New Roman' ? 'selected' : ''}>Times New Roman</option>
                        <option value="Courier New" ${(element.fontFamily || 'Arial') === 'Courier New' ? 'selected' : ''}>Courier New</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Farbe</label>
                    <input type="color" class="form-input" value="${element.color || '#000'}" 
                           onchange="updateElementProperty('${elementId}', 'color', this.value); updateElementStyle('${elementId}')">
                </div>
                <div class="form-group">
                    <label class="form-label">Ausrichtung</label>
                    <select class="form-select" onchange="updateElementProperty('${elementId}', 'textAlign', this.value); updateElementStyle('${elementId}')">
                        <option value="left" ${(element.textAlign || 'left') === 'left' ? 'selected' : ''}>Links</option>
                        <option value="center" ${(element.textAlign || 'left') === 'center' ? 'selected' : ''}>Zentriert</option>
                        <option value="right" ${(element.textAlign || 'left') === 'right' ? 'selected' : ''}>Rechts</option>
                    </select>
                </div>
            `;
            break;
            
        case 'qr':
            html += `
                <div class="form-group">
                    <label class="form-label">Daten (Variable oder Text)</label>
                    <input type="text" class="form-input" value="${element.data || '{{internal_id}}'}" 
                           onchange="updateElementProperty('${elementId}', 'data', this.value)">
                </div>
                <div class="form-group">
                    <label class="form-label">Größe (mm)</label>
                    <input type="number" class="form-input" value="${element.width}" 
                           onchange="updateElementProperty('${elementId}', 'width', this.value); updateElementProperty('${elementId}', 'height', this.value); updateElementStyle('${elementId}')" step="0.1">
                </div>
            `;
            break;
            
        case 'barcode':
            html += `
                <div class="form-group">
                    <label class="form-label">Daten (Variable oder Text)</label>
                    <input type="text" class="form-input" value="${element.data || '{{internal_id}}'}" 
                           onchange="updateElementProperty('${elementId}', 'data', this.value)">
                </div>
                <div class="form-group">
                    <label class="form-label">Barcode-Typ</label>
                    <select class="form-select" onchange="updateElementProperty('${elementId}', 'barcodeType', this.value)">
                        <option value="CODE128">CODE128</option>
                        <option value="CODE39">CODE39</option>
                        <option value="EAN13">EAN-13</option>
                        <option value="UPC-A">UPC-A</option>
                    </select>
                </div>
            `;
            break;
            
        case 'image':
            html += `
                <div class="form-group">
                    <label class="form-label">Bild-URL</label>
                    <input type="text" class="form-input" value="${element.src || ''}" 
                           onchange="updateElementProperty('${elementId}', 'src', this.value); updateElementContent('${elementId}')">
                </div>
            `;
            break;
            
        case 'line':
            html += `
                <div class="form-group">
                    <label class="form-label">Farbe</label>
                    <input type="color" class="form-input" value="${element.color || '#000'}" 
                           onchange="updateElementProperty('${elementId}', 'color', this.value); updateElementStyle('${elementId}')">
                </div>
                <div class="form-group">
                    <label class="form-label">Dicke (px)</label>
                    <input type="number" class="form-input" value="${element.thickness || 1}" 
                           onchange="updateElementProperty('${elementId}', 'thickness', this.value); updateElementStyle('${elementId}')" min="1">
                </div>
            `;
            break;
            
        case 'rect':
            html += `
                <div class="form-group">
                    <label class="form-label">Farbe</label>
                    <input type="color" class="form-input" value="${element.color || '#000'}" 
                           onchange="updateElementProperty('${elementId}', 'color', this.value); updateElementStyle('${elementId}')">
                </div>
                <div class="form-group">
                    <label class="form-label">Gefüllt</label>
                    <select class="form-select" onchange="updateElementProperty('${elementId}', 'fill', this.value === 'true'); updateElementStyle('${elementId}')">
                        <option value="false" ${(element.fill === false || !element.fill) ? 'selected' : ''}>Nein</option>
                        <option value="true" ${element.fill === true ? 'selected' : ''}>Ja</option>
                    </select>
                </div>
            `;
            break;
    }
    
    html += '</div>';
    return html;
}

// Update element property
function updateElementProperty(elementId, property, value) {
    const index = elements.findIndex(e => e.id === elementId);
    if (index !== -1) {
        // Convert value type
        if (property === 'x' || property === 'y' || property === 'width' || property === 'height' || 
            property === 'fontSize' || property === 'qrSize' || property === 'thickness') {
            elements[index][property] = parseFloat(value);
        } else if (property === 'fill') {
            elements[index][property] = value === true || value === 'true';
        } else {
            elements[index][property] = value;
        }
        
        // Update the element on canvas
        updateElementStyle(elementId);
        updateElementContent(elementId);
    }
}

// Update element style on canvas
function updateElementStyle(elementId) {
    const element = elements.find(e => e.id === elementId);
    if (!element) return;
    
    const canvasElement = document.querySelector(`[data-id="${elementId}"]`);
    if (!canvasElement) return;
    
    canvasElement.style.left = (element.x || 0) + 'mm';
    canvasElement.style.top = (element.y || 0) + 'mm';
    canvasElement.style.width = (element.width || 0) + 'mm';
    canvasElement.style.height = (element.height || 0) + 'mm';
    
    // Update content based on type
    switch (element.type) {
        case 'text':
            const textSpan = canvasElement.querySelector('span');
            if (textSpan) {
                textSpan.style.fontSize = (element.fontSize || 10) + 'pt';
                textSpan.style.fontFamily = element.fontFamily || 'Arial';
                textSpan.style.color = element.color || '#000';
                textSpan.style.textAlign = element.textAlign || 'left';
            }
            break;
            
        case 'line':
            const lineDiv = canvasElement.querySelector('div');
            if (lineDiv) {
                lineDiv.style.height = (element.thickness || 1) + 'px';
                lineDiv.style.background = element.color || '#000';
            }
            break;
            
        case 'rect':
            const rectDiv = canvasElement.querySelector('div');
            if (rectDiv) {
                rectDiv.style.borderColor = element.color || '#000';
                rectDiv.style.background = element.fill ? (element.color || '#000') : 'transparent';
            }
            break;
    }
}

// Update element content
function updateElementContent(elementId) {
    const element = elements.find(e => e.id === elementId);
    if (!element) return;
    
    const canvasElement = document.querySelector(`[data-id="${elementId}"]`);
    if (!canvasElement) return;
    
    switch (element.type) {
        case 'text':
            const textSpan = canvasElement.querySelector('span');
            if (textSpan) {
                textSpan.textContent = element.text || '';
            }
            break;
            
        case 'image':
            const imgDiv = canvasElement.querySelector('div');
            if (imgDiv && element.src) {
                imgDiv.innerHTML = `<img src="${element.src}" style="max-width: 100%; max-height: 100%;">`;
            }
            break;
    }
}

// Update element count
function updateElementCount() {
    const countElement = document.getElementById('element-count');
    if (countElement) {
        countElement.textContent = elements.length;
    }
}

// Clear canvas
function clearCanvas() {
    if (confirm('Möchten Sie alle Elemente vom Canvas entfernen?')) {
        const canvas = document.getElementById('label-canvas');
        canvas.innerHTML = '';
        elements = [];
        updateElementCount();
        deselectElement();
    }
}

// Reset canvas
function resetCanvas() {
    if (confirm('Möchten Sie den Canvas zurücksetzen? Alle nicht gespeicherten Änderungen gehen verloren.')) {
        window.location.reload();
    }
}

// Update canvas size
function updateCanvasSize() {
    const width = parseFloat(document.getElementById('template-width').value) || 100;
    const height = parseFloat(document.getElementById('template-height').value) || 60;
    const unit = document.getElementById('template-unit').value || 'mm';
    
    const canvas = document.getElementById('label-canvas');
    canvas.style.width = width + 'mm';
    canvas.style.height = height + 'mm';
    
    // Update size display
    const sizeElement = document.getElementById('canvas-size');
    if (sizeElement) {
        sizeElement.textContent = width + ' × ' + height + ' ' + unit;
    }
}

// Apply size preset
function applySizePreset(button) {
    const width = parseFloat(button.dataset.width);
    const height = parseFloat(button.dataset.height);
    const unit = button.dataset.unit || 'mm';
    
    document.getElementById('template-width').value = width;
    document.getElementById('template-height').value = height;
    document.getElementById('template-unit').value = unit;
    
    updateCanvasSize();
}

// Load items for preview
function loadItems(type) {
    const itemIdsSelect = document.getElementById('item-ids');
    if (!itemIdsSelect) return;
    
    itemIdsSelect.innerHTML = '<option value="">Laden...</option>';
    
    fetch('/api/' + type + 's?limit=50')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                itemIdsSelect.innerHTML = '';
                data.data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name || item.internal_id || ('Item ' + item.id);
                    itemIdsSelect.appendChild(option);
                });
            } else {
                itemIdsSelect.innerHTML = '<option value="">Fehler beim Laden</option>';
            }
        })
        .catch(error => {
            console.error('Error loading items:', error);
            itemIdsSelect.innerHTML = '<option value="">Fehler beim Laden</option>';
        });
}

// Preview labels
function previewLabels() {
    const itemType = document.getElementById('item-type').value;
    const itemIdsSelect = document.getElementById('item-ids');
    const templateId = document.getElementById('template-select').value;
    
    const selectedIds = Array.from(itemIdsSelect.selectedOptions).map(opt => opt.value);
    
    if (selectedIds.length === 0) {
        alert('Bitte wählen Sie mindestens einen Artikel aus.');
        return;
    }
    
    // Save current template first if it's new
    if (currentTemplateId === 0) {
        saveTemplateSettings(null, true);
    }
    
    // Open preview in new window or redirect
    const params = new URLSearchParams({
        template_id: templateId || currentTemplateId,
        item_type: itemType,
        item_ids: selectedIds.join(',')
    });
    
    window.open('<?= route("labels.preview") ?>?' + params.toString(), '_blank');
}

// Generate labels
function generateLabels() {
    const itemType = document.getElementById('item-type').value;
    const itemIdsSelect = document.getElementById('item-ids');
    const templateId = document.getElementById('template-select').value;
    
    const selectedIds = Array.from(itemIdsSelect.selectedOptions).map(opt => opt.value);
    
    if (selectedIds.length === 0) {
        alert('Bitte wählen Sie mindestens einen Artikel aus.');
        return;
    }
    
    // Save current template first if it's new
    if (currentTemplateId === 0) {
        saveTemplateSettings(null, true);
    }
    
    // Generate labels
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= route("labels.generate") ?>';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_csrf';
    csrfInput.value = '<?= csrf_token() ?>';
    form.appendChild(csrfInput);
    
    const templateInput = document.createElement('input');
    templateInput.type = 'hidden';
    templateInput.name = 'template_id';
    templateInput.value = templateId || currentTemplateId;
    form.appendChild(templateInput);
    
    const itemTypeInput = document.createElement('input');
    itemTypeInput.type = 'hidden';
    itemTypeInput.name = 'item_type';
    itemTypeInput.value = itemType;
    form.appendChild(itemTypeInput);
    
    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'item_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    const previewInput = document.createElement('input');
    previewInput.type = 'hidden';
    previewInput.name = 'preview';
    previewInput.value = 'false';
    form.appendChild(previewInput);
    
    document.body.appendChild(form);
    form.submit();
}

// Save template settings
function saveTemplateSettings(event, silent = false) {
    if (event) event.preventDefault();
    
    const form = document.getElementById('template-settings-form');
    const formData = new FormData(form);
    
    // Add elements JSON
    formData.set('template_json', JSON.stringify(elements));
    
    fetch('<?= route("labels.templates.store") ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-Token': '<?= csrf_token() ?>',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentTemplateId = data.data.id;
            if (!silent) {
                alert('Vorlage erfolgreich gespeichert!');
            }
            // Update template select
            const templateSelect = document.getElementById('template-select');
            if (templateSelect) {
                const option = document.createElement('option');
                option.value = data.data.id;
                option.textContent = formData.get('name');
                option.selected = true;
                templateSelect.appendChild(option);
            }
        } else {
            alert('Fehler beim Speichern: ' + (data.message || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        console.error('Error saving template:', error);
        alert('Fehler beim Speichern der Vorlage.');
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', initDesigner);
</script>

<style>
.designer-layout {
    display: grid;
    grid-template-columns: 250px 1fr 250px;
    gap: 20px;
    min-height: calc(100vh - 200px);
}

.designer-sidebar, .designer-sidebar-right {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.designer-main {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.template-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.template-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 6px;
    color: var(--text-primary);
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.2s;
}

.template-item:hover {
    background-color: var(--bg-secondary);
}

.template-item.active {
    background-color: var(--primary);
    color: white;
}

.template-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

.template-name {
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.size-presets {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.btn-size-preset {
    font-size: 12px;
    padding: 6px 12px;
}

.element-palette {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
}

.element-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 12px 8px;
    border: 1px solid var(--border);
    border-radius: 6px;
    cursor: grab;
    transition: all 0.2s;
    text-decoration: none;
    color: var(--text-primary);
}

.element-item:hover {
    background-color: var(--bg-secondary);
    border-color: var(--primary);
}

.element-item svg {
    width: 24px;
    height: 24px;
}

.element-item span {
    font-size: 12px;
}

.canvas-container {
    position: relative;
    padding: 20px;
    background: var(--bg-secondary);
    border-radius: 8px;
    overflow: hidden;
}

.canvas {
    position: relative;
    min-height: 100px;
    transform-origin: 0 0;
}

.canvas-element {
    position: absolute;
    cursor: pointer;
    user-select: none;
}

.canvas-element:hover {
    outline: 2px dashed var(--primary);
}

.canvas-element.selected {
    outline: 2px solid var(--primary);
    background: rgba(0, 123, 255, 0.1);
}

.canvas-ruler {
    position: absolute;
    background: var(--bg-tertiary);
    pointer-events: none;
}

.canvas-ruler-horizontal {
    top: 0;
    left: 0;
    right: 0;
    height: 20px;
}

.canvas-ruler-vertical {
    top: 0;
    left: 0;
    bottom: 0;
    width: 20px;
}

.ruler-scale {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        linear-gradient(to right, transparent 1px, transparent 1px),
        linear-gradient(to bottom, transparent 1px, transparent 1px);
    background-size: 10mm 10mm;
}

.canvas-ruler-horizontal .ruler-scale {
    background-image: linear-gradient(to right, transparent 1px, transparent 1px);
}

.canvas-ruler-vertical .ruler-scale {
    background-image: linear-gradient(to bottom, transparent 1px, transparent 1px);
}

.preview-section {
    margin-bottom: 15px;
}

@media (max-width: 1200px) {
    .designer-layout {
        grid-template-columns: 1fr;
    }
    
    .designer-sidebar, .designer-sidebar-right {
        order: 2;
    }
    
    .designer-main {
        order: 1;
    }
}
</style>

<?php $this->end(); ?>
