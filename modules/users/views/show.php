<div class="users-show">
    <div class="page-header">
        <h1 class="page-title">Benutzer: <?= e($user['name']) ?></h1>
        
        <div class="page-actions">
            <a href="<?= route('admin.users.edit', ['id' => $user['id']]) ?>" class="btn btn-ghost">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11 4H4C3.46957 4 3 4.46957 3 5V19C3 19.5304 3.46957 20 4 20H11C11.5304 20 12 19.5304 12 19V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18 13L23 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M23 8V16C23 16.5304 22.7893 17.0391 22.4142 17.4142C22.0391 17.7893 21.5304 18 21 18H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18 13L16 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18 13L20 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Bearbeiten
            </a>
            
            <a href="<?= route('admin.users') ?>" class="btn btn-ghost">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Zurück
            </a>
        </div>
    </div>

    <div class="users-show-grid">
        <!-- User Details Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Benutzerdetails</h2>
            </div>
            
            <div class="card-body">
                <div class="user-details">
                    <div class="user-avatar-large">
                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    </div>
                    
                    <div class="user-info">
                        <div class="user-info-item">
                            <span class="user-info-label">Name:</span>
                            <span class="user-info-value"><?= e($user['name']) ?></span>
                        </div>
                        
                        <div class="user-info-item">
                            <span class="user-info-label">E-Mail:</span>
                            <span class="user-info-value"><?= e($user['email']) ?></span>
                        </div>
                        
                        <div class="user-info-item">
                            <span class="user-info-label">Rolle:</span>
                            <span class="user-info-value">
                                <span class="badge badge-<?= $user['role'] === 'admin' ? 'error' : 'primary' ?>">
                                    <?= e(ucfirst($user['role'])) ?>
                                </span>
                            </span>
                        </div>
                        
                        <div class="user-info-item">
                            <span class="user-info-label">Status:</span>
                            <span class="user-info-value">
                                <span class="badge badge-<?= $user['active'] ? 'success' : 'secondary' ?>">
                                    <?= $user['active'] ? 'Aktiv' : 'Inaktiv' ?>
                                </span>
                            </span>
                        </div>
                        
                        <div class="user-info-item">
                            <span class="user-info-label">Benutzer-ID:</span>
                            <span class="user-info-value"><?= e($user['id']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer">
                <div class="user-timestamps">
                    <div class="user-timestamp">
                        <span class="user-timestamp-label">Erstellt:</span>
                        <span class="user-timestamp-value">
                            <?= format_date($user['created_at'], 'd.m.Y H:i') ?>
                        </span>
                    </div>
                    <div class="user-timestamp">
                        <span class="user-timestamp-label">Aktualisiert:</span>
                        <span class="user-timestamp-value">
                            <?= format_date($user['updated_at'], 'd.m.Y H:i') ?>
                        </span>
                    </div>
                    <div class="user-timestamp">
                        <span class="user-timestamp-label">Letzter Login:</span>
                        <span class="user-timestamp-value">
                            <?= $user['last_login_at'] ? format_date($user['last_login_at'], 'd.m.Y H:i') : '-' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Schnellaktionen</h2>
            </div>
            
            <div class="card-body">
                <div class="quick-actions">
                    <?php if ($user['id'] !== $this->getUserId()): ?>
                        <a 
                            href="<?= route('admin.users.toggle_active', ['id' => $user['id']]) ?>" 
                            class="quick-action"
                            onclick="return confirm('Möchten Sie diesen Benutzer wirklich <?= $user['active'] ? 'deaktivieren' : 'aktivieren' ?>?')"
                        >
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13 3L4 12H10V21L19 12V9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M19 12H21C21.5304 12 22 12.4696 22 13V20C22 20.5304 21.5304 21 21 21H19V12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span><?= $user['active'] ? 'Deaktivieren' : 'Aktivieren' ?></span>
                        </a>
                    <?php endif; ?>
                    
                    <a 
                        href="<?= route('admin.users.edit', ['id' => $user['id']]) ?>" 
                        class="quick-action"
                    >
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11 4H4C3.46957 4 3 4.46957 3 5V19C3 19.5304 3.46957 20 4 20H11C11.5304 20 12 19.5304 12 19V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 13L23 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M23 8V16C23 16.5304 22.7893 17.0391 22.4142 17.4142C22.0391 17.7893 21.5304 18 21 18H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 13L16 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 13L20 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Bearbeiten</span>
                    </a>
                    
                    <a 
                        href="<?= route('admin.users.reset_password', ['id' => $user['id']]) ?>" 
                        class="quick-action"
                        onclick="return confirm('Möchten Sie das Passwort für diesen Benutzer wirklich zurücksetzen?')"
                    >
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 10V14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M23 13H17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17 10V4C17 3.46957 16.7893 3.03914 16.4142 2.78929C16.0391 2.53948 15.5304 2.46957 15 2.46957H5C4.46957 2.46957 3.96086 2.53948 3.58579 2.78929C3.21071 3.03914 3 3.46957 3 4V10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 10H17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 16H10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Passwort zurücksetzen</span>
                    </a>
                    
                    <?php if ($user['id'] !== $this->getUserId()): ?>
                        <a 
                            href="<?= route('admin.users.delete', ['id' => $user['id']]) ?>" 
                            class="quick-action quick-action-danger"
                            onclick="return confirm('Möchten Sie diesen Benutzer wirklich löschen?')"
                        >
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 6V4C8 3.46957 8.46957 3 9 3H15C15.5304 3 16 3.46957 16 4V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Löschen</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Additional Info Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Zusätzliche Informationen</h2>
            </div>
            
            <div class="card-body">
                <div class="user-additional-info">
                    <div class="user-additional-info-item">
                        <span class="user-additional-info-label">Benutzer seit:</span>
                        <span class="user-additional-info-value">
                            <?= format_date($user['created_at'], 'd.m.Y') ?>
                        </span>
                    </div>
                    
                    <div class="user-additional-info-item">
                        <span class="user-additional-info-label">Letzte Aktivität:</span>
                        <span class="user-additional-info-value">
                            <?= $user['last_login_at'] ? format_date($user['last_login_at'], 'd.m.Y H:i') : 'Noch nie' ?>
                        </span>
                    </div>
                    
                    <div class="user-additional-info-item">
                        <span class="user-additional-info-label">Letzte Änderung:</span>
                        <span class="user-additional-info-value">
                            <?= format_date($user['updated_at'], 'd.m.Y H:i') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
