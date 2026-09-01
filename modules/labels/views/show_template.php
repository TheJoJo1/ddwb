<?php
/**
 * @var array $template
 * @var array $typeOptions
 * @var array $unitOptions
 * @var array $orientationOptions
 * @var array $barcodeTypeOptions
 */

use DDWB\Modules\Labels\Models\LabelTemplate;

$title = 'Labelvorlage: ' . e($template['name']);
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
            <?= e($template['name']) ?>
        </h1>
        <p class="page-subtitle">Labelvorlage anzeigen und verwalten</p>
    </div>
    <div class="page-header-actions">
        <a href="<?= route('labels.templates.edit', ['id' => $template['id']]) ?>" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 4H4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M18 14V20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M20 14L14 20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M20 4L14 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M14 4L20 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M14 4L8 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Bearbeiten
        </a>
        <a href="<?= route('labels.designer', ['template_id' => $template['id']]) ?>" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Designer
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Vorlagen-Details</h2>
    </div>
    <div class="card-body">
        <div class="details-grid">
            <div class="detail-item">
                <div class="detail-label">Name</div>
                <div class="detail-value"><?= e($template['name']) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Beschreibung</div>
                <div class="detail-value"><?= e($template['description'] ?? '-') ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Typ</div>
                <div class="detail-value">
                    <span class="badge badge-<?= $template['type'] === 'device' ? 'primary' : ($template['type'] === 'case' ? 'secondary' : 'info') ?>">
                        <?= e($typeOptions[$template['type']] ?? $template['type']) ?>
                    </span>
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Standardvorlage</div>
                <div class="detail-value">
                    <span class="badge badge-<?= $template['is_default'] ? 'success' : 'ghost' ?>">
                        <?= $template['is_default'] ? 'Ja' : 'Nein' ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="section">
            <h3>Abmessungen</h3>
            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">Breite</div>
                    <div class="detail-value"><?= e($template['width']) ?> <?= e($unitOptions[$template['unit']] ?? $template['unit']) ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Höhe</div>
                    <div class="detail-value"><?= e($template['height']) ?> <?= e($unitOptions[$template['unit']] ?? $template['unit']) ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Einheit</div>
                    <div class="detail-value"><?= e($unitOptions[$template['unit']] ?? $template['unit']) ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Ausrichtung</div>
                    <div class="detail-value"><?= e($orientationOptions[$template['orientation']] ?? $template['orientation']) ?></div>
                </div>
            </div>
        </div>

        <div class="section">
            <h3>Inhalte</h3>
            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">QR-Code</div>
                    <div class="detail-value">
                        <span class="badge badge-<?= $template['include_qr'] ? 'success' : 'ghost' ?>">
                            <?= $template['include_qr'] ? 'Ja' : 'Nein' ?>
                        </span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Barcode</div>
                    <div class="detail-value">
                        <span class="badge badge-<?= $template['include_barcode'] ? 'success' : 'ghost' ?>">
                            <?= $template['include_barcode'] ? 'Ja' : 'Nein' ?>
                        </span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Barcode-Typ</div>
                    <div class="detail-value"><?= e($barcodeTypeOptions[$template['barcode_type']] ?? $template['barcode_type']) ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Name</div>
                    <div class="detail-value">
                        <span class="badge badge-<?= $template['include_name'] ? 'success' : 'ghost' ?>">
                            <?= $template['include_name'] ? 'Ja' : 'Nein' ?>
                        </span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Interne ID</div>
                    <div class="detail-value">
                        <span class="badge badge-<?= $template['include_internal_id'] ? 'success' : 'ghost' ?>">
                            <?= $template['include_internal_id'] ? 'Ja' : 'Nein' ?>
                        </span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Seriennummer</div>
                    <div class="detail-value">
                        <span class="badge badge-<?= $template['include_serial_number'] ? 'success' : 'ghost' ?>">
                            <?= $template['include_serial_number'] ? 'Ja' : 'Nein' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <h3>Stil</h3>
            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">Schriftgröße</div>
                    <div class="detail-value"><?= e($template['font_size']) ?> pt</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">QR-Code-Größe</div>
                    <div class="detail-value"><?= e($template['qr_size']) ?> <?= e($unitOptions[$template['unit']] ?? $template['unit']) ?></div>
                </div>
            </div>
        </div>

        <?php if (!empty($template['template_json'])): ?>
        <div class="section">
            <h3>Benutzerdefiniertes Template</h3>
            <pre class="code-block"><?= e($template['template_json']) ?></pre>
        </div>
        <?php endif; ?>
    </div>
    <div class="card-footer">
        <div class="footer-meta">
            <span>Erstellt: <?= format_datetime($template['created_at']) ?></span>
            <?php if ($template['updated_at'] !== null): ?>
                <span>| Aktualisiert: <?= format_datetime($template['updated_at']) ?></span>
            <?php endif; ?>
            <?php if ($template['deleted_at'] !== null): ?>
                <span class="text-danger">| Gelöscht: <?= format_datetime($template['deleted_at']) ?></span>
            <?php endif; ?>
        </div>
        <div class="footer-actions">
            <a href="<?= route('labels.templates') ?>" class="btn btn-ghost">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Zurück zur Übersicht
            </a>
            <form action="<?= route('labels.templates.destroy', ['id' => $template['id']]) ?>" method="POST" onsubmit="return confirm('Sind Sie sicher, dass Sie diese Labelvorlage löschen möchten?');" style="display: inline;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 6V4C8 3.46957 8.21071 3.03914 8.58579 2.78929C8.96086 2.53948 9.46957 2.46957 10 2.46957H14C14.5304 2.46957 15.0391 2.53948 15.4142 2.78929C15.7893 3.03914 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Löschen
                </button>
            </form>
        </div>
    </div>
