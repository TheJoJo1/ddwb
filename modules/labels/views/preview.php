<?php
/**
 * @var array $template
 * @var array $items
 * @var string $itemType
 */

$title = 'Label-Vorschau';
?>

<?php $this->start('content'); ?>

<div class="page-header">
    <div class="page-header-content">
        <h1>
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.53929C4.21071 2.86785 4 3.29815 4 3.79815V20.2019C4 20.7019 4.21071 21.1321 4.58579 21.4608C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4608C19.7893 21.1321 20 20.7019 20 20.2019V7.79815C20 7.29815 19.7893 6.86785 19.4142 6.53929C19.0391 6.21071 18.5304 6 18 6L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M14 2V6H18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Label-Vorschau
        </h1>
        <p class="page-subtitle">Vorschau der Labels vor dem Druck</p>
    </div>
    <div class="page-header-actions">
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 9V2H18V6H22V20C22 20.5304 21.7893 21.0391 21.4142 21.4142C21.0391 21.7893 20.5304 22 20 22H4C3.46957 22 2.96086 21.7893 2.58579 21.4142C2.21071 21.0391 2 20.5304 2 20V9H6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M6 18H4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M18 9H16V2H18V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M18 18H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Drucken
        </button>
        <a href="<?= route('labels.designer') ?>" class="btn btn-ghost">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Zurück zum Designer
        </a>
    </div>
</div>

<?php if (empty($items)): ?>
<div class="alert alert-warning">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 9V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M12 15V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
    </svg>
    <strong>Hinweis:</strong> Keine Artikel zum Anzeigen ausgewählt.
</div>
<?php else: ?>

