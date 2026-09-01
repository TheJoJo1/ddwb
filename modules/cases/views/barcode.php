<?php

/** @var array $case */
/** @var string $barcodeData */

$title = 'Barcode: ' . e($case['name']) . ' - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Barcode: <?= e($case['name']) ?></h1>
            <p class="text-muted mb-0">
                <span class="badge bg-secondary">ID: <?= e($case['internal_id']) ?></span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="/cases/<?= e($case['id']) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Zurück zum Case
            </a>
            <button class="btn btn-primary" onclick="downloadBarcode()">
                <i class="bi bi-download"></i> Barcode herunterladen
            </button>
            <button class="btn btn-outline-dark" onclick="window.print()">
                <i class="bi bi-printer"></i> Drucken
            </button>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Barcode für Case: <?= e($case['name']) ?></h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <!-- Barcode will be generated here -->
                        <svg id="barcode" class="w-100" style="max-width: 400px; height: 100px;"></svg>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        Dieser Barcode enthält die interne ID: <strong><?= e($barcodeData) ?></strong>
                    </div>

                    <div class="mt-4">
                        <h6>Case Informationen</h6>
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Name:</dt>
                            <dd class="col-sm-8"><?= e($case['name']) ?></dd>

                            <dt class="col-sm-4">Interne ID:</dt>
                            <dd class="col-sm-8"><?= e($case['internal_id']) ?></dd>

                            <dt class="col-sm-4">Daten:</dt>
                            <dd class="col-sm-8"><code><?= e($barcodeData) ?></code></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Generate barcode when page loads
window.addEventListener('DOMContentLoaded', function() {
    const barcodeData = '<?= addslashes($barcodeData) ?>';
    const barcodeElement = document.getElementById('barcode');
    
    // Use the barcode library to generate the barcode
    if (typeof JsBarcode !== 'undefined') {
        try {
            JsBarcode(barcodeElement, barcodeData, {
                format: 'CODE128',
                lineColor: '#000000',
                width: 2,
                height: 80,
                displayValue: true,
                fontSize: 16,
                margin: 10
            });
        } catch (e) {
            barcodeElement.innerHTML = '<div class="alert alert-warning">Barcode-Generierung fehlgeschlagen: ' + e.message + '</div>';
        }
    } else {
        // Fallback: Show the data as text
        barcodeElement.innerHTML = '<div class="alert alert-warning">Barcode-Bibliothek nicht geladen. Daten: <strong>' + barcodeData + '</strong></div>';
    }
});

// Download barcode as SVG
function downloadBarcode() {
    const barcodeData = '<?= addslashes($barcodeData) ?>';
    const caseInternalId = '<?= e($case["internal_id"]) ?>';
    
    // Create a temporary link with the barcode image
    const link = document.createElement('a');
    link.href = '/api/generate-barcode?data=' + encodeURIComponent(barcodeData) + '&type=code128&width=400&height=100';
    link.download = 'case-' + caseInternalId + '-barcode.svg';
    link.target = '_blank';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<?php $this->stop(); ?>
