/**
 * DDWB - Main JavaScript
 * 
 * Core JavaScript functionality for the application
 */

// ============================================
// Theme Management
// ============================================

/**
 * Toggle between light and dark theme
 */
function toggleTheme() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    // Set new theme
    html.setAttribute('data-theme', newTheme);
    
    // Save to localStorage
    localStorage.setItem('ddwb-theme', newTheme);
    
    // Update theme toggle button
    updateThemeToggle();
}

/**
 * Update theme toggle button icon
 */
function updateThemeToggle() {
    const theme = document.documentElement.getAttribute('data-theme');
    const lightIcon = document.querySelector('.theme-icon-light');
    const darkIcon = document.querySelector('.theme-icon-dark');
    
    if (theme === 'dark') {
        if (lightIcon) lightIcon.style.display = 'none';
        if (darkIcon) darkIcon.style.display = 'block';
    } else {
        if (lightIcon) lightIcon.style.display = 'block';
        if (darkIcon) darkIcon.style.display = 'none';
    }
}

// ============================================
// Mobile Menu
// ============================================

/**
 * Toggle mobile menu
 */
function toggleMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('open');
    }
}

/**
 * Close mobile menu
 */
function closeMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.remove('open');
    }
}

// ============================================
// User Menu
// ============================================

/**
 * Toggle user menu dropdown
 */
function toggleUserMenu() {
    const dropdown = document.getElementById('user-menu-dropdown');
    if (dropdown) {
        dropdown.classList.toggle('show');
    }
}

/**
 * Close user menu when clicking outside
 */
document.addEventListener('click', function(event) {
    const userMenu = document.querySelector('.user-menu');
    const dropdown = document.getElementById('user-menu-dropdown');
    
    if (userMenu && dropdown && !userMenu.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});

// ============================================
// Modal Management
// ============================================

/**
 * Show a modal
 * @param {string} modalId - The modal ID
 */
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    const overlay = document.querySelector('.modal-overlay');
    
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    if (overlay) {
        overlay.classList.add('show');
    }
}

/**
 * Close a modal
 * @param {string} modalId - The modal ID
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    const overlay = document.querySelector('.modal-overlay');
    
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    if (overlay) {
        overlay.classList.remove('show');
    }
}

/**
 * Close modal when clicking overlay
 */
document.addEventListener('click', function(event) {
    const overlay = document.querySelector('.modal-overlay');
    if (overlay && overlay.contains(event.target)) {
        closeAllModals();
    }
});

/**
 * Close all modals
 */
function closeAllModals() {
    document.querySelectorAll('.modal').forEach(modal => {
        modal.classList.remove('show');
    });
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.classList.remove('show');
    });
    document.body.style.overflow = '';
}

/**
 * Close modal on escape key
 */
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeAllModals();
    }
});

// ============================================
// Confirmation Dialogs
// ============================================

/**
 * Show a confirmation dialog
 * @param {string} title - The dialog title
 * @param {string} message - The confirmation message
 * @param {function} onConfirm - Callback when confirmed
 * @param {function} onCancel - Callback when cancelled
 * @param {string} confirmText - Confirm button text (default: 'Bestätigen')
 * @param {string} cancelText - Cancel button text (default: 'Abbrechen')
 */
function showConfirmation(title, message, onConfirm, onCancel, confirmText = 'Bestätigen', cancelText = 'Abbrechen') {
    // Create modal if it doesn't exist
    let modal = document.getElementById('confirmation-modal');
    
    if (!modal) {
        modal = createConfirmationModal();
    }
    
    // Update modal content
    const modalTitle = modal.querySelector('.confirmation-title');
    const modalMessage = modal.querySelector('.confirmation-message');
    const modalConfirmBtn = modal.querySelector('.confirmation-confirm');
    const modalCancelBtn = modal.querySelector('.confirmation-cancel');
    
    if (modalTitle) modalTitle.textContent = title;
    if (modalMessage) modalMessage.textContent = message;
    if (modalConfirmBtn) modalConfirmBtn.textContent = confirmText;
    if (modalCancelBtn) modalCancelBtn.textContent = cancelText;
    
    // Set up event listeners
    const confirmHandler = function() {
        closeModal('confirmation-modal');
        if (onConfirm) onConfirm();
        // Remove event listener
        modalConfirmBtn.removeEventListener('click', confirmHandler);
    };
    
    const cancelHandler = function() {
        closeModal('confirmation-modal');
        if (onCancel) onCancel();
        // Remove event listener
        modalCancelBtn.removeEventListener('click', cancelHandler);
    };
    
    // Remove existing listeners
    modalConfirmBtn.replaceWith(modalConfirmBtn.cloneNode(true));
    modalCancelBtn.replaceWith(modalCancelBtn.cloneNode(true));
    
    // Add new listeners
    modal.querySelector('.confirmation-confirm').addEventListener('click', confirmHandler);
    modal.querySelector('.confirmation-cancel').addEventListener('click', cancelHandler);
    
    // Show modal
    showModal('confirmation-modal');
}

