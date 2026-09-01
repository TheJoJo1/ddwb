<footer class="footer" role="contentinfo">
    <div class="footer-content">
        <div class="footer-left">
            <p>&copy; <?= date('Y') ?> <strong>DingeDieWirBesitzen (DDWB)</strong></p>
            <p>Version 1.0.0</p>
        </div>
        
        <div class="footer-center">
            <p>
                <a href="<?= route('dashboard') ?>">Dashboard</a>
                <span class="footer-separator">|</span>
                <a href="<?= route('devices') ?>">Geräte</a>
                <span class="footer-separator">|</span>
                <a href="<?= route('cases') ?>">Cases</a>
                <span class="footer-separator">|</span>
                <a href="<?= route('rentals') ?>">Ausleihe</a>
            </p>
        </div>
        
        <div class="footer-right">
            <p>
                <a href="#" onclick="toggleTheme(); return false;">Theme wechseln</a>
                <span class="footer-separator">|</span>
                <a href="https://github.com/TheJoJo1/ddwb" target="_blank" rel="noopener noreferrer">GitHub</a>
            </p>
        </div>
    </div>
</footer>
