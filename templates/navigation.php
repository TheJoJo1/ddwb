<nav class="navigation" role="navigation" aria-label="Hauptnavigation">
    <div class="navigation-content">
        <ul class="navigation-list">
            <li class="navigation-item">
                <a 
                    href="<?= route('dashboard') ?>" 
                    class="navigation-link <?= is_active_route('/dashboard') ? 'active' : '' ?>"
                    title="Dashboard"
                >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 10L12 2L21 10V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 22V12H15V22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="navigation-item">
                <a 
                    href="<?= route('devices') ?>" 
                    class="navigation-link <?= is_active_route('/devices') ? 'active' : '' ?>"
                    title="Geräte"
                >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                        <line x1="8" y1="21" x2="16" y2="21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <line x1="12" y1="17" x2="12" y2="21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <line x1="12" y1="7" x2="12.01" y2="7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Geräte</span>
                </a>
            </li>

            <li class="navigation-item">
                <a 
                    href="<?= route('cases') ?>" 
                    class="navigation-link <?= is_active_route('/cases') ? 'active' : '' ?>"
                    title="Cases"
                >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 16V8C21 7.46957 20.7893 7.03914 20.4142 6.78929C20.0391 6.53948 19.5304 6.46957 19 6.46957H5C4.46957 6.46957 3.96086 6.53948 3.58579 6.78929C3.21071 7.03914 3 7.46957 3 8V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 16L7 20H17L21 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7 20V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17 20V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Cases</span>
                </a>
            </li>

            <li class="navigation-item">
                <a 
                    href="<?= route('rentals') ?>" 
                    class="navigation-link <?= is_active_route('/rentals') ? 'active' : '' ?>"
                    title="Ausleihe"
                >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 2H6C5.46957 2 5 2.46957 5 3V5C5 5.53043 5.46957 6 6 6H14C14.5304 6 15 5.53043 15 5V3C15 2.46957 14.5304 2 14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M20 10V21C20 21.5304 19.7893 22.0391 19.4142 22.4142C19.0391 22.7893 18.5304 23 18 23H6C5.46957 23 4.96086 22.7893 4.58579 22.4142C4.21071 22.0391 4 21.5304 4 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M20 10H4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 10V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Ausleihe</span>
                </a>
            </li>

            <li class="navigation-item">
                <a 
                    href="<?= route('maintenance') ?>" 
                    class="navigation-link <?= is_active_route('/maintenance') ? 'active' : '' ?>"
                    title="Wartung"
                >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.878 2.03003C10.878 2.03003 10.878 2.03003 10.878 2.03003L13.122 4.27403C15.966 7.11803 15.966 11.162 15.966 11.162C15.966 11.162 14.494 14.494 12 16C9.506 14.494 8.034 11.162 8.034 11.162C8.034 11.162 8.034 7.11803 10.878 2.03003Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Wartung</span>
                </a>
            </li>

            <li class="navigation-item">
                <a 
                    href="<?= route('packlists') ?>" 
                    class="navigation-link <?= is_active_route('/packlists') ? 'active' : '' ?>"
                    title="Packlisten"
                >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 2H6C5.46957 2 5 2.46957 5 3V5C5 5.53043 5.46957 6 6 6H14C14.5304 6 15 5.53043 15 5V3C15 2.46957 14.5304 2 14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 10H6C5.46957 10 5 10.4696 5 11V19C5 19.5304 5.46957 20 6 20H14C14.5304 20 15 19.5304 15 19V11C15 10.4696 14.5304 10 14 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M20 10H18C17.4696 10 17 10.4696 17 11V13C17 13.5304 17.4696 14 18 14H20C20.5304 14 21 13.5304 21 13V11C21 10.4696 20.5304 10 20 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Packlisten</span>
                </a>
            </li>

            <li class="navigation-item">
                <a 
                    href="<?= route('labels') ?>" 
                    class="navigation-link <?= is_active_route('/labels') ? 'active' : '' ?>"
                    title="Labels"
                >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.5 2H6C5.46957 2 5 2.46957 5 3V21C5 21.5304 5.46957 22 6 22H18C18.5304 22 19 21.5304 19 21V7.5L14.5 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14.5 2V7.5H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 13H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 17H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10 9H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Labels</span>
                </a>
            </li>

            <?php if (auth()->isAuthenticated() && auth()->isAdmin()): ?>
                <li class="navigation-item">
                    <a 
                        href="<?= route('logs') ?>" 
                        class="navigation-link <?= is_active_route('/logs') ? 'active' : '' ?>"
                        title="Protokoll"
                    >
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.5 2H6C5.46957 2 5 2.46957 5 3V21C5 21.5304 5.46957 22 6 22H18C18.5304 22 19 21.5304 19 21V7.5L14.5 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14.5 2V7.5H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 13H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 17H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M10 9H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Protokoll</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
