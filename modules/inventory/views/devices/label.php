<div class="device-label">
    <div class="page-header">
        <h1 class="page-title">Label für Gerät</h1>
        <p class="page-subtitle">
            <a href="<?= route('devices.show', ['id' => $device['id']]) ?>" class="text-primary">
                <?= e($device['name']) ?>
            </a>
            <span class="text-muted">|</span>
            <code><?= e($device['internal_id']) ?></code>
        </p>
    </div>

    <div class="label-container">
        <div class="label-preview">
            <div class="label-card">
                <div class="label-card-header">
                    <h2 class="label-card-title">Label-Vorschau</h2>
                </div>
                <div class="label-card-body">
                    <div class="label-content">
                        <div class="label-header">
                            <div class="label-title">DingeDieWirBesitzen</div>
                        </div>
                        
                        <div class="label-body">
                            <div class="label-field">
                                <span class="label-field-label">Interne ID:</span>
                                <span class="label-field-value"><code><?= e($device['internal_id']) ?></code></span>
                            </div>
                            
                            <div class="label-field">
                                <span class="label-field-label">Name:</span>
                                <span class="label-field-value"><?= e(str_limit($device['name'], 30)) ?></span>
                            </div>
                            
                            <?php if ($device['serial_number']): ?>
                                <div class="label-field">
                                    <span class="label-field-label">Seriennr:</span>
                                    <span class="label-field-value"><code><?= e($device['serial_number']) ?></code></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="label-footer">
                            <div class="label-qr">
                                <?= $qrCode ?>
                            </div>
                            <div class="label-barcode">
                                <?= $barcode ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="label-card-footer">
                    <div class="label-actions">
                        <button onclick="downloadLabel()" class="btn btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 15V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 15V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Herunterladen
                        </button>
                        
                        <button onclick="printLabel()" class="btn btn-secondary">
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
                            Nur QR-Code
                        </a>
                        
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
                            Nur Barcode
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="label-info">
            <div class="label-info-card">
                <h3 class="label-info-title">Label-Informationen</h3>
                <p>Dieses Label enthält alle wichtigen Informationen zum Gerät in einem kompakten Format.</p>
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
// Download label as image
function downloadLabel() {
    const labelCard = document.querySelector('.label-card');
    if (!labelCard) return;

    // Use html2canvas if available
    if (typeof html2canvas !== 'undefined') {
        html2canvas(labelCard, {
            scale: 2,
            backgroundColor: '#ffffff',
            logging: false,
            useCORS: true
        }).then(function(canvas) {
            const link = document.createElement('a');
            link.download = 'label-<?= e($device["internal_id"]) ?>.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    } else {
        // Fallback: Create a simple download
        alert('Für den Download des Labels wird html2canvas benötigt. Bitte verwenden Sie die Druckfunktion.');
    }
}

// Print label
function printLabel() {
    const labelCard = document.querySelector('.label-card');
    if (!labelCard) return;

    // Create a print window
    const printWindow = window.open('', '_blank');
    printWindow.document.write('<html><head><title>Label: <?= e($device["internal_id"]) ?></title>');
    printWindow.document.write('<style>body { display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: white; padding: 20px; } .label-print { max-width: 400px; } .label-print .label-content { border: 1px solid #ccc; padding: 10px; } .label-print .label-header { text-align: center; margin-bottom: 10px; } .label-print .label-title { font-size: 18px; font-weight: bold; } .label-print .label-body { margin-bottom: 10px; } .label-print .label-field { display: flex; margin-bottom: 5px; } .label-print .label-field-label { font-weight: bold; margin-right: 5px; } .label-print .label-footer { display: flex; justify-content: space-between; } .label-print .label-qr, .label-print .label-barcode { margin-top: 10px; }</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<div class="label-print">');
    printWindow.document.write(labelCard.innerHTML);
    printWindow.document.write('</div>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}
</script>