/**
 * Create confirmation modal
 * @returns {HTMLElement} The modal element
 */
function createConfirmationModal() {
    const modal = document.createElement('div');
    modal.id = 'confirmation-modal';
    modal.className = 'modal confirmation-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="confirmation-title">Bestätigung erforderlich</h2>
                <button type="button" class="modal-close" onclick="closeModal('confirmation-modal')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="confirmation-icon">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 8V12L16 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <p class="confirmation-message"></p>
            </div>
            <div class="modal-footer modal-footer-between">
                <button type="button" class="btn btn-ghost confirmation-cancel">Abbrechen</button>
                <button type="button" class="btn btn-danger confirmation-confirm">Bestätigen</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay';
    overlay.onclick = function() {
        closeModal('confirmation-modal');
    };
    document.body.appendChild(overlay);
    
    return modal;
}

// ============================================
// Toast Notifications
// ============================================

/**
 * Show a toast notification
 * @param {string} type - Toast type: 'success', 'error', 'warning', 'info'
 * @param {string} message - The notification message
 * @param {number} duration - Duration in milliseconds (default: 5000)
 */
function showToast(type, message, duration = 5000) {
    // Create toast container if it doesn't exist
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        container.style.cssText = `
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            pointer-events: none;
        `;
        document.body.appendChild(container);
    }
    
    // Create toast
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.cssText = `
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: var(--bg-primary);
        border: 1px solid var(--border-primary);
        border-radius: 0.5rem;
        box-shadow: var(--shadow-lg);
        font-size: 0.875rem;
        color: var(--text-primary);
        animation: slideInRight 0.3s ease;
        pointer-events: auto;
        min-width: 280px;
        max-width: 400px;
    `;
    
    // Add icon
    const icons = {
        success: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M22 11.08V12C22 16.4771 20.4771 19 16.5 19H7.5C3.52285 19 2 16.4771 2 12C2 11.08 2.75 10.5 3.75 10.5H5.25C6.25 10.5 7 11.25 7 12.5C7 13.75 6.25 14.5 5.25 14.5H3.75C2.75 14.5 2 13.75 2 12.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M22 4L12 14.01L2 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        error: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><line x1="15" y1="9" x2="9" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><line x1="9" y1="9" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        warning: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.878 2.03003C10.878 2.03003 10.878 2.03003 10.878 2.03003L13.122 4.27403C15.966 7.11803 15.966 11.162 15.966 11.162C15.966 11.162 14.494 14.494 12 16C9.506 14.494 8.034 11.162 8.034 11.162C8.034 11.162 8.034 7.11803 10.878 2.03003Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2"/></svg>',
        info: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><line x1="12" y1="16" x2="12" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><line x1="12" y1="8" x2="12.01" y2="8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
    };
    
    const iconContainer = document.createElement('span');
    iconContainer.innerHTML = icons[type] || icons.info;
    iconContainer.style.flexShrink = '0';
    
    // Set toast color based on type
    const colors = {
        success: 'var(--color-success)',
        error: 'var(--color-error)',
        warning: 'var(--color-warning)',
        info: 'var(--color-info)'
    };
    
    toast.style.borderLeft = `4px solid ${colors[type] || colors.info}`;
    
    // Add message
    const messageSpan = document.createElement('span');
    messageSpan.textContent = message;
    messageSpan.style.flex = '1';
    
    // Add to toast
    toast.appendChild(iconContainer);
    toast.appendChild(messageSpan);
    
    // Add to container
    container.appendChild(toast);
    
    // Auto remove
    setTimeout(() => {
        toast.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => {
            toast.remove();
            // Remove container if empty
            if (container.children.length === 0) {
                container.remove();
            }
        }, 300);
    }, duration);
    
    return toast;
}

