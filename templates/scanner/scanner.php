<div class="scanner-page">
    <div class="scanner-container">
        <!-- Header -->
        <div class="scanner-header">
            <h1>
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                    <line x1="9" y1="3" x2="9" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="15" y1="3" x2="15" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="9" y1="21" x2="9" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="15" y1="21" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="3" y1="9" x2="21" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="3" y1="15" x2="21" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Scanner
            </h1>
            <p class="scanner-subtitle">Scannen Sie einen QR-Code oder Barcode</p>
        </div>

        <!-- Scanner Area -->
        <div class="scanner-area" id="scanner-area">
            <!-- Video Preview -->
            <div class="scanner-preview" id="scanner-preview">
                <video id="scanner-video" playsinline autoplay muted></video>
                <div class="scanner-overlay" id="scanner-overlay">
                    <div class="scanner-frame"></div>
                    <div class="scanner-guide">
                        <p>Positionieren Sie den Code innerhalb des Rahmens</p>
                    </div>
                </div>
            </div>

            <!-- Manual Input -->
            <div class="scanner-manual" id="scanner-manual">
                <div class="scanner-manual-header">
                    <h3>Manuelle Eingabe</h3>
                    <p>Falls das Scannen nicht funktioniert</p>
                </div>
                <form class="scanner-manual-form" id="manual-form">
                    <div class="form-group">
                        <label for="manual-identifier" class="form-label">Geräte- oder Case-ID</label>
                        <div class="form-input-wrapper">
                            <svg class="form-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 16V8C21 7.46957 20.7893 7.03914 20.4142 6.78929C20.0391 6.53948 19.5304 6.46957 19 6.46957H5C4.46957 6.46957 3.96086 6.53948 3.58579 6.78929C3.21071 7.03914 3 7.46957 3 8V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 16L7 20H17L21 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7 20V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M17 20V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <input 
                                type="text" 
                                id="manual-identifier" 
                                name="identifier" 
                                class="form-input"
                                placeholder="z.B. DEV-0001 oder CASE-0001"
                                autofocus
                            >
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                            <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Suchen
                    </button>
                </form>
            </div>
        </div>

        <!-- Status -->
        <div class="scanner-status" id="scanner-status">
            <div class="scanner-status-icon">
                <svg class="status-icon-initial" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                </svg>
                <svg class="status-icon-scanning" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                    <path d="M12 2V4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M12 18V20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M4.92999 4.93001L6.34001 6.34001" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M17.6599 17.66L19.07 19.07" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M19.07 6.34001L17.6599 7.75001" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M6.34001 19.07L4.92999 17.66" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <svg class="status-icon-success" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                    <path d="M22 11.08V12C22 16.4771 20.4771 19 16.5 19H7.5C3.52285 19 2 16.4771 2 12C2 11.08 2.75 10.5 3.75 10.5H5.25C6.25 10.5 7 11.25 7 12.5C7 13.75 6.25 14.5 5.25 14.5H3.75C2.75 14.5 2 13.75 2 12.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 4L12 14.01L2 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <svg class="status-icon-error" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    <line x1="15" y1="9" x2="9" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="9" y1="9" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="scanner-status-text">
                <span id="status-text">Kamera wird initialisiert...</span>
                <span id="status-detail"></span>
            </div>
        </div>

        <!-- Actions -->
        <div class="scanner-actions">
            <button type="button" class="btn btn-ghost" onclick="toggleScanner()" id="toggle-scanner-btn">
                <svg class="scanner-toggle-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                    <line x1="9" y1="3" x2="9" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="15" y1="3" x2="15" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="9" y1="21" x2="9" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="15" y1="21" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="3" y1="9" x2="21" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="3" y1="15" x2="21" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span id="toggle-scanner-text">Manuelle Eingabe</span>
            </button>
            
            <button type="button" class="btn btn-ghost" onclick="goBack()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Zurück
            </button>
        </div>

        <!-- Result Display -->
        <div class="scanner-result" id="scanner-result" style="display: none;">
            <div class="scanner-result-content">
                <div class="scanner-result-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 11.08V12C22 16.4771 20.4771 19 16.5 19H7.5C3.52285 19 2 16.4771 2 12C2 11.08 2.75 10.5 3.75 10.5H5.25C6.25 10.5 7 11.25 7 12.5C7 13.75 6.25 14.5 5.25 14.5H3.75C2.75 14.5 2 13.75 2 12.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M22 4L12 14.01L2 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="scanner-result-info">
                    <h3 id="result-title">Erfolgreich gescannt!</h3>
                    <p id="result-message"></p>
                    <div class="scanner-result-details" id="result-details"></div>
                </div>
                <button type="button" class="btn btn-primary" onclick="redirectToResult()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Öffnen
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Camera Permission Modal -->
<div class="modal" id="camera-permission-modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Kamera-Zugriff erforderlich</h2>
            <button type="button" class="modal-close" onclick="closeModal('camera-permission-modal')">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <p>Um den Scanner zu verwenden, müssen Sie der Kamera den Zugriff auf Ihre Kamera erlauben.</p>
            <p>Bitte klicken Sie auf "Erlauben" oder "Zulassen", wenn Sie dazu aufgefordert werden.</p>
            <p>Auf mobilen Geräten wird möglicherweise eine Benachrichtigung angezeigt.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="requestCameraPermission()">
                Erneut versuchen
            </button>
            <button type="button" class="btn btn-ghost" onclick="closeModal('camera-permission-modal'); showManualInput();">
                Manuelle Eingabe
            </button>
        </div>
    </div>