</div>

<?php if ($template['is_default']): ?>
<div class="alert alert-info">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
        <path d="M12 16V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M12 8H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <strong>Hinweis:</strong> Dies ist die Standard-Labelvorlage für <?= e($typeOptions[$template['type']] ?? $template['type']) ?>. 
    Sie wird automatisch verwendet, wenn keine andere Vorlage ausgewählt ist.
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2>Vorschau</h2>
    </div>
    <div class="card-body">
        <div class="label-preview" style="width: <?= $template['width'] ?>mm; height: <?= $template['height'] ?>mm; border: 1px dashed #ccc; padding: 10px; margin: 20px auto; max-width: 300px;">
            <div style="font-size: <?= $template['font_size'] ?>pt; text-align: center;">
                <?php if ($template['include_name']): ?>
                    <div style="margin-bottom: 5px;"><strong>[Gerätename]</strong></div>
                <?php endif; ?>
                <?php if ($template['include_internal_id']): ?>
                    <div style="margin-bottom: 5px;">ID: [Interne-ID]</div>
                <?php endif; ?>
                <?php if ($template['include_serial_number']): ?>
                    <div style="margin-bottom: 5px;">S/N: [Seriennummer]</div>
                <?php endif; ?>
                <?php if ($template['include_qr']): ?>
                    <div style="width: <?= $template['qr_size'] ?>mm; height: <?= $template['qr_size'] ?>mm; margin: 10px auto; border: 1px solid #ddd;">
                        [QR-Code]
                    </div>
                <?php endif; ?>
                <?php if ($template['include_barcode']): ?>
                    <div style="width: 80%; margin: 10px auto; border: 1px solid #ddd; padding: 5px;">
                        [Barcode: <?= e($barcodeTypeOptions[$template['barcode_type']] ?? $template['barcode_type']) ?>]
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <p class="text-muted text-center">Dies ist eine Vorschau. Die tatsächlichen Daten werden beim Generieren eingefügt.</p>
    </div>
</div>

<?php $this->end(); ?>
