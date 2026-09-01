<aside class="sidebar" role="complementary" aria-label="Seitennavigation">
    <div class="sidebar-content">
        <!-- Mobile Close Button -->
        <button 
            type="button" 
            class="mobile-close btn btn-icon btn-ghost md:hidden" 
            aria-label="Menü schließen"
            onclick="closeMobileMenu()"
        >
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <!-- Quick Actions -->
        <div class="sidebar-section">
            <h3 class="sidebar-section-title">Schnellzugriff</h3>
            <div class="sidebar-quick-actions">
                <a href="<?= route('scanner') ?>" class="sidebar-quick-action" title="Scanner öffnen">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                        <line x1="9" y1="3" x2="9" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <line x1="15" y1="3" x2="15" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <line x1="9" y1="21" x2="9" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <line x1="15" y1="21" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <line x1="3" y1="9" x2="21" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <line x1="3" y1="15" x2="21" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span>Scanner</span>
                </a>
                
                <a href="<?= route('devices.create') ?>" class="sidebar-quick-action" title="Neues Gerät">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 5V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M5 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Gerät</span>
                </a>
                
                <a href="<?= route('cases.create') ?>" class="sidebar-quick-action" title="Neuer Case">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 16V8C21 7.46957 20.7893 7.03914 20.4142 6.78929C20.0391 6.53948 19.5304 6.46957 19 6.46957H5C4.46957 6.46957 3.96086 6.53948 3.58579 6.78929C3.21071 7.03914 3 7.46957 3 8V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 16L7 20H17L21 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7 20V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17 20V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Case</span>
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="sidebar-section">
            <h3 class="sidebar-section-title">Statistik</h3>
            <div class="sidebar-stats">
                <div class="sidebar-stat">
                    <span class="sidebar-stat-label">Geräte</span>
                    <span class="sidebar-stat-value">
                        <?= $this->getDeviceCount() ?? '0' ?>
                    </span>
                </div>
                <div class="sidebar-stat">
                    <span class="sidebar-stat-label">Cases</span>
                    <span class="sidebar-stat-value">
                        <?= $this->getCaseCount() ?? '0' ?>
                    </span>
                </div>
                <div class="sidebar-stat">
                    <span class="sidebar-stat-label">Ausgeliehen</span>
                    <span class="sidebar-stat-value">
                        <?= $this->getRentalCount() ?? '0' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="sidebar-section">
            <h3 class="sidebar-section-title">Letzte Aktivitäten</h3>
            <div class="sidebar-activity">
                <?php foreach ($this->getRecentLogs() ?? [] as $log): ?>
                    <div class="sidebar-activity-item">
                        <span class="sidebar-activity-icon">
                            <?= $this->getActionIcon($log['action'] ?? '') ?>
                        </span>
                        <div class="sidebar-activity-content">
                            <span class="sidebar-activity-text">
                                <?= e($log['description'] ?? 'Unbekannte Aktion') ?>
                            </span>
                            <span class="sidebar-activity-time">
                                <?= format_date($log['timestamp'] ?? '', 'H:i') ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</aside>

<?php

/**
 * Helper methods for sidebar
 */