</div>

<!-- Unsupported Browser Modal -->
<div class="modal" id="unsupported-browser-modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Browser nicht unterstützt</h2>
            <button type="button" class="modal-close" onclick="closeModal('unsupported-browser-modal')">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <p>Ihr Browser unterstützt die Kamera-API nicht.</p>
            <p>Bitte verwenden Sie einen modernen Browser wie:</p>
            <ul>
                <li>Google Chrome</li>
                <li>Mozilla Firefox</li>
                <li>Microsoft Edge</li>
                <li>Safari (auf iOS 11+ und macOS 10.13+)</li>
            </ul>
            <p>Sie können weiterhin die manuelle Eingabe verwenden.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="closeModal('unsupported-browser-modal'); showManualInput();">
                Manuelle Eingabe
            </button>
        </div>
    </div>
</div>

<script>
// Scanner state
let scannerActive = true;
let codeReader = null;
let videoElement = null;
let resultUrl = null;

// DOM elements
const scannerArea = document.getElementById('scanner-area');
const scannerPreview = document.getElementById('scanner-preview');
const scannerVideo = document.getElementById('scanner-video');
const scannerOverlay = document.getElementById('scanner-overlay');
const scannerManual = document.getElementById('scanner-manual');
const scannerStatus = document.getElementById('scanner-status');
const scannerResult = document.getElementById('scanner-result');
const toggleScannerBtn = document.getElementById('toggle-scanner-btn');
const toggleScannerText = document.getElementById('toggle-scanner-text');
const manualForm = document.getElementById('manual-form');

// Status elements
const statusText = document.getElementById('status-text');
const statusDetail = document.getElementById('status-detail');
const statusIcons = {
    initial: document.querySelector('.status-icon-initial'),
    scanning: document.querySelector('.status-icon-scanning'),
    success: document.querySelector('.status-icon-success'),
    error: document.querySelector('.status-icon-error')
};

// Result elements
const resultTitle = document.getElementById('result-title');
const resultMessage = document.getElementById('result-message');
const resultDetails = document.getElementById('result-details');

// Check if HTTPS is required
const isHttps = window.location.protocol === 'https:';
const isLocalhost = window.location.hostname === 'localhost' || 
                   window.location.hostname === '127.0.0.1' ||
                   window.location.hostname === '';

