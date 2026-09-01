<?php
/**
 * @var array $logs
 * @var array $actionOptions
 */

$title = 'Heutige Protokolleinträge';
?>

<?php $this->start('content'); ?>

<div class="page-header">
    <div class="page-header-content">
        <h1>
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                <path d="M16 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Heute - Protokolleinträge
        </h1>
        <p class="page-subtitle">Alle Aktionen vom heutigen Tag: <?= format_date('Y-m-d') ?></p>
    </div>
    <div class="page-header-actions">
        <a href="<?= route('logs') ?>" class="btn btn-ghost">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Alle anzeigen
        </a>
        <a href="<?= route('logs.recent') ?>" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14.5 2H6C5.46957 2 4.96086 2.21071 4.58579 2.53929C4.21071 2.86785 4 3.29815 4 3.79815V20.2019C4 20.7019 4.21071 21.1321 4.58579 21.4608C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4608C19.7893 21.1321 20 20.7019 20 20.2019V7.79815C20 7.29815 19.7893 6.86785 19.4142 6.53929C19.0391 6.21071 18.5304 6 18 6L14.5 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M14.5 2V6H18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Letzte Einträge
        </a>
    </div>
</div>

<?php if (empty($logs)): ?>
<div class="alert alert-info">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
        <path d="M12 16V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M12 8H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <strong>Keine Einträge für heute gefunden.</strong> Es gibt keine Protokolleinträge für den heutigen Tag.
</div>
<?php else: ?>

<div class="card">
    <div class="card-header">
        <h2>Heutige Einträge (<?= count($logs) ?>)</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Zeit</th>
                        <th>Benutzer</th>
                        <th>Aktion</th>
                        <th>Entity</th>
                        <th>Beschreibung</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= format_time($log['created_at']) ?></td>
                        <td>
                            <?php if ($log['user_id']): ?>
                                <a href="<?= route('logs.byUser', ['user_id' => $log['user_id']]) ?>">
                                    <?= e($log['user_name'] ?? 'Benutzer #' . $log['user_id']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">System</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-<?= getActionBadgeClass($log['action']) ?>">
                                <?= e($actionOptions[$log['action']] ?? $log['action']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($log['entity_type']): ?>
                                <?= e(ucfirst($log['entity_type'])) ?>
                                <?php if ($log['entity_id']): ?>
                                    <span class="text-muted">#<?= e($log['entity_id']) ?></span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e($log['description'] ?? '-') ?></td>
                        <td>
                            <a href="<?= route('logs.show', ['id' => $log['id']]) ?>" class="btn btn-ghost btn-sm" title="Details anzeigen">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                    <path d="M12 16V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 8H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Statistik für heute</h2>
    </div>
    <div class="card-body">
        <div class="stats-grid">
            <?php 
            $actionCounts = [];
            foreach ($logs as $log) {
                $action = $log['action'] ?? 'unknown';
                $actionCounts[$action] = ($actionCounts[$action] ?? 0) + 1;
            }
            
            $totalActions = count($logs);
            $uniqueUsers = count(array_unique(array_filter(array_map(fn($l) => $l['user_id'], $logs))));
            $entityTypes = count(array_unique(array_filter(array_map(fn($l) => $l['entity_type'], $logs))));
            
            // Top 5 actions
            arsort($actionCounts);
            $topActions = array_slice($actionCounts, 0, 5);
            ?>
            
            <div class="stat-card">
                <div class="stat-value"><?= e($totalActions) ?></div>
                <div class="stat-label">Gesamtaktionen</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= e($uniqueUsers) ?></div>
                <div class="stat-label">Einzigartige Benutzer</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= e($entityTypes) ?></div>
                <div class="stat-label">Entity-Typen betroffen</div>
            </div>
        </div>
        
        <?php if (!empty($topActions)): ?>
        <div class="section">
            <h3>Häufigste Aktionen</h3>
            <div class="progress-list">
                <?php foreach ($topActions as $action => $count): ?>
                <div class="progress-item">
                    <div class="progress-info">
                        <span class="progress-label">
                            <span class="badge badge-<?= getActionBadgeClass($action) ?>">
                                <?= e($actionOptions[$action] ?? $action) ?>
                            </span>
                        </span>
                        <span class="progress-count"><?= e($count) ?></span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= min(100, ($count / $totalActions) * 100) ?>%;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php endif; ?>

<?php 
function getActionBadgeClass($action) {
    $createActions = ['create', 'store', 'add', 'import'];
    $updateActions = ['update', 'edit', 'toggle', 'extend'];
    $deleteActions = ['delete', 'destroy', 'remove', 'clear'];
    $warningActions = ['scan', 'export', 'print'];
    
    if (in_array($action, $createActions)) {
        return 'success';
    } elseif (in_array($action, $updateActions)) {
        return 'info';
    } elseif (in_array($action, $deleteActions)) {
        return 'danger';
    } elseif (in_array($action, $warningActions)) {
        return 'warning';
    } else {
        return 'secondary';
    }
}
?>

<?php $this->end(); ?>
