/**
 * ZXing Library Wrapper for DDWB
 * 
 * This file provides a fallback mechanism to load ZXing from CDN
 * if the local library is not available.
 * 
 * The actual ZXing library will be loaded from CDN in the layout.
 * This file exists to maintain the reference in the scanner template.
 */

// ZXing Browser QRCodeReader polyfill
// This will be loaded from CDN in the actual layout template

if (typeof ZXing === 'undefined') {
    window.ZXing = window.ZXing || {};
    window.ZXing.BrowserQRCodeReader = window.ZXing.BrowserQRCodeReader || function() {
        console.error('ZXing library not loaded. Please ensure it is loaded from CDN in the layout template.');
    };
}
