<div class="profile-show">
    <div class="page-header">
        <h1 class="page-title">Mein Profil</h1>
        
        <div class="page-actions">
            <a href="<?= route('profile.edit') ?>" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11 4H4C3.46957 4 3 4.46957 3 5V19C3 19.5304 3.46957 20 4 20H11C11.5304 20 12 19.5304 12 19V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18 13L23 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M23 8V16C23 16.5304 22.7893 17.0391 22.4142 17.4142C22.0391 17.7893 21.5304 18 21 18H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18 13L16 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18 13L20 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Profil bearbeiten
            </a>
        </div>
    </div>

    <div class="profile-grid">
        <!-- Profile Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Meine Daten</h2>
            </div>
            
            <div class="card-body">
                <div class="profile-details">
                    <div class="profile-avatar">
                        <span class="user-avatar-xlarge">
                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                        </span>
                    </div>
                    
                    <div class="profile-info">
                        <div class="profile-info-item">
                            <span class="profile-info-label">Name:</span>
                            <span class="profile-info-value"><?= e($user['name']) ?></span>
                        </div>
                        
                        <div class="profile-info-item">
                            <span class="profile-info-label">E-Mail:</span>
                            <span class="profile-info-value"><?= e($user['email']) ?></span>
                        </div>
                        
                        <div class="profile-info-item">
                            <span class="profile-info-label">Rolle:</span>
                            <span class="profile-info-value">
                                <span class="badge badge-<?= $user['role'] === 'admin' ? 'error' : 'primary' ?>">
                                    <?= e(ucfirst($user['role'])) ?>
                                </span>
                            </span>
                        </div>
                        
                        <div class="profile-info-item">
                            <span class="profile-info-label">Status:</span>
                            <span class="profile-info-value">
                                <span class="badge badge-<?= $user['active'] ? 'success' : 'secondary' ?>">
                                    <?= $user['active'] ? 'Aktiv' : 'Inaktiv' ?>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer">
                <div class="profile-timestamps">
                    <div class="profile-timestamp">
                        <span class="profile-timestamp-label">Benutzer seit:</span>
                        <span class="profile-timestamp-value">
                            <?= format_date($user['created_at'], 'd.m.Y') ?>
                        </span>
                    </div>
                    <div class="profile-timestamp">
                        <span class="profile-timestamp-label">Letzter Login:</span>
                        <span class="profile-timestamp-value">
                            <?= $user['last_login_at'] ? format_date($user['last_login_at'], 'd.m.Y H:i') : 'Noch nie' ?>
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
                <div class="profile-quick-actions">
                    <a href="<?= route('profile.edit') ?>" class="profile-quick-action">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11 4H4C3.46957 4 3 4.46957 3 5V19C3 19.5304 3.46957 20 4 20H11C11.5304 20 12 19.5304 12 19V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 13L23 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M23 8V16C23 16.5304 22.7893 17.0391 22.4142 17.4142C22.0391 17.7893 21.5304 18 21 18H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 13L16 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 13L20 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Profil bearbeiten</span>
                    </a>
                    
                    <a href="<?= route('profile.change_password') ?>" class="profile-quick-action">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 10V14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M23 13H17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17 10V4C17 3.46957 16.7893 3.03914 16.4142 2.78929C16.0391 2.53948 15.5304 2.46957 15 2.46957H5C4.46957 2.46957 3.96086 2.53948 3.58579 2.78929C3.21071 3.03914 3 3.46957 3 4V10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 10H17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 16H10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Passwort ändern</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Letzte Aktivitäten</h2>
            </div>
            
            <div class="card-body">
                <?php if (empty($logs)): ?>
                    <p class="text-muted text-center">Keine Aktivitäten gefunden.</p>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach ($logs as $log): ?>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <?= $this->getActionIcon($log['action'] ?? '') ?>
                                </div>
                                <div class="activity-content">
                                    <span class="activity-text">
                                        <?= e($log['description'] ?? 'Unbekannte Aktion') ?>
                                    </span>
                                    <span class="activity-time">
                                        <?= format_date($log['timestamp'] ?? '', 'd.m.Y H:i') ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="card-footer">
                <a href="<?= route('logs') ?>" class="btn btn-sm btn-ghost">
                    Alle Aktivitäten anzeigen
                </a>
            </div>
        </div>
    </div>
