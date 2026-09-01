<div class="error-page">
    <div class="error-container">
        <div class="error-code">403</div>
        <div class="error-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                <path d="M7 11V7C7 5.93913 7.43913 5 8.5 5H15.5C16.5609 5 17 5.93913 17 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h1 class="error-title">Zugriff verweigert</h1>
        <p class="error-message">
            <?= e($message ?? 'Sie haben keine Berechtigung, diese Seite zu betrachten.') ?>
        </p>
        <div class="error-actions">
            <a href="<?= route('dashboard') ?>" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Zurück zum Dashboard
            </a>
            <a href="<?= route('login') ?>" class="btn btn-ghost">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 3H9C8.46957 3 8 3.46957 8 4V6H16V4C16 3.46957 15.5304 3 15 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M15 13H9C8.46957 13 8 13.4696 8 14V20C8 20.5304 8.46957 21 9 21H15C15.5304 21 16 20.5304 16 20V14C16 13.4696 15.5304 13 15 13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 13V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M15 13V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Anmelden
            </a>
        </div>
    </div>
</div>