class SidebarHelper
{
    public function getDeviceCount(): int
    {
        try {
            $db = db();
            return (int)$db->selectValue('SELECT COUNT(*) FROM devices WHERE deleted_at IS NULL');
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getCaseCount(): int
    {
        try {
            $db = db();
            return (int)$db->selectValue('SELECT COUNT(*) FROM cases WHERE deleted_at IS NULL');
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getRentalCount(): int
    {
        try {
            $db = db();
            return (int)$db->selectValue(
                'SELECT COUNT(*) FROM rentals WHERE status = ? AND deleted_at IS NULL',
                ['active']
            );
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getRecentLogs(): array
    {
        try {
            $db = db();
            return $db->select(
                'SELECT * FROM logs ORDER BY timestamp DESC LIMIT 5'
            );
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getActionIcon(string $action): string
    {
        $icons = [
            'login' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M15 3H9C8.46957 3 8 3.46957 8 4V6H16V4C16 3.46957 15.5304 3 15 3Z" stroke="currentColor" stroke-width="2"/><path d="M15 13H9C8.46957 13 8 13.4696 8 14V20C8 20.5304 8.46957 21 9 21H15C15.5304 21 16 20.5304 16 20V14C16 13.4696 15.5304 13 15 13Z" stroke="currentColor" stroke-width="2"/><path d="M9 13V6" stroke="currentColor" stroke-width="2"/><path d="M15 13V6" stroke="currentColor" stroke-width="2"/></svg>',
            'logout' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M9 21H5C4.46957 21 4 20.5304 4 20V4C4 3.46957 4.46957 3 5 3H9" stroke="currentColor" stroke-width="2"/><path d="M16 17L21 12L16 7" stroke="currentColor" stroke-width="2"/><path d="M21 12H9" stroke="currentColor" stroke-width="2"/></svg>',
            'create' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 5V21" stroke="currentColor" stroke-width="2"/><path d="M5 12H21" stroke="currentColor" stroke-width="2"/></svg>',
            'update' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M11 4H4C3.46957 4 3 4.46957 3 5V19C3 19.5304 3.46957 20 4 20H11" stroke="currentColor" stroke-width="2"/><path d="M18 13L23 8" stroke="currentColor" stroke-width="2"/><path d="M23 8V16C23 16.5304 22.7893 17.0391 22.4142 17.4142C22.0391 17.7893 21.5304 18 21 18H16" stroke="currentColor" stroke-width="2"/><path d="M18 13L16 15" stroke="currentColor" stroke-width="2"/><path d="M18 13L20 11" stroke="currentColor" stroke-width="2"/></svg>',
            'delete' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M3 6H5H21" stroke="currentColor" stroke-width="2"/><path d="M8 6V4C8 3.46957 8.46957 3 9 3H15C15.5304 3 16 3.46957 16 4V6" stroke="currentColor" stroke-width="2"/><path d="M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2"/></svg>',
            'lend' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 19L19 12L12 5" stroke="currentColor" stroke-width="2"/><path d="M5 12H19" stroke="currentColor" stroke-width="2"/></svg>',
            'return' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M19 12H5" stroke="currentColor" stroke-width="2"/><path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2"/></svg>',
            'maintenance_create' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M10.878 2.03003C10.878 2.03003 10.878 2.03003 10.878 2.03003L13.122 4.27403C15.966 7.11803 15.966 11.162 15.966 11.162C15.966 11.162 14.494 14.494 12 16C9.506 14.494 8.034 11.162 8.034 11.162C8.034 11.162 8.034 7.11803 10.878 2.03003Z" stroke="currentColor" stroke-width="2"/><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2"/></svg>',
            'case_assign' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M21 16V8C21 7.46957 20.7893 7.03914 20.4142 6.78929C20.0391 6.53948 19.5304 6.46957 19 6.46957H5C4.46957 6.46957 3.96086 6.53948 3.58579 6.78929C3.21071 7.03914 3 7.46957 3 8V16" stroke="currentColor" stroke-width="2"/><path d="M3 16L7 20H17L21 16" stroke="currentColor" stroke-width="2"/><path d="M7 20V16" stroke="currentColor" stroke-width="2"/><path d="M17 20V16" stroke="currentColor" stroke-width="2"/></svg>',
        ];

        return $icons[$action] ?? '<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/></svg>';
    }
}

// Create a helper instance
$sidebarHelper = new SidebarHelper();

// Add methods to the current controller instance if it exists
if (isset($this) && is_object($this)) {
    foreach (get_class_methods($sidebarHelper) as $method) {
        if (!method_exists($this, $method)) {
            $this->$method = function (...$args) use ($sidebarHelper, $method) {
                return $sidebarHelper->$method(...$args);
            };
        }
    }
}

?>
