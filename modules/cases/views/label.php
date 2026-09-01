<?php

/** @var array $case */
/** @var string $qrUrl */
/** @var string $barcodeData */

$title = 'Label: ' . e($case['name']) . ' - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Label: <?= e($case['name']) ?></h1>
            <p class="text-muted mb-0">
                <span class="badge bg-secondary">ID: <?= e($case['internal_id']) ?></span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="/cases/<?= e($case['id']) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Zurück zum Case
            </a>
            <button class="btn btn-primary" onclick="downloadLabel()">
                <i class="bi bi-download"></i> Label herunterladen
            </button>
            <button class="btn btn-outline-dark" onclick="window.print()">
                <i class="bi bi-printer"></i> Drucken
            </button>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Label für Case: <?= e($case['name']) ?></h5>
                </div>
                <div class="card-body">
                    <!-- Label Preview -->
                    <div id="label-preview" class="border p-4 mb-4 bg-white" style="max-width: 400px; margin: 0 auto;">
                        <div class="text-center">
                            <h4 class="mb-2">DDWB</h4>
                            <h5 class="mb-1"><?= e($case['name']) ?></h5>
                            <p class="mb-3 small text-muted"><?= e($case['internal_id']) ?></p>
                            
                            <div class="d-flex justify-content-center gap-3 mb-3">
                                <div class="d-inline-block">
                                    <div id="label-qr" class="d-inline-block" style="width: 100px; height: 100px;"></div>
                                </div>
                                <div class="d-inline-block">
                                    <svg id="label-barcode" style="width: 150px; height: 50px;"></svg>
                                </div>
                            </div>
                            
                            <?php if (!empty($case['description'])): ?>
                                <p class="small text-muted mb-0"><?= e($case['description']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="text-center mb-4">
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-primary active" onclick="setLabelSize('small')">Klein</button>
                            <button class="btn btn-outline-primary" onclick="setLabelSize('medium')">Mittel</button>
                            <button class="btn btn-outline-primary" onclick="setLabelSize('large')">Groß</button>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        Dieses Label enthält QR-Code und Barcode für schnelles Scannen. 
                        Sie können das Label herunterladen oder direkt drucken.
                    </div>

                    <div class="mt-4">
                        <h6>Label Informationen</h6>
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Name:</dt>
                            <dd class="col-sm-8"><?= e($case['name']) ?></dd>

                            <dt class="col-sm-4">Interne ID:</dt>
                            <dd class="col-sm-8"><?= e($case['internal_id']) ?></dd>

                            <dt class="col-sm-4">QR-Code URL:</dt>
                            <dd class="col-sm-8"><code class="text-break"><?= e($qrUrl) ?></code></dd>

                            <dt class="col-sm-4">Barcode Daten:</dt>
                            <dd class="col-sm-8"><code><?= e($barcodeData) ?></code></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Label sizes
const labelSizes = {
    small: { width: 300, height: 150, qrSize: 80, barcodeWidth: 120, barcodeHeight: 40 },
    medium: { width: 400, height: 200, qrSize: 100, barcodeWidth: 150, barcodeHeight: 50 },
    large: { width: 500, height: 250, qrSize: 120, barcodeWidth: 180, barcodeHeight: 60 }
};

let currentSize = 'medium';

// Generate QR code and barcode
window.addEventListener('DOMContentLoaded', function() {
    generateLabel();
});

function generateLabel() {
    const qrUrl = '<?= addslashes($qrUrl) ?>';
    const barcodeData = '<?= addslashes($barcodeData) ?>';
    const size = labelSizes[currentSize];

    // Generate QR code
    const qrElement = document.getElementById('label-qr');
    if (typeof QRCode !== 'undefined') {
        qrElement.innerHTML = '';
        new QRCode(qrElement, {
            text: qrUrl,
            width: size.qrSize,
            height: size.qrSize,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    // Generate barcode
    const barcodeElement = document.getElementById('label-barcode');
    if (typeof JsBarcode !== 'undefined') {
        try {
            JsBarcode(barcodeElement, barcodeData, {
                format: 'CODE128',
                lineColor: '#000000',
                width: 2,
                height: size.barcodeHeight,
                displayValue: true,
                fontSize: 14,
                margin: 5
            });
        } catch (e) {
            barcodeElement.innerHTML = '<div class="alert alert-warning small">Barcode: ' + barcodeData + '</div>';
        }
    }

    // Update preview size
    const preview = document.getElementById('label-preview');
    preview.style.width = size.width + 'px';
    preview.style.height = size.height + 'px';
}

function setLabelSize(size) {
    currentSize = size;
    generateLabel();

    // Update active button
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
}

// Download label as PDF
function downloadLabel() {
    const caseInternalId = '<?= e($case["internal_id"]) ?>';
    
    // Open PDF generation in new tab
    window.open('/api/generate-label?type=case&id=<?= e($case["id"]) ?>&size=' + currentSize, '_blank');
}
</script>

<?php $this->stop(); ?>
