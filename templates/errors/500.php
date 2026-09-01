<div class="error-page">
    <div class="error-container">
        <div class="error-code">500</div>
        <div class="error-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L13.09 8.26L22 9L13.09 9.74L12 16L10.91 9.74L2 9L10.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 15L22 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 19L22 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h1 class="error-title">Interner Serverfehler</h1>
        <p class="error-message">
            <?= e($message ?? 'Es ist ein unerwarteter Fehler aufgetreten. Bitte versuchen Sie es später erneut.') ?>
        </p>
        
        <?php if ($debug ?? false): ?>
            <div class="error-debug">
                <h3>Debug-Informationen</h3>
                <p>Dies ist eine Debug-Ansicht, die nur in der Entwicklungsumgebung angezeigt wird.</p>
            </div>
        <?php endif; ?>
        
        <div class="error-actions">
            <a href="<?= route('dashboard') ?>" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Zurück zum Dashboard
            </a>
            <button type="button" class="btn btn-ghost" onclick="location.reload()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M23 4V4C22.1716 4 21.5 4.67157 21.1213 5.39071C20.7426 6.10984 20.6661 6.91301 20.8944 7.66026L21.8433 11.5687C21.9421 11.9588 21.8983 12.3734 21.7247 12.7435C21.5511 13.1136 21.2582 13.4355 20.8709 13.6633L17.8433 16.6909C17.5907 17.0435 17.2426 17.2835 16.8345 17.3582C16.4264 17.4329 15.9888 17.3456 15.6237 17.1056L12.8433 14.3251C12.6797 14.1615 12.4764 14.0742 12.2591 14.0742H8.25908C8.04184 14.0742 7.83853 14.1615 7.675 14.3251L4.89442 17.1056C4.52933 17.3456 4.09173 17.4329 3.68363 17.3582C3.27553 17.2835 2.92744 17.0435 2.67483 16.6909L2.1566 13.6633C1.76932 13.4355 1.47642 13.1136 1.30281 12.7435C1.1292 12.3734 1.08539 11.9588 1.18425 11.5687L2.13314 7.66026C2.36143 6.91301 2.28492 6.10984 1.90625 5.39071C1.52758 4.67157 0.855957 4 0 4V4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Seite neu laden
            </button>
        </div>
    </div>
</div>