// Add toast animation styles
const toastStyles = document.createElement('style');
toastStyles.textContent = `
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
`;
document.head.appendChild(toastStyles);

// ============================================
// Form Helpers
// ============================================

/**
 * Clear form errors
 * @param {string|HTMLElement} form - Form ID or element
 */
function clearFormErrors(form) {
    let formElement;
    if (typeof form === 'string') {
        formElement = document.getElementById(form);
    } else {
        formElement = form;
    }
    
    if (!formElement) return;
    
    // Remove error classes
    formElement.querySelectorAll('.form-input-error, .form-select-error, .form-textarea-error').forEach(el => {
        el.classList.remove('form-input-error', 'form-select-error', 'form-textarea-error');
    });
    
    // Remove error messages
    formElement.querySelectorAll('.form-error').forEach(el => {
        el.textContent = '';
    });
}

/**
 * Show form errors
 * @param {string|HTMLElement} form - Form ID or element
 * @param {Object} errors - Error object with field names as keys
 */
function showFormErrors(form, errors) {
    clearFormErrors(form);
    
    let formElement;
    if (typeof form === 'string') {
        formElement = document.getElementById(form);
    } else {
        formElement = form;
    }
    
    if (!formElement || !errors) return;
    
    // Show errors for each field
    for (const [field, message] of Object.entries(errors)) {
        const input = formElement.querySelector(`[name="${field}"], #${field}`);
        if (input) {
            input.classList.add('form-input-error');
            
            // Find or create error element
            let errorEl = formElement.querySelector(`.form-error[data-field="${field}"]`);
            if (!errorEl) {
                errorEl = document.createElement('span');
                errorEl.className = 'form-error';
                errorEl.setAttribute('data-field', field);
                
                // Insert after the input
                if (input.nextSibling) {
                    input.parentNode.insertBefore(errorEl, input.nextSibling);
                } else {
                    input.parentNode.appendChild(errorEl);
                }
            }
            
            errorEl.textContent = message;
        }
    }
}

/**
 * Get form data as object
 * @param {string|HTMLElement} form - Form ID or element
 * @returns {Object} Form data
 */
function getFormData(form) {
    let formElement;
    if (typeof form === 'string') {
        formElement = document.getElementById(form);
    } else {
        formElement = form;
    }
    
    if (!formElement) return {};
    
    const formData = new FormData(formElement);
    const data = {};
    
    for (const [key, value] of formData.entries()) {
        // Handle checkboxes (unchecked checkboxes are not included in FormData)
        const checkbox = formElement.querySelector(`input[name="${key}"][type="checkbox"]`);
        if (checkbox) {
            data[key] = checkbox.checked;
        } else {
            // Handle multiple values (e.g., select multiple)
            if (data[key]) {
                if (Array.isArray(data[key])) {
                    data[key].push(value);
                } else {
                    data[key] = [data[key], value];
                }
            } else {
                data[key] = value;
            }
        }
    }
    
    return data;
}

/**
 * Set form data from object
 * @param {string|HTMLElement} form - Form ID or element
 * @param {Object} data - Data to set
 */
function setFormData(form, data) {
    let formElement;
    if (typeof form === 'string') {
        formElement = document.getElementById(form);
    } else {
        formElement = form;
    }
    
    if (!formElement || !data) return;
    
    for (const [key, value] of Object.entries(data)) {
        const input = formElement.querySelector(`[name="${key}"], #${key}`);
        if (input) {
            if (input.type === 'checkbox' || input.type === 'radio') {
                input.checked = input.value == value;
            } else {
                input.value = value;
            }
        }
    }
}

/**
 * Reset form
 * @param {string|HTMLElement} form - Form ID or element
 */
function resetForm(form) {
    let formElement;
    if (typeof form === 'string') {
        formElement = document.getElementById(form);
    } else {
        formElement = form;
    }
    
    if (formElement) {
        formElement.reset();
        clearFormErrors(formElement);
    }
}

// ============================================
// AJAX Helpers
// ============================================

/**
 * Make an AJAX request
 * @param {string} url - Request URL
 * @param {Object} options - Request options
 * @returns {Promise} Promise with response
 */