// Initialize scanner
function initScanner() {
    updateStatus('initial', 'Kamera wird initialisiert...');
    
    // Check if browser supports mediaDevices
    if (typeof navigator.mediaDevices === 'undefined' || 
        typeof navigator.mediaDevices.getUserMedia === 'undefined') {
        showUnsupportedBrowser();
        return;
    }
    
    // Check HTTPS requirement
    if (!isHttps && !isLocalhost) {
        updateStatus('error', 'HTTPS erforderlich', 'Die Kamera-API erfordert eine sichere Verbindung (HTTPS).');
        showModal('camera-permission-modal');
        return;
    }
    
    // Initialize video element
    videoElement = scannerVideo;
    videoElement.style.display = 'block';
    
    // Initialize code reader
    try {
        // Use ZXing library for scanning
        codeReader = new ZXing.BrowserQRCodeReader();
        
        // Get available cameras
        ZXing.BrowserQRCodeReader.listVideoInputDevices()
            .then(devices => {
                if (devices.length === 0) {
                    updateStatus('error', 'Keine Kamera gefunden', 'Keine Kamera an diesem Gerät gefunden.');
                    showManualInput();
                    return;
                }
                
                // Use the back camera on mobile devices
                const backCamera = devices.find(device => 
                    device.label.toLowerCase().includes('back') ||
                    device.label.toLowerCase().includes('environment')
                );
                const cameraId = backCamera ? backCamera.deviceId : undefined;
                
                // Start scanning
                startScanning(cameraId);
            })
            .catch(error => {
                console.error('Error listing cameras:', error);
                updateStatus('error', 'Fehler beim Zugriff auf Kamera', error.message);
                showManualInput();
            });
    } catch (error) {
        console.error('ZXing library error:', error);
        updateStatus('error', 'Fehler beim Laden des Scanners', error.message);
        showManualInput();
    }
}

// Start scanning
function startScanning(cameraId) {
    updateStatus('scanning', 'Scannen...', 'Positionieren Sie den Code innerhalb des Rahmens');
    
    const options = {
        video: {
            deviceId: cameraId ? { exact: cameraId } : undefined,
            facingMode: 'environment',
            width: { ideal: 1280 },
            height: { ideal: 720 }
        },
        qrbox: {
            width: 200,
            height: 200
        }
    };
    
    // Use ZXing to decode from video
    const constraints = cameraId ? 
        { video: { deviceId: { exact: cameraId }, facingMode: 'environment' } } :
        { video: { facingMode: 'environment' } };
    
    navigator.mediaDevices.getUserMedia(constraints)
        .then(stream => {
            videoElement.srcObject = stream;
            videoElement.play();
            
            // Set up the overlay frame
            scannerOverlay.style.display = 'block';
            
            // Start decoding
            decodeContinuously();
            
            // Handle video errors
            videoElement.onerror = (error) => {
                console.error('Video error:', error);
                updateStatus('error', 'Fehler beim Abspielen der Kamera');
                stopScanning();
            };
        })
        .catch(error => {
            console.error('Camera access error:', error);
            if (error.name === 'NotAllowedError') {
                updateStatus('error', 'Kamera-Zugriff verweigert', 'Bitte erlauben Sie den Kamera-Zugriff.');
                showModal('camera-permission-modal');
            } else if (error.name === 'NotFoundError') {
                updateStatus('error', 'Keine Kamera gefunden', 'Keine Kamera an diesem Gerät gefunden.');
                showManualInput();
            } else {
                updateStatus('error', 'Fehler beim Zugriff auf Kamera', error.message);
                showManualInput();
            }
        });
}

// Decode continuously
function decodeContinuously() {
    if (!scannerActive) return;
    
    // Use ZXing to decode from video
    codeReader.decodeFromVideoDevice(null, scannerVideo, (result, error) => {
        if (result) {
            handleScanResult(result.getText());
            return;
        }
        
        if (error) {
            console.error('Scan error:', error);
            // Continue scanning even if there's an error
            if (scannerActive) {
                setTimeout(decodeContinuously, 500);
            }
        } else {
            // No result yet, continue scanning
            if (scannerActive) {
                setTimeout(decodeContinuously, 500);
            }
        }
    });
}

// Handle scan result
function handleScanResult(identifier) {
    if (!identifier) return;
    
    // Stop scanning
    stopScanning();
    
    // Trim the identifier
    identifier = identifier.trim();
    
    // Resolve the identifier via API
    resolveIdentifier(identifier);
}

// Resolve identifier via API
function resolveIdentifier(identifier) {
    updateStatus('scanning', 'Auflösen...', 'Identifier: ' + identifier);
    
    fetch('<?= route("api.scanner.resolve") ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-Token': '<?= csrf_token() ?>'
        },
        body: 'identifier=' + encodeURIComponent(identifier)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showResult(data);
        } else {
            updateStatus('error', 'Nicht gefunden', data.message || 'Item nicht gefunden');
            setTimeout(() => {
                resumeScanning();
            }, 2000);
        }
    })
    .catch(error => {
        console.error('API error:', error);
        updateStatus('error', 'Fehler beim Auflösen', error.message);
        setTimeout(() => {
            resumeScanning();
        }, 2000);
    });
}

