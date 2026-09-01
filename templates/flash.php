<?php

/**
 * Flash Messages
 * 
 * Displays flash messages from session
 */

$session = session();

// Get flash messages
$success = $session->getFlash('success');
$error = $session->getFlash('error');
$errors = $session->getFlash('errors', []);
$warning = $session->getFlash('warning');
$info = $session->getFlash('info');

// Check if we're on a mobile scanner page
$isScanner = str_contains($_SERVER['REQUEST_URI'] ?? '', '/scanner');

?>

<?php if ($success !== null): ?>
    <div class="alert alert-success alert-dismissible <?= $isScanner ? 'scanner-alert' : '' ?>" role="alert">
        <svg class="alert-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M22 11.08V12C22 16.4771 20.4771 19 16.5 19H7.5C3.52285 19 2 16.4771 2 12C2 11.08 2.75 10.5 3.75 10.5H5.25C6.25 10.5 7 11.25 7 12.5C7 13.75 6.25 14.5 5.25 14.5H3.75C2.75 14.5 2 13.75 2 12.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M22 4L12 14.01L2 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="alert-message"><?= e($success) ?></span>
        <button type="button" class="alert-close btn btn-icon btn-ghost" onclick="this.parentElement.style.display='none'" aria-label="Schließen">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
<?php endif; ?>

<?php if ($error !== null): ?>
    <div class="alert alert-error alert-dismissible <?= $isScanner ? 'scanner-alert' : '' ?>" role="alert">
        <svg class="alert-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
            <line x1="15" y1="9" x2="9" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <line x1="9" y1="9" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="alert-message"><?= e($error) ?></span>
        <button type="button" class="alert-close btn btn-icon btn-ghost" onclick="this.parentElement.style.display='none'" aria-label="Schließen">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
<?php endif; ?>

<?php if ($warning !== null): ?>
    <div class="alert alert-warning alert-dismissible <?= $isScanner ? 'scanner-alert' : '' ?>" role="alert">
        <svg class="alert-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10.878 2.03003C10.878 2.03003 10.878 2.03003 10.878 2.03003L13.122 4.27403C15.966 7.11803 15.966 11.162 15.966 11.162C15.966 11.162 14.494 14.494 12 16C9.506 14.494 8.034 11.162 8.034 11.162C8.034 11.162 8.034 7.11803 10.878 2.03003Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="alert-message"><?= e($warning) ?></span>
        <button type="button" class="alert-close btn btn-icon btn-ghost" onclick="this.parentElement.style.display='none'" aria-label="Schließen">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
<?php endif; ?>

<?php if ($info !== null): ?>
    <div class="alert alert-info alert-dismissible <?= $isScanner ? 'scanner-alert' : '' ?>" role="alert">
        <svg class="alert-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
            <line x1="12" y1="16" x2="12" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <line x1="12" y1="8" x2="12.01" y2="8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="alert-message"><?= e($info) ?></span>
        <button type="button" class="alert-close btn btn-icon btn-ghost" onclick="this.parentElement.style.display='none'" aria-label="Schließen">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
<?php endif; ?>

<?php if (!empty($errors) && is_array($errors)): ?>
    <div class="alert alert-error alert-dismissible <?= $isScanner ? 'scanner-alert' : '' ?>" role="alert">
        <svg class="alert-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
            <line x1="15" y1="9" x2="9" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <line x1="9" y1="9" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <div class="alert-message">
            <ul class="error-list">
                <?php foreach ($errors as $field => $message): ?>
                    <li><?= e(is_array($message) ? implode(', ', $message) : $message) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <button type="button" class="alert-close btn btn-icon btn-ghost" onclick="this.parentElement.style.display='none'" aria-label="Schließen">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
<?php endif; ?>