function ajax(url, options = {}) {
    const defaults = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: null,
        credentials: 'include'
    };
    
    const opts = { ...defaults, ...options };
    
    // Add CSRF token if available
    const csrfToken = document.querySelector('meta[name="csrf_token"]')?.content ||
                     document.querySelector('input[name="csrf_token"]')?.value;
    if (csrfToken) {
        opts.headers['X-CSRF-Token'] = csrfToken;
    }
    
    // Handle JSON data
    if (opts.data && typeof opts.data === 'object') {
        if (!opts.headers['Content-Type'].includes('json')) {
            opts.headers['Content-Type'] = 'application/x-www-form-urlencoded';
            opts.body = new URLSearchParams(opts.data).toString();
        } else {
            opts.body = JSON.stringify(opts.data);
        }
        delete opts.data;
    }
    
    return fetch(url, opts)
        .then(response => {
            // Handle JSON response
            const contentType = response.headers.get('Content-Type');
            if (contentType && contentType.includes('application/json')) {
                return response.json().then(data => {
                    if (!response.ok) {
                        return Promise.reject({ response, data });
                    }
                    return { response, data };
                });
            }
            
            // Handle text response
            return response.text().then(text => {
                if (!response.ok) {
                    return Promise.reject({ response, data: text });
                }
                return { response, data: text };
            });
        });
}

/**
 * GET request
 * @param {string} url - Request URL
 * @param {Object} params - Query parameters
 * @returns {Promise} Promise with response
 */
function get(url, params = {}) {
    const query = new URLSearchParams(params).toString();
    const fullUrl = query ? `${url}?${query}` : url;
    return ajax(fullUrl);
}

/**
 * POST request
 * @param {string} url - Request URL
 * @param {Object} data - Request data
 * @returns {Promise} Promise with response
 */
function post(url, data = {}) {
    return ajax(url, {
        method: 'POST',
        data: data
    });
}

/**
 * PUT request
 * @param {string} url - Request URL
 * @param {Object} data - Request data
 * @returns {Promise} Promise with response
 */
function put(url, data = {}) {
    return ajax(url, {
        method: 'PUT',
        data: data
    });
}

/**
 * DELETE request
 * @param {string} url - Request URL
 * @param {Object} data - Request data
 * @returns {Promise} Promise with response
 */
function del(url, data = {}) {
    return ajax(url, {
        method: 'DELETE',
        data: data
    });
}

// ============================================
// URL Helpers
// ============================================

/**
 * Get current URL
 * @returns {string} Current URL
 */
function getCurrentUrl() {
    return window.location.href;
}

/**
 * Get query parameter
 * @param {string} name - Parameter name
 * @returns {string|null} Parameter value or null
 */
function getQueryParam(name) {
    const params = new URLSearchParams(window.location.search);
    return params.get(name);
}

/**
 * Set query parameter
 * @param {string} name - Parameter name
 * @param {string} value - Parameter value
 */
function setQueryParam(name, value) {
    const url = new URL(window.location.href);
    url.searchParams.set(name, value);
    window.history.pushState({}, '', url.toString());
}

/**
 * Remove query parameter
 * @param {string} name - Parameter name
 */
function removeQueryParam(name) {
    const url = new URL(window.location.href);
    url.searchParams.delete(name);
    window.history.pushState({}, '', url.toString());
}

// ============================================
// Event Delegation
// ============================================

/**
 * Delegate event to parent element
 * @param {HTMLElement} parent - Parent element
 * @param {string} selector - Child selector
 * @param {string} eventType - Event type
 * @param {Function} handler - Event handler
 */
function delegateEvent(parent, selector, eventType, handler) {
    parent.addEventListener(eventType, function(event) {
        const target = event.target.closest(selector);
        if (target) {
            handler.call(target, event);
        }
    });
}

// ============================================
// Debounce Function
// ============================================

/**
 * Debounce a function
 * @param {Function} func - Function to debounce
 * @param {number} wait - Wait time in milliseconds
 * @returns {Function} Debounced function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ============================================
// Throttle Function
// ============================================

/**
 * Throttle a function
 * @param {Function} func - Function to throttle
 * @param {number} limit - Limit time in milliseconds
 * @returns {Function} Throttled function
 */
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// ============================================
// Format Helpers
// ============================================

/**
 * Format date
 * @param {string|Date} date - Date to format
 * @param {string} format - Format string (default: 'YYYY-MM-DD HH:mm:ss')
 * @returns {string} Formatted date
 */
