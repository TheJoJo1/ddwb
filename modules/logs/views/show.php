<?php

/** @var array $log */
/** @var array $actionOptions */

use DDWB\Modules\Logs\Models\Log;

$title = 'Protokolleintrag: #' . e($log['id']) . ' - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Protokolleintrag #<?= e($log['id']) ?></h1>
            <p class="text-muted mb-0">Detaillierte Informationen zum Protokolleintrag</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/logs" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Zurück zur Liste
            </a>
            <?php if ($log['user_id'] > 0): ?>
                <a href="/logs/user/<?= e($log['user_id']) ?>" class="btn btn-outline-primary">
                    <i class="bi bi-person"></i> Alle Einträge dieses Benutzers
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4">
        <!-- Log Details -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Protokolleintrag Details</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Protokoll-ID:</dt>
                        <dd class="col-sm-8">#<?= e($log['id']) ?></dd>

                        <dt class="col-sm-4">Zeitstempel:</dt>
                        <dd class="col-sm-8"><?= format_datetime($log['timestamp']) ?></dd>

                        <dt class="col-sm-4">Aktion:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-<?= Log::getActionColor($log['action']) ?>">
                                <?= e(Log::getActionLabel($log['action'])) ?>
                            </span>
                        </dd>

                        <dt class="col-sm-4">Entitätstyp:</dt>
                        <dd class="col-sm-8"><code><?= e($log['entity_type'] ?? '-') ?></code></dd>

                        <dt class="col-sm-4">Entitäts-ID:</dt>
                        <dd class="col-sm-8"><code><?= e($log['entity_id'] ?? '-') ?></code></dd>

                        <dt class="col-sm-4">Beschreibung:</dt>
                        <dd class="col-sm-8"><?= e($log['description'] ?? '-') ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- User Information -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Benutzer Informationen</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Benutzer-ID:</dt>
                        <dd class="col-sm-8">
                            <?php if ($log['user_id'] > 0): ?>
                                #<?= e($log['user_id']) ?>
                            <?php else: ?>
                                <span class="text-muted">System</span>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-4">Name:</dt>
                        <dd class="col-sm-8"><?= e($log['user_name'] ?? '-') ?></dd>

                        <dt class="col-sm-4">E-Mail:</dt>
                        <dd class="col-sm-8"><?= e($log['user_email'] ?? '-') ?></dd>

                        <dt class="col-sm-4">IP-Adresse:</dt>
                        <dd class="col-sm-8"><code><?= e($log['ip_address'] ?? '-') ?></code></dd>

                        <dt class="col-sm-4">User-Agent:</dt>
                        <dd class="col-sm-8"><code class="text-break"><?= e($log['user_agent'] ?? '-') ?></code></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Metadata -->
    <?php if (!empty($log['metadata'])): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Metadaten</h5>
            </div>
            <div class="card-body">
                <pre class="mb-0" style="white-space: pre-wrap; word-wrap: break-word;"><?= e(json_encode($log['metadata'], JSON_PRETTY_PRINT)) ?></pre>
            </div>
        </div>
    <?php endif; ?>

    <!-- Quick Actions -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Schnellaktionen</h5>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-3">
                <a href="/logs" class="btn btn-outline-primary">
                    <i class="bi bi-list"></i> Alle Protokolleinträge
                </a>
                <?php if ($log['user_id'] > 0): ?>
                    <a href="/logs/user/<?= e($log['user_id']) ?>" class="btn btn-outline-primary">
                        <i class="bi bi-person"></i> Benutzer-Protokolle
                    </a>
                <?php endif; ?>
                <a href="/logs/action/<?= e($log['action']) ?>" class="btn btn-outline-primary">
                    <i class="bi bi-filter"></i> Aktions-Protokolle
                </a>
                <?php if (!empty($log['entity_type']) && !empty($log['entity_id'])): ?>
                    <a href="/logs?entity_type=<?= e($log['entity_type']) ?>&entity_id=<?= e($log['entity_id']) ?>" 
                       class="btn btn-outline-primary">
                        <i class="bi bi-box"></i> Entitäts-Protokolle
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $this->stop(); ?>
