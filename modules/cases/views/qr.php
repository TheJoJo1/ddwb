<?php

/** @var array $case */
/** @var string $qrUrl */

$title = 'QR-Code: ' . e($case['name']) . ' - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">QR-Code: <?= e($case['name']) ?></h1>
            <p class="text-muted mb-0">
                <span class="badge bg-secondary">ID: <?= e($case['internal_id']) ?></span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="/cases/<?= e($case['id']) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Zurück zum Case
            </a>
            <button class="btn btn-primary" onclick="downloadQR()">
                <i class="bi bi-download"></i> QR-Code herunterladen
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
                    <h5 class="mb-0">QR-Code für Case: <?= e($case['name']) ?></h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <!-- QR Code will be generated here -->
                        <div id="qr-code" class="d-inline-block"></div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        Scannen Sie diesen QR-Code mit einem mobilen Gerät, um schnell auf diesen Case zuzugreifen.
                    </div>

                    <div class="mt-4">
                        <h6>Case Informationen</h6>
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Name:</dt>
                            <dd class="col-sm-8"><?= e($case['name']) ?></dd>

                            <dt class="col-sm-4">Interne ID:</dt>
                            <dd class="col-sm-8"><?= e($case['internal_id']) ?></dd>

                            <dt class="col-sm-4">Beschreibung:</dt>
                            <dd class="col-sm-8"><?= e($case['description'] ?? '-') ?></dd>

                            <dt class="col-sm-4">URL:</dt>
                            <dd class="col-sm-8">
                                <code class="text-break"><?= e($qrUrl) ?></code>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Generate QR code when page loads
window.addEventListener('DOMContentLoaded', function() {
    const qrUrl = '<?= addslashes($qrUrl) ?>';
    const qrCodeElement = document.getElementById('qr-code');
    
    // Use the QR code library to generate the QR code
    if (typeof QRCode !== 'undefined') {
        new QRCode(qrCodeElement, {
            text: qrUrl,
            width: 256,
            height: 256,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    } else {
        // Fallback: Show the URL as a link
        qrCodeElement.innerHTML = '<div class="alert alert-warning">QR-Code-Bibliothek nicht geladen. <a href="' + qrUrl + '">Direkter Link</a></div>';
    }
});

// Download QR code as PNG
function downloadQR() {
    const qrUrl = '<?= addslashes($qrUrl) ?>';
    const qrCodeElement = document.getElementById('qr-code');
    
    // Create a temporary link with the QR code image
    // This is a simple approach - in production, you might want to use a server-side solution
    const link = document.createElement('a');
    link.href = '/api/generate-qr?url=' + encodeURIComponent(qrUrl) + '&size=256';
    link.download = 'case-<?= e($case["internal_id"]) ?>-qr.png';
    link.target = '_blank';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<?php $this->stop(); ?>