function formatDate(date, format = 'YYYY-MM-DD HH:mm:ss') {
    if (!date) return '';
    
    const d = new Date(date);
    if (isNaN(d.getTime())) return date;
    
    const pad = (num) => num.toString().padStart(2, '0');
    
    return format
        .replace('YYYY', d.getFullYear())
        .replace('MM', pad(d.getMonth() + 1))
        .replace('DD', pad(d.getDate()))
        .replace('HH', pad(d.getHours()))
        .replace('mm', pad(d.getMinutes()))
        .replace('ss', pad(d.getSeconds()));
}

/**
 * Format number
 * @param {number} num - Number to format
 * @param {number} decimals - Decimal places (default: 2)
 * @returns {string} Formatted number
 */
function formatNumber(num, decimals = 2) {
    if (num === null || num === undefined) return '';
    return Number(num).toLocaleString('de-DE', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

/**
 * Format bytes
 * @param {number} bytes - Bytes to format
 * @param {number} decimals - Decimal places (default: 2)
 * @returns {string} Formatted bytes
 */
function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(decimals)) + ' ' + sizes[i];
}

/**
 * Truncate text
 * @param {string} text - Text to truncate
 * @param {number} length - Maximum length
 * @param {string} suffix - Suffix (default: '...')
 * @returns {string} Truncated text
 */
function truncate(text, length, suffix = '...') {
    if (!text) return '';
    if (text.length <= length) return text;
    return text.substring(0, length) + suffix;
}

/**
 * Escape HTML
 * @param {string} text - Text to escape
 * @returns {string} Escaped text
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ============================================
// DOM Helpers
// ============================================

/**
 * Create element
 * @param {string} tag - Tag name
 * @param {Object} options - Element options
 * @returns {HTMLElement} Created element
 */
function createElement(tag, options = {}) {
    const el = document.createElement(tag);
    
    if (options.className) {
        el.className = options.className;
    }
    if (options.id) {
        el.id = options.id;
    }
    if (options.text) {
        el.textContent = options.text;
    }
    if (options.html) {
        el.innerHTML = options.html;
    }
    if (options.style) {
        Object.assign(el.style, options.style);
    }
    if (options.attributes) {
        for (const [key, value] of Object.entries(options.attributes)) {
            el.setAttribute(key, value);
        }
    }
    if (options.events) {
        for (const [event, handler] of Object.entries(options.events)) {
            el.addEventListener(event, handler);
        }
    }
    
    return el;
}

/**
 * Show element
 * @param {HTMLElement} el - Element to show
 */
function show(el) {
    if (el) {
        el.style.display = '';
    }
}

/**
 * Hide element
 * @param {HTMLElement} el - Element to hide
 */
function hide(el) {
    if (el) {
        el.style.display = 'none';
    }
}

/**
 * Toggle element visibility
 * @param {HTMLElement} el - Element to toggle
 */
function toggle(el) {
    if (el) {
        el.style.display = el.style.display === 'none' ? '' : 'none';
    }
}

/**
 * Check if element is visible
 * @param {HTMLElement} el - Element to check
 * @returns {boolean} True if visible
 */
function isVisible(el) {
    if (!el) return false;
    return el.style.display !== 'none' && 
           el.offsetWidth > 0 && 
           el.offsetHeight > 0;
}

// ============================================
// Cookie Management
// ============================================

/**
 * Get cookie
 * @param {string} name - Cookie name
 * @returns {string|null} Cookie value or null
 */
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

/**
 * Set cookie
 * @param {string} name - Cookie name
 * @param {string} value - Cookie value
 * @param {number} days - Expiration days
 * @param {string} path - Cookie path (default: '/')
 */
function setCookie(name, value, days = 7, path = '/') {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = `expires=${date.toUTCString()}`;
    document.cookie = `${name}=${value};${expires};path=${path};SameSite=Lax`;
}

/**
 * Delete cookie
 * @param {string} name - Cookie name
 * @param {string} path - Cookie path (default: '/')
 */
