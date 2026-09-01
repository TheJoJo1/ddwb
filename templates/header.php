<header class="header" role="banner">
    <div class="header-content">
        <!-- Logo -->
        <div class="logo">
            <a href="<?= route('dashboard') ?>" class="logo-link">
                <span class="logo-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7V17L12 22L22 17V7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 7L12 12L22 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 22V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="logo-text">
                    <span class="logo-title">DDWB</span>
                    <span class="logo-subtitle">DingeDieWirBesitzen</span>
                </span>
            </a>
        </div>

        <!-- Header Actions -->
        <div class="header-actions">
            <!-- Theme Toggle -->
            <button 
                type="button" 
                class="theme-toggle btn btn-icon btn-ghost" 
                aria-label="Theme wechseln"
                title="Theme wechseln"
                onclick="toggleTheme()"
            >
                <svg class="theme-icon-light" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 2V4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M12 20V22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M4.92999 4.93001L6.34001 6.34001" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M17.6599 17.66L19.07 19.07" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M19.07 6.34001L17.6599 7.75001" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M6.34001 19.07L4.92999 17.66" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M12 8V12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M12 16V12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <svg class="theme-icon-dark" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 3V4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M12 20V21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M4 12H3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M21 12H22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M18.364 18.364L19.778 19.778" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M4.22199 4.22201L5.63599 5.63601" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M19.778 5.63601L18.364 4.22201" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M5.63599 19.778L4.22199 18.364" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M12 8V12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M12 16V12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>

            <!-- Mobile Menu Toggle -->
            <button 
                type="button" 
                class="mobile-menu-toggle btn btn-icon btn-ghost md:hidden" 
                aria-label="Menü öffnen"
                title="Menü öffnen"
                onclick="toggleMobileMenu()"
            >
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3 6H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <!-- User Menu -->
            <?php if (auth()->isAuthenticated()): ?>
                <div class="user-menu">
                    <button 
                        type="button" 
                        class="user-menu-toggle btn btn-ghost" 
                        aria-label="Benutzermenü"
                        title="Benutzermenü"
                        onclick="toggleUserMenu()"
                    >
                        <span class="user-avatar">
                            <?= strtoupper(substr(auth()->getAuthenticatedUser()['name'] ?? 'U', 0, 1)) ?>
                        </span>
                        <span class="user-name md:hidden lg:inline">
                            <?= e(auth()->getAuthenticatedUser()['name'] ?? 'User') ?>
                        </span>
                        <svg class="user-menu-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    
                    <div class="user-menu-dropdown" id="user-menu-dropdown" role="menu">
                        <div class="user-menu-header">
                            <div class="user-info">
                                <span class="user-avatar-large">
                                    <?= strtoupper(substr(auth()->getAuthenticatedUser()['name'] ?? 'U', 0, 1)) ?>
                                </span>
                                <div class="user-details">
                                    <span class="user-fullname">
                                        <?= e(auth()->getAuthenticatedUser()['name'] ?? 'User') ?>
                                    </span>
                                    <span class="user-email">
                                        <?= e(auth()->getAuthenticatedUser()['email'] ?? '') ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="user-menu-divider"></div>
                        
                        <div class="user-menu-items">
                            <a href="<?= route('dashboard') ?>" class="user-menu-item" role="menuitem">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 10L12 2L21 10V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9 22V12H15V22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Dashboard
                            </a>
                            
                            <?php if (auth()->isAdmin()): ?>
                                <a href="<?= route('admin.users') ?>" class="user-menu-item" role="menuitem">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17 21V19C17 17.9391 16.7893 17.0391 16.4142 16.4142C16.0391 15.7893 15.5304 15.5304 15 15.5304H9C8.46957 15.5304 7.96086 15.7893 7.58579 16.4142C7.21071 17.0391 7 17.9391 7 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M7 11C7 10.4696 7.21071 10.0391 7.58579 9.78929C7.96086 9.53948 8.46957 9.46957 9 9.46957H15C15.5304 9.46957 16.0391 9.53948 16.4142 9.78929C16.7893 10.0391 17 10.4696 17 11V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M7 3C8.84315 3 10.4695 3.53043 11.4142 4.41421C12.3589 5.298 12.8586 6.49138 12.8586 7.85862C12.8586 9.22586 12.3589 10.4192 11.4142 11.303C10.4695 12.1868 8.84315 12.7174 7 12.7174V11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Benutzerverwaltung
                                </a>
                            <?php endif; ?>
                            
                            <a href="<?= route('scanner') ?>" class="user-menu-item" role="menuitem">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                                    <line x1="9" y1="3" x2="9" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <line x1="15" y1="3" x2="15" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <line x1="9" y1="21" x2="9" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <line x1="15" y1="21" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <line x1="3" y1="9" x2="21" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <line x1="3" y1="15" x2="21" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                Scanner
                            </a>
                            
                            <a href="<?= route('logout') ?>" class="user-menu-item user-menu-item-danger" role="menuitem" onclick="return confirm('Möchten Sie sich wirklich abmelden?')">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 21H5C4.46957 21 4 20.5304 4 20V4C4 3.46957 4.46957 3 5 3H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M16 17L21 12L16 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Abmelden
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>
