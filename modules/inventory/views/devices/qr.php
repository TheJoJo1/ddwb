<div class="device-qr">
    <div class="page-header">
        <h1 class="page-title">QR-Code für Gerät</h1>
        <p class="page-subtitle">
            <a href="<?= route('devices.show', ['id' => $device['id']]) ?>" class="text-primary">
                <?= e($device['name']) ?>
            </a>
            <span class="text-muted">|</span>
            <code><?= e($device['internal_id']) ?></code>
        </p>
    </div>

    <div class="qr-container">
        <div class="qr-card">
            <div class="qr-card-header">
                <h2 class="qr-card-title">QR-Code</h2>
            </div>
            <div class="qr-card-body">
                <div class="qr-code">
                    <?= $qrCode ?>
                </div>
                <div class="qr-identifier">
                    <code><?= e($device['internal_id']) ?></code>
                </div>
            </div>
            <div class="qr-card-footer">
                <div class="qr-actions">
                    <button onclick="downloadQrCode()" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 15V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 15V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Herunterladen
                    </button>
                    
                    <button onclick="printQrCode()" class="btn btn-secondary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 21V17C17 16.4696 16.7893 16.0391 16.4142 15.7893C16.0391 15.5395 15.5304 15.4696 15 15.4696H9C8.46957 15.4696 7.96086 15.5395 7.58579 15.7893C7.21071 16.0391 7 16.4696 7 17V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 11C7 10.4696 7.21071 10.0391 7.58579 9.78929C7.96086 9.53948 8.46957 9.46957 9 9.46957H15C15.5304 9.46957 16.0391 9.53948 16.4142 9.78929C16.7893 10.0391 17 10.4696 17 11V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 3H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Drucken
                    </button>
                    
                    <a href="<?= route('devices.barcode', ['id' => $device['id']]) ?>" class="btn btn-info">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 6H4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 4V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 18V20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4 18H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 4V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M20 4H18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 20V18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M20 20H18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M10 4H14V6H10V4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M10 18H14V20H10V18Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8 10H16V12H8V10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8 14H16V16H8V14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Barcode anzeigen
                    </a>
                </div>
            </div>
        </div>

        <div class="qr-info">
            <div class="qr-info-card">
                <h3 class="qr-info-title">Informationen</h3>
                <p>Scannen Sie diesen QR-Code mit einem QR-Code-Scanner oder einer Kamera-App, um das Gerät schnell zu identifizieren.</p>
                <p class="text-muted">
                    <strong>Interne ID:</strong> <?= e($device['internal_id']) ?><br>
                    <strong>Name:</strong> <?= e($device['name']) ?><br>
                    <?php if ($device['serial_number']): ?>
                        <strong>Seriennummer:</strong> <?= e($device['serial_number']) ?><br>
                    <?php endif; ?>
                    <strong>Status:</strong> 
                    <span class="badge badge-<?= \DDWB\Modules\Inventory\Models\Device::getStatusColor($device['status']) ?>">
                        <?= e(\DDWB\Modules\Inventory\Models\Device::getStatusLabel($device['status'])) ?>
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Download QR code as image
function downloadQrCode() {
    const qrCode = document.querySelector('.qr-code');
    if (!qrCode) return;

    // Use html2canvas if available, otherwise simple download
    if (typeof html2canvas !== 'undefined') {
        html2canvas(qrCode, {
            scale: 2,
            backgroundColor: null,
            logging: false,
            useCORS: true
        }).then(function(canvas) {
            const link = document.createElement('a');
            link.download = 'qr-code-<?= e($device["internal_id"]) ?>.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    } else {
        // Fallback: Create a simple download link
        const svg = qrCode.querySelector('svg');
        if (svg) {
            const serializer = new XMLSerializer();
            const source = serializer.serializeToString(svg);
            const blob = new Blob([source], {type: 'image/svg+xml'});
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.download = 'qr-code-<?= e($device["internal_id"]) ?>.svg';
            link.href = url;
            link.click();
            URL.revokeObjectURL(url);
        }
    }
}

// Print QR code
function printQrCode() {
    const qrCard = document.querySelector('.qr-card');
    if (!qrCard) return;

    // Create a print window
    const printWindow = window.open('', '_blank');
    printWindow.document.write('<html><head><title>QR-Code: <?= e($device["internal_id"]) ?></title>');
    printWindow.document.write('<style>body { display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: white; } .qr-print { text-align: center; } .qr-print svg { width: 200px; height: 200px; } .qr-print code { display: block; margin-top: 10px; font-size: 16px; }</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<div class="qr-print">');
    printWindow.document.write(qrCard.innerHTML);
    printWindow.document.write('</div>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}
</script>