function deleteCookie(name, path = '/') {
    document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=${path};`;
}

// ============================================
// Local Storage Helpers
// ============================================

/**
 * Get from localStorage
 * @param {string} key - Storage key
 * @param {*} defaultValue - Default value
 * @returns {*} Stored value or default
 */
function getLocalStorage(key, defaultValue = null) {
    try {
        const item = localStorage.getItem(key);
        return item ? JSON.parse(item) : defaultValue;
    } catch (error) {
        console.error('Error reading from localStorage:', error);
        return defaultValue;
    }
}

/**
 * Set to localStorage
 * @param {string} key - Storage key
 * @param {*} value - Value to store
 */
function setLocalStorage(key, value) {
    try {
        localStorage.setItem(key, JSON.stringify(value));
    } catch (error) {
        console.error('Error writing to localStorage:', error);
    }
}

/**
 * Remove from localStorage
 * @param {string} key - Storage key
 */
function removeLocalStorage(key) {
    try {
        localStorage.removeItem(key);
    } catch (error) {
        console.error('Error removing from localStorage:', error);
    }
}

// ============================================
// Initialize
// ============================================

// Initialize theme on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize theme toggle
    updateThemeToggle();
    
    // Initialize mobile menu
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', toggleMobileMenu);
    }
    
    // Close mobile menu on window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            closeMobileMenu();
        }
    });
    
    // Initialize form submissions
    document.querySelectorAll('form').forEach(form => {
        // Add submit handler for AJAX forms
        if (form.classList.contains('ajax-form')) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const action = form.getAttribute('action') || window.location.href;
                const method = form.getAttribute('method') || 'POST';
                const data = getFormData(form);
                
                // Show loading state
                const submitBtn = form.querySelector('[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<svg class="animate-spin" width="18" height="18" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round"/></svg> Bitte warten...';
                }
                
                // Make request
                ajax(action, {
                    method: method,
                    data: data
                })
                .then(({ response, data }) => {
                    if (data.success && data.redirect) {
                        window.location.href = data.redirect;
                    } else if (data.success) {
                        // Handle success
                        if (data.message) {
                            showToast('success', data.message);
                        }
                        if (data.reload) {
                            window.location.reload();
                        }
                    } else {
                        // Handle errors
                        if (data.errors) {
                            showFormErrors(form, data.errors);
                        } else if (data.message) {
                            showToast('error', data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('AJAX error:', error);
                    showToast('error', 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.');
                })
                .finally(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = submitBtn.getAttribute('data-original-text') || 'Absenden';
                    }
                });
            });
        }
    });
    
    // Store original submit button text
    document.querySelectorAll('form [type="submit"]').forEach(btn => {
        btn.setAttribute('data-original-text', btn.textContent);
    });
    
    // Initialize delete buttons
    document.querySelectorAll('.btn-delete, [data-delete]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            const form = this.closest('form');
            const action = form ? form.getAttribute('action') : href;
            const method = form ? form.getAttribute('method') || 'POST' : 'POST';
            const data = form ? getFormData(form) : {};
            
            e.preventDefault();
            
            // Get confirmation message
            const message = this.getAttribute('data-confirm') || 
                          this.getAttribute('title') || 
                          'Sind Sie sicher, dass Sie diesen Eintrag löschen möchten?';
            const title = this.getAttribute('data-confirm-title') || 'Löschen bestätigen';
            
            showConfirmation(title, message, function() {
                if (form) {
                    form.submit();
                } else if (href) {
                    // Make DELETE request
                    del(action, data)
                        .then(({ response, data }) => {
                            if (data.success) {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else if (data.reload) {
                                    window.location.reload();
                                } else {
                                    showToast('success', data.message || 'Eintrag erfolgreich gelöscht');
                                }
                            } else {
                                showToast('error', data.message || 'Fehler beim Löschen');
                            }
                        })
                        .catch(error => {
                            showToast('error', 'Ein Fehler ist aufgetreten');
                        });
                }
            });
        });
    });
});

// Add spinner animation style
const spinnerStyle = document.createElement('style');
spinnerStyle.textContent = `
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    .animate-spin {
        animation: spin 1s linear infinite;
    }
`;
document.head.appendChild(spinnerStyle);

// Export functions to global scope
window.DDWB = {
    toggleTheme,
    showModal,
    closeModal,
    showToast,
    showConfirmation,
    getFormData,
    setFormData,
    resetForm,
    clearFormErrors,
    showFormErrors,
    get,
    post,
    put,
    del,
    ajax,
    getQueryParam,
    setQueryParam,
    removeQueryParam,
    formatDate,
    formatNumber,
    formatBytes,
    truncate,
    escapeHtml,
    createElement,
    show,
    hide,
    toggle,
    isVisible,
    getCookie,
    setCookie,
    deleteCookie,
    getLocalStorage,
    setLocalStorage,
    removeLocalStorage,
    debounce,
    throttle
};
