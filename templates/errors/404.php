<div class="error-page">
    <div class="error-container">
        <div class="error-code">404</div>
        <div class="error-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                <path d="M15 9L9 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9 9L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h1 class="error-title">Seite nicht gefunden</h1>
        <p class="error-message">
            <?= e($message ?? 'Die Seite, die Sie suchen, konnte nicht gefunden werden.') ?>
        </p>
        <div class="error-actions">
            <a href="<?= route('dashboard') ?>" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Zurück zum Dashboard
            </a>
            <a href="<?= route('scanner') ?>" class="btn btn-ghost">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                    <line x1="9" y1="3" x2="9" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="15" y1="3" x2="15" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="9" y1="21" x2="9" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="15" y1="21" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="3" y1="9" x2="21" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="3" y1="15" x2="21" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Scanner öffnen
            </a>
        </div>
    </div>
</div>
