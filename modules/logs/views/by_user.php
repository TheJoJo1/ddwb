<?php
/**
 * @var array $logs
 * @var array $pagination
 * @var array $filters
 * @var array $user
 * @var array $actionOptions
 */

$title = 'Protokolleinträge von ' . e($user['name']);
?>

<?php $this->start('content'); ?>

<div class="page-header">
    <div class="page-header-content">
        <h1>
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 21V19C20 17.9391 19.7893 17.0304 19.4142 16.2893C19.0391 15.5482 18.5304 15.0406 18 15.0406C17.4696 15.0406 16.9609 15.5482 16.5858 16.2893C16.2107 17.0304 16 17.9391 16 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M4 21V19C4 15.134 7.13401 12 11 12H13C16.866 12 20 15.134 20 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Protokolleinträge von <?= e($user['name']) ?>
        </h1>
        <p class="page-subtitle">Benutzer-ID: <?= e($user['id']) ?> | E-Mail: <?= e($user['email'] ?? 'N/A') ?></p>
    </div>
    <div class="page-header-actions">
        <a href="<?= route('logs') ?>" class="btn btn-ghost">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Alle anzeigen
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
    <strong>Keine Einträge gefunden.</strong> Dieser Benutzer hat keine Protokolleinträge.
</div>
<?php else: ?>

<div class="card">
    <div class="card-header">
        <h2>Filter</h2>
    </div>
    <div class="card-body">
        <form action="<?= route('logs.byUser', ['user_id' => $user['id']]) ?>" method="GET" class="form">
            <input type="hidden" name="user_id" value="<?= e($user['id']) ?>">
            <div class="form-grid">
                <div class="form-group">
                    <label for="action" class="form-label">Aktion</label>
                    <select id="action" name="action" class="form-select">
                        <option value="">Alle Aktionen</option>
                        <?php foreach ($actionOptions as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= ($filters['action'] ?? '') === $value ? 'selected' : '' ?>>
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="entity_type" class="form-label">Entity-Typ</label>
                    <select id="entity_type" name="entity_type" class="form-select">
                        <option value="">Alle Typen</option>
                        <option value="device" <?= ($filters['entity_type'] ?? '') === 'device' ? 'selected' : '' ?>>Gerät</option>
                        <option value="case" <?= ($filters['entity_type'] ?? '') === 'case' ? 'selected' : '' ?>>Case</option>
                        <option value="rental" <?= ($filters['entity_type'] ?? '') === 'rental' ? 'selected' : '' ?>>Ausleihe</option>
                        <option value="maintenance" <?= ($filters['entity_type'] ?? '') === 'maintenance' ? 'selected' : '' ?>>Wartung</option>
                        <option value="packlist" <?= ($filters['entity_type'] ?? '') === 'packlist' ? 'selected' : '' ?>>Packliste</option>
                        <option value="label_template" <?= ($filters['entity_type'] ?? '') === 'label_template' ? 'selected' : '' ?>>Label-Vorlage</option>
                        <option value="user" <?= ($filters['entity_type'] ?? '') === 'user' ? 'selected' : '' ?>>Benutzer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="start_date" class="form-label">Von Datum</label>
                    <input type="date" id="start_date" name="start_date" class="form-input" 
                           value="<?= e($filters['start_date'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="end_date" class="form-label">Bis Datum</label>
                    <input type="date" id="end_date" name="end_date" class="form-input" 
                           value="<?= e($filters['end_date'] ?? '') ?>">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Filtern
                </button>
                <a href="<?= route('logs.byUser', ['user_id' => $user['id']]) ?>" class="btn btn-ghost">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Zurücksetzen
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Protokolleinträge (<?= e($pagination['total']) ?> insgesamt)</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Zeitpunkt</th>
                        <th>Aktion</th>
                        <th>Entity</th>
                        <th>Beschreibung</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= format_datetime($log['created_at']) ?></td>
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
    <div class="card-footer">
        <?= render_pagination($pagination, route('logs.byUser', ['user_id' => $user['id']])) ?>
    </div>
</div>

<?php endif; ?>

<?php 
function getActionBadgeClass($action) {
    $createActions = ['create', 'store', 'add', 'import', 'login'];
    $updateActions = ['update', 'edit', 'toggle', 'extend', 'logout'];
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

function render_pagination($pagination, $baseUrl) {
    $html = '<div class="pagination">';
    
    if ($pagination['current_page'] > 1) {
        $html .= '<a href="' . $baseUrl . '?page=' . ($pagination['current_page'] - 1) . '" class="btn btn-ghost pagination-btn">&laquo; Zurück</a>';
    }
    
    $startPage = max(1, $pagination['current_page'] - 2);
    $endPage = min($pagination['total_pages'], $pagination['current_page'] + 2);
    
    if ($startPage > 1) {
        $html .= '<a href="' . $baseUrl . '?page=1" class="btn btn-ghost pagination-btn">1</a>';
        if ($startPage > 2) {
            $html .= '<span class="pagination-gap">...</span>';
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i === $pagination['current_page']) {
            $html .= '<span class="btn btn-primary pagination-btn active">' . $i . '</span>';
        } else {
            $html .= '<a href="' . $baseUrl . '?page=' . $i . '" class="btn btn-ghost pagination-btn">' . $i . '</a>';
        }
    }
    
    if ($endPage < $pagination['total_pages']) {
        if ($endPage < $pagination['total_pages'] - 1) {
            $html .= '<span class="pagination-gap">...</span>';
        }
        $html .= '<a href="' . $baseUrl . '?page=' . $pagination['total_pages'] . '" class="btn btn-ghost pagination-btn">' . $pagination['total_pages'] . '</a>';
    }
    
    if ($pagination['current_page'] < $pagination['total_pages']) {
        $html .= '<a href="' . $baseUrl . '?page=' . ($pagination['current_page'] + 1) . '" class="btn btn-ghost pagination-btn">Vor &raquo;</a>';
    }
    
    $html .= '</div>';
    return $html;
}
?>

<?php $this->end(); ?>