</div>

<?php

/**
 * Helper method to get action icon
 */
class ProfileHelper
{
    public function getActionIcon(string $action): string
    {
        $icons = [
            'login' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M15 3H9C8.46957 3 8 3.46957 8 4V6H16V4C16 3.46957 15.5304 3 15 3Z" stroke="currentColor" stroke-width="2"/><path d="M15 13H9C8.46957 13 8 13.4696 8 14V20C8 20.5304 8.46957 21 9 21H15C15.5304 21 16 20.5304 16 20V14C16 13.4696 15.5304 13 15 13Z" stroke="currentColor" stroke-width="2"/><path d="M9 13V6" stroke="currentColor" stroke-width="2"/><path d="M15 13V6" stroke="currentColor" stroke-width="2"/></svg>',
            'logout' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 21H5C4.46957 21 4 20.5304 4 20V4C4 3.46957 4.46957 3 5 3H9" stroke="currentColor" stroke-width="2"/><path d="M16 17L21 12L16 7" stroke="currentColor" stroke-width="2"/><path d="M21 12H9" stroke="currentColor" stroke-width="2"/></svg>',
            'create' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 5V21" stroke="currentColor" stroke-width="2"/><path d="M5 12H21" stroke="currentColor" stroke-width="2"/></svg>',
            'update' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M11 4H4C3.46957 4 3 4.46957 3 5V19C3 19.5304 3.46957 20 4 20H11" stroke="currentColor" stroke-width="2"/><path d="M18 13L23 8" stroke="currentColor" stroke-width="2"/><path d="M23 8V16C23 16.5304 22.7893 17.0391 22.4142 17.4142C22.0391 17.7893 21.5304 18 21 18H16" stroke="currentColor" stroke-width="2"/><path d="M18 13L16 15" stroke="currentColor" stroke-width="2"/><path d="M18 13L20 11" stroke="currentColor" stroke-width="2"/></svg>',
            'delete' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M3 6H5H21" stroke="currentColor" stroke-width="2"/><path d="M8 6V4C8 3.46957 8.46957 3 9 3H15C15.5304 3 16 3.46957 16 4V6" stroke="currentColor" stroke-width="2"/><path d="M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2"/></svg>',
            'lend' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 19L19 12L12 5" stroke="currentColor" stroke-width="2"/><path d="M5 12H19" stroke="currentColor" stroke-width="2"/></svg>',
            'return' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M19 12H5" stroke="currentColor" stroke-width="2"/><path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2"/></svg>',
            'maintenance_create' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M10.878 2.03003C10.878 2.03003 10.878 2.03003 10.878 2.03003L13.122 4.27403C15.966 7.11803 15.966 11.162 15.966 11.162C15.966 11.162 14.494 14.494 12 16C9.506 14.494 8.034 11.162 8.034 11.162C8.034 11.162 8.034 7.11803 10.878 2.03003Z" stroke="currentColor" stroke-width="2"/><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2"/></svg>',
            'case_assign' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M21 16V8C21 7.46957 20.7893 7.03914 20.4142 6.78929C20.0391 6.53948 19.5304 6.46957 19 6.46957H5C4.46957 6.46957 3.96086 6.53948 3.58579 6.78929C3.21071 7.03914 3 7.46957 3 8V16" stroke="currentColor" stroke-width="2"/><path d="M3 16L7 20H17L21 16" stroke="currentColor" stroke-width="2"/><path d="M7 20V16" stroke="currentColor" stroke-width="2"/><path d="M17 20V16" stroke="currentColor" stroke-width="2"/></svg>',
        ];

        return $icons[$action] ?? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/></svg>';
    }
}

// Add method to controller instance
if (isset($this) && is_object($this)) {
    $helper = new ProfileHelper();
    $this->getActionIcon = function($action) use ($helper) {
        return $helper->getActionIcon($action);
    };
}

?>