<div class="card">
    <div class="card-header">
        <h2>Vorlageninformationen</h2>
    </div>
    <div class="card-body">
        <div class="details-grid">
            <div class="detail-item">
                <div class="detail-label">Vorlage</div>
                <div class="detail-value"><?= e($template['name']) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Typ</div>
                <div class="detail-value"><?= e(ucfirst($itemType)) ?>s</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Anzahl Artikel</div>
                <div class="detail-value"><?= count($items) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Label-Größe</div>
                <div class="detail-value"><?= e($template['width']) ?> × <?= e($template['height']) ?> <?= e($template['unit'] ?? 'mm') ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Label-Vorschau</h2>
        <p class="card-subtitle">Jedes Label wird auf einem eigenen Blatt angezeigt</p>
    </div>
    <div class="card-body">
        <div class="label-preview-grid">
            <?php foreach ($items as $item): ?>
            <div class="label-preview-item" 
                 style="width: <?= $template['width'] + 20 ?>mm; 
                        height: <?= $template['height'] + 20 ?>mm;
                        margin: 10mm;
                        border: 1px solid #eee;
                        padding: 10mm;
                        page-break-after: always;">
                
                <div class="label-preview-content" 
                     style="width: <?= $template['width'] ?>mm; 
                            height: <?= $template['height'] ?>mm;
                            border: 1px dashed #ccc;
                            position: relative;">
                    
                    <?php if ($template['include_name']): ?>
                    <div style="text-align: center; margin-bottom: 5mm; font-size: <?= $template['font_size'] ?? 10 ?>pt;">
                        <strong><?= e($item['name']) ?></strong>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($template['include_internal_id']): ?>
                    <div style="text-align: center; margin-bottom: 5mm; font-size: <?= ($template['font_size'] ?? 10) - 2 ?>pt;">
                        ID: <?= e($item['internal_id']) ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($template['include_serial_number'] && !empty($item['serial_number'])): ?>
                    <div style="text-align: center; margin-bottom: 5mm; font-size: <?= ($template['font_size'] ?? 10) - 2 ?>pt;">
                        S/N: <?= e($item['serial_number']) ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($template['include_qr']): ?>
                    <div style="width: <?= $template['qr_size'] ?? 30 ?>mm; 
                                height: <?= $template['qr_size'] ?? 30 ?>mm; 
                                margin: 5mm auto; 
                                border: 1px solid #ddd; 
                                display: flex; 
                                align-items: center; 
                                justify-content: center;">
                        <img src="<?= route('devices.qr', ['id' => $item['id']]) ?>" 
                             alt="QR-Code für <?= e($item['internal_id']) ?>"
                             style="max-width: 100%; max-height: 100%;">
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($template['include_barcode']): ?>
                    <div style="width: 80%; margin: 5mm auto; border: 1px solid #ddd; padding: 2mm; text-align: center;">
                        <img src="<?= route('devices.barcode', ['id' => $item['id']]) ?>" 
                             alt="Barcode für <?= e($item['internal_id']) ?>"
                             style="max-width: 100%;">
                    </div>
                    <?php endif; ?>
                    
                </div>
                
                <div class="label-preview-footer" style="margin-top: 5mm; text-align: center; font-size: 8pt; color: #666;">
                    <?= e($itemType === 'device' ? 'Gerät' : 'Case') ?>: <?= e($item['name']) ?> (<?= e($item['internal_id']) ?>)
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="card-footer">
        <p class="text-muted">
            <strong>Hinweis:</strong> Diese Vorschau zeigt die Labels in ihrer tatsächlichen Größe. 
            Zum Drucken klicken Sie auf die "Drucken" Schaltfläche oben.
        </p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Druckoptionen</h2>
    </div>
    <div class="card-body">
        <div class="form">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Anzahl Kopien pro Label</label>
                    <input type="number" id="copies-per-label" value="1" min="1" max="10" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Gesamtanzahl Kopien</label>
                    <input type="number" id="total-copies" value="1" min="1" max="100" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Drucker</label>
                    <select id="printer-select" class="form-select">
                        <option value="">Standarddrucker</option>
                        <option value="label-printer">Label-Drucker</option>
                        <option value="a4">A4 (für Testdruck)</option>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="exportAsPdf()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.53929C4.21071 2.86785 4 3.29815 4 3.79815V20.2019C4 20.7019 4.21071 21.1321 4.58579 21.4608C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4608C19.7893 21.1321 20 20.7019 20 20.2019V7.79815C20 7.29815 19.7893 6.86785 19.4142 6.53929C19.0391 6.21071 18.5304 6 18 6L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 2V6H18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Als PDF exportieren
                </button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 9V2H18V6H22V20C22 20.5304 21.7893 21.0391 21.4142 21.4142C21.0391 21.7893 20.5304 22 20 22H4C3.46957 22 2.96086 21.7893 2.58579 21.4142C2.21071 21.0391 2 20.5304 2 20V9H6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 18H4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18 9H16V2H18V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18 18H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Drucken
                </button>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    
    .label-preview-grid,
    .label-preview-grid * {
        visibility: visible;
    }
    
    .label-preview-grid {
        display: block;
        page-break-inside: avoid;
    }
    
    .label-preview-item {
        page-break-after: always;
        page-break-inside: avoid;
        margin: 0;
        padding: 0;
        border: none;
    }
    
    .page-header,
    .card,
    .btn,
    .alert {
        display: none !important;
    }
}

.label-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(<?= ($template['width'] + 20) * 2 ?>mm, 1fr));
    gap: 10mm;
}

@media (max-width: 768px) {
    .label-preview-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function exportAsPdf() {
    const copiesPerLabel = parseInt(document.getElementById('copies-per-label').value) || 1;
    const totalCopies = parseInt(document.getElementById('total-copies').value) || 1;
    const printer = document.getElementById('printer-select').value;
    
    alert('PDF-Export-Funktion wird in einer späteren Version implementiert.\n\n' +
          'Verwenden Sie die Druckfunktion für eine Vorschau oder speichern Sie die Seite als PDF über den Browser.');
}

// Auto-print on load if in print mode
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('autoprint') === '1') {
        window.print();
    }
});
</script>

<?php $this->end(); ?>
