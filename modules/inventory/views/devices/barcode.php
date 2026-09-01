<div class="device-barcode">
    <div class="page-header">
        <h1 class="page-title">Barcode für Gerät</h1>
        <p class="page-subtitle">
            <a href="<?= route('devices.show', ['id' => $device['id']]) ?>" class="text-primary">
                <?= e($device['name']) ?>
            </a>
            <span class="text-muted">|</span>
            <code><?= e($device['internal_id']) ?></code>
        </p>
    </div>

    <div class="barcode-container">
        <div class="barcode-card">
            <div class="barcode-card-header">
                <h2 class="barcode-card-title">Barcode</h2>
            </div>
            <div class="barcode-card-body">
                <div class="barcode-image">
                    <?= $barcode ?>
                </div>
                <div class="barcode-identifier">
                    <code><?= e($device['internal_id']) ?></code>
                </div>
            </div>
            <div class="barcode-card-footer">
                <div class="barcode-actions">
                    <button onclick="downloadBarcode()" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 15V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 15V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Herunterladen
                    </button>
                    
                    <button onclick="printBarcode()" class="btn btn-secondary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 21V17C17 16.4696 16.7893 16.0391 16.4142 15.7893C16.0391 15.5395 15.5304 15.4696 15 15.4696H9C8.46957 15.4696 7.96086 15.5395 7.58579 15.7893C7.21071 16.0391 7 16.4696 7 17V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 11C7 10.4696 7.21071 10.0391 7.58579 9.78929C7.96086 9.53948 8.46957 9.46957 9 9.46957H15C15.5304 9.46957 16.0391 9.53948 16.4142 9.78929C16.7893 10.0391 17 10.4696 17 11V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 3H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Drucken
                    </button>
                    
                    <a href="<?= route('devices.qr', ['id' => $device['id']]) ?>" class="btn btn-info">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 3H10V10H3V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14 3H21V10H14V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14 14H21V21H14V14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 14H10V21H3V14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M10 10H14V14H10V10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        QR-Code anzeigen
                    </a>
                </div>
            </div>
        </div>

        <div class="barcode-info">
            <div class="barcode-info-card">
                <h3 class="barcode-info-title">Informationen</h3>
                <p>Scannen Sie diesen Barcode mit einem Barcode-Scanner, um das Gerät schnell zu identifizieren.</p>
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
// Download barcode as image
function downloadBarcode() {
    const barcodeImage = document.querySelector('.barcode-image img');
    if (!barcodeImage) return;

    const link = document.createElement('a');
    link.download = 'barcode-<?= e($device["internal_id"]) ?>.png';
    link.href = barcodeImage.src;
    link.click();
}

// Print barcode
function printBarcode() {
    const barcodeCard = document.querySelector('.barcode-card');
    if (!barcodeCard) return;

    // Create a print window
    const printWindow = window.open('', '_blank');
    printWindow.document.write('<html><head><title>Barcode: <?= e($device["internal_id"]) ?></title>');
    printWindow.document.write('<style>body { display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: white; } .barcode-print { text-align: center; } .barcode-print img { width: 300px; height: 100px; } .barcode-print code { display: block; margin-top: 10px; font-size: 16px; }</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<div class="barcode-print">');
    printWindow.document.write(barcodeCard.innerHTML);
    printWindow.document.write('</div>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}
</script>