// Show result
function showResult(data) {
    updateStatus('success', 'Erfolgreich gescannt!', data.type + ': ' + data.name);
    
    resultUrl = data.url;
    resultTitle.textContent = 'Erfolgreich gescannt!';
    resultMessage.textContent = data.name || data.internal_id;
    
    // Build details
    let detailsHtml = '<div class="result-detail"><span class="result-detail-label">Typ:</span><span class="result-detail-value">' + 
                      (data.type === 'device' ? 'Gerät' : 'Case') + '</span></div>';
    detailsHtml += '<div class="result-detail"><span class="result-detail-label">ID:</span><span class="result-detail-value">' + 
                   e(data.internal_id) + '</span></div>';
    
    resultDetails.innerHTML = detailsHtml;
    
    // Show result
    scannerArea.style.display = 'none';
    scannerResult.style.display = 'block';
}

// Redirect to result
function redirectToResult() {
    if (resultUrl) {
        window.location.href = resultUrl;
    }
}

// Stop scanning
function stopScanning() {
    scannerActive = false;
    
    if (codeReader) {
        try {
            codeReader.reset();
        } catch (error) {
            console.error('Error resetting code reader:', error);
        }
    }
    
    // Stop all video streams
    if (videoElement && videoElement.srcObject) {
        const stream = videoElement.srcObject;
        const tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
        videoElement.srcObject = null;
    }
}

// Resume scanning
function resumeScanning() {
    scannerArea.style.display = 'block';
    scannerResult.style.display = 'none';
    scannerActive = true;
    
    if (videoElement && videoElement.srcObject) {
        videoElement.play();
        decodeContinuously();
    } else {
        initScanner();
    }
}

// Toggle scanner mode
function toggleScanner() {
    if (scannerActive) {
        stopScanning();
        showManualInput();
        scannerActive = false;
    } else {
        hideManualInput();
        initScanner();
        scannerActive = true;
    }
}

// Show manual input
function showManualInput() {
    scannerPreview.style.display = 'none';
    scannerManual.style.display = 'block';
    toggleScannerBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/><line x1="9" y1="3" x2="9" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><line x1="15" y1="3" x2="15" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><line x1="9" y1="21" x2="9" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><line x1="15" y1="21" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><line x1="3" y1="9" x2="21" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><line x1="3" y1="15" x2="21" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Scanner';
    toggleScannerText.textContent = 'Scanner';
}

// Hide manual input
function hideManualInput() {
    scannerPreview.style.display = 'block';
    scannerManual.style.display = 'none';
    toggleScannerBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 5V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>Manuelle Eingabe';
    toggleScannerText.textContent = 'Manuelle Eingabe';
}

// Update status
function updateStatus(state, text, detail = '') {
    // Hide all icons
    Object.values(statusIcons).forEach(icon => {
        if (icon) icon.style.display = 'none';
    });
    
    // Show the active icon
    if (statusIcons[state]) {
        statusIcons[state].style.display = 'block';
    }
    
    // Update text
    statusText.textContent = text;
    statusDetail.textContent = detail;
}

// Show modal
function showModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

// Close modal
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Request camera permission
function requestCameraPermission() {
    closeModal('camera-permission-modal');
    initScanner();
}

// Show unsupported browser
function showUnsupportedBrowser() {
    updateStatus('error', 'Browser nicht unterstützt');
    showModal('unsupported-browser-modal');
}

// Go back
function goBack() {
    window.history.back();
}

// Handle manual form submission
manualForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const identifier = document.getElementById('manual-identifier').value.trim();
    if (identifier) {
        resolveIdentifier(identifier);
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on a mobile device
    const isMobile = /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
    
    if (isMobile) {
        // On mobile, prefer scanner by default
        initScanner();
    } else {
        // On desktop, show scanner but handle permission errors gracefully
        initScanner();
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        // Adjust video size
        if (videoElement) {
            const preview = document.getElementById('scanner-preview');
            const width = preview.clientWidth;
            const height = preview.clientHeight;
            videoElement.style.width = width + 'px';
            videoElement.style.height = height + 'px';
        }
    });
    
    // Trigger resize once
    window.dispatchEvent(new Event('resize'));
});

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    stopScanning();
});

// Escape HTML for display
function e(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
