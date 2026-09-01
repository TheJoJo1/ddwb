<?php

/** @var array $packlist */
/** @var array $availableDevices */
/** @var array $availableCases */
/** @var array $statusOptions */
/** @var array $itemTypeOptions */

use DDWB\Modules\Packlists\Models\Packlist;

$title = 'Packliste: ' . e($packlist['name']) . ' - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Packliste: <?= e($packlist['name']) ?></h1>
            <p class="text-muted mb-0">
                <span class="badge bg-<?= Packlist::getStatusColor($packlist['status']) ?>">
                    <?= e(Packlist::getStatusLabel($packlist['status'])) ?>
                </span>
                <span class="ms-2">
                    <?= e($packlist['item_count'] ?? 0) ?> Artikel
                    <?php if ($packlist['checked_count'] > 0): ?>
                        - <?= e($packlist['checked_count']) ?> abgehakt
                    <?php endif; ?>
                </span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="/packlists" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Zurück
            </a>
            <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                <a href="/packlists/<?= e($packlist['id']) ?>/edit" class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i> Bearbeiten
                </a>
                <div class="dropdown">
                    <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i> Mehr
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="/packlists/<?= e($packlist['id']) ?>/print" target="_blank">
                                <i class="bi bi-printer"></i> Drucken
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/packlists/<?= e($packlist['id']) ?>/pdf">
                                <i class="bi bi-file-pdf"></i> PDF exportieren
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="/packlists/<?= e($packlist['id']) ?>/delete" 
                                  onsubmit="return confirm('Sind Sie sicher, dass Sie diese Packliste löschen möchten?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-trash"></i> Packliste löschen
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4">
        <!-- Packlist Details -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Packlisten Details</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Packlisten-ID:</dt>
                        <dd class="col-sm-8">#<?= e($packlist['id']) ?></dd>

                        <dt class="col-sm-4">Name:</dt>
                        <dd class="col-sm-8"><?= e($packlist['name']) ?></dd>

                        <dt class="col-sm-4">Beschreibung:</dt>
                        <dd class="col-sm-8"><?= e($packlist['description'] ?? '-') ?></dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-<?= Packlist::getStatusColor($packlist['status']) ?>">
                                <?= e(Packlist::getStatusLabel($packlist['status'])) ?>
                            </span>
                        </dd>

                        <dt class="col-sm-4">Erstellt von:</dt>
                        <dd class="col-sm-8"><?= e($packlist['created_by_name'] ?? 'Unbekannt') ?></dd>

                        <dt class="col-sm-4">Erstellt:</dt>
                        <dd class="col-sm-8"><?= format_datetime($packlist['created_at']) ?></dd>

                        <dt class="col-sm-4">Aktualisiert:</dt>
                        <dd class="col-sm-8"><?= format_datetime($packlist['updated_at']) ?></dd>

                        <dt class="col-sm-4">Artikel:</dt>
                        <dd class="col-sm-8"><?= e($packlist['item_count'] ?? 0) ?></dd>

                        <dt class="col-sm-4">Abgehakt:</dt>
                        <dd class="col-sm-8"><?= e($packlist['checked_count'] ?? 0) ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Schnellaktionen</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                <i class="bi bi-plus-circle"></i> Artikel hinzufügen
                            </button>
                            <a href="/packlists/<?= e($packlist['id']) ?>/edit" class="btn btn-outline-primary">
                                <i class="bi bi-pencil"></i> Packliste bearbeiten
                            </a>
                        <?php endif; ?>
                        <a href="/packlists/<?= e($packlist['id']) ?>/print" class="btn btn-outline-dark" target="_blank">
                            <i class="bi bi-printer"></i> Drucken
                        </a>
                        <a href="/packlists/<?= e($packlist['id']) ?>/pdf" class="btn btn-outline-dark">
                            <i class="bi bi-file-pdf"></i> PDF exportieren
                        </a>
                    </div>

                    <div class="mt-4">
                        <h6>Statistik</h6>
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="text-muted small">Gesamt Artikel:</div>
                                <div class="fw-bold"><?= e($packlist['item_count'] ?? 0) ?></div>
                            </div>
                            <div>
                                <div class="text-muted small">Abgehakt:</div>
                                <div class="fw-bold text-success"><?= e($packlist['checked_count'] ?? 0) ?></div>
                            </div>
                            <div>
                                <div class="text-muted small">Offen:</div>
                                <div class="fw-bold text-warning"><?= e(($packlist['item_count'] ?? 0) - ($packlist['checked_count'] ?? 0)) ?></div>
                            </div>
                        </div>
                        <?php if ($packlist['item_count'] > 0): ?>
                            <div class="progress mt-3">
                                <div class="progress-bar bg-success" 
                                     role="progressbar" 
                                     style="width: <?= min(100, (($packlist['checked_count'] ?? 0) / max(1, $packlist['item_count'] ?? 1)) * 100) ?>%" 
                                     aria-valuenow="<?= (($packlist['checked_count'] ?? 0) / max(1, $packlist['item_count'] ?? 1)) * 100 ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    <?= round((($packlist['checked_count'] ?? 0) / max(1, $packlist['item_count'] ?? 1)) * 100) ?>%
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Packlist Items -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Artikel in der Packliste</h5>
            <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                    <i class="bi bi-plus-circle"></i> Artikel hinzufügen
                </button>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <?php if (empty($packlist['items'] ?? [])): ?>
                <div class="p-5 text-center">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">Keine Artikel in dieser Packliste</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Typ</th>
                                <th>Artikel</th>
                                <th>Menge</th>
                                <th>Notizen</th>
                                <th>Status</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody id="packlist-items-tbody">
                            <?php foreach ($packlist['items'] ?? [] as $item): ?>
                                <tr data-item-id="<?= e($item['id']) ?>" data-sort-order="<?= e($item['sort_order']) ?>">
                                    <td>
                                        <span class="badge bg-info">
                                            <?= e(Packlist::getItemTypeLabel($item['item_type'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($item['item_type'] === Packlist::ITEM_TYPE_DEVICE): ?>
                                            <a href="/devices/<?= e($item['item_id']) ?>" class="text-decoration-none">
                                                <?= e($item['device_name'] ?? 'Unbekanntes Gerät') ?>
                                            </a>
                                            <div class="text-muted small"><?= e($item['device_internal_id'] ?? '') ?></div>
                                        <?php elseif ($item['item_type'] === Packlist::ITEM_TYPE_CASE): ?>
                                            <a href="/cases/<?= e($item['item_id']) ?>" class="text-decoration-none">
                                                <?= e($item['case_name'] ?? 'Unbekannter Case') ?>
                                            </a>
                                            <div class="text-muted small"><?= e($item['case_internal_id'] ?? '') ?></div>
                                        <?php else: ?>
                                            <span class="text-muted">Unbekannter Typ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <input type="number" 
                                               class="form-control form-control-sm item-quantity" 
                                               data-item-id="<?= e($item['id']) ?>" 
                                               value="<?= e($item['quantity']) ?>" 
                                               min="1" 
                                               onchange="updateQuantity(<?= e($item['id']) ?>, this.value)">
                                    </td>
                                    <td>
                                        <input type="text" 
                                               class="form-control form-control-sm item-notes" 
                                               data-item-id="<?= e($item['id']) ?>" 
                                               value="<?= e($item['notes'] ?? '') ?>" 
                                               placeholder="Notizen..." 
                                               onchange="updateNotes(<?= e($item['id']) ?>, this.value)">
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input item-checked" 
                                                   type="checkbox" 
                                                   id="checked-<?= e($item['id']) ?>" 
                                                   data-item-id="<?= e($item['id']) ?>" 
                                                   <?= $item['checked'] ? 'checked' : '' ?> 
                                                   onchange="toggleChecked(<?= e($item['id']) ?>, this.checked)">
                                            <label class="form-check-label" for="checked-<?= e($item['id']) ?>">
                                                <?= $item['checked'] ? 'Abgehakt' : 'Offen' ?>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="removeItem(<?= e($item['id']) ?>)" 
                                                        title="Entfernen">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Notes -->
    <?php if (!empty($packlist['notes'])): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Notizen</h5>
            </div>
            <div class="card-body">
                <p class="mb-0"><?= nl2br(e($packlist['notes'])) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Add Item Modal -->
    <?php if ($this->auth()->check() && $this->auth()->user()['role'] === 'admin'): ?>
        <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST" action="/packlists/<?= e($packlist['id']) ?>/add-item">
                        <?= csrf_field() ?>
                        <div class="modal-header">
                            <h5 class="modal-title" id="addItemModalLabel">Artikel zur Packliste hinzufügen</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="item_type" class="form-label">Typ</label>
                                    <select class="form-select" id="item_type" name="item_type" required>
                                        <?php foreach ($itemTypeOptions as $value => $label): ?>
                                            <option value="<?= e($value) ?>">
                                                <?= e($label) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-7">
                                    <label for="item_id" class="form-label">Artikel</label>
                                    <select class="form-select" id="item_id" name="item_id" required>
                                        <option value="" selected disabled>Bitte wählen Sie einen Artikel aus</option>
                                        <optgroup label="Verfügbare Geräte">
                                            <?php foreach ($availableDevices as $device): ?>
                                                <option value="<?= e($device['id']) ?>" 
                                                    data-type="device">
                                                    <?= e($device['internal_id']) ?> - <?= e($device['name']) ?>
                                                    <?php if (!empty($device['serial_number'])): ?>
                                                        (SN: <?= e($device['serial_number']) ?>)
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                        <optgroup label="Verfügbare Cases">
                                            <?php foreach ($availableCases as $case): ?>
                                                <option value="<?= e($case['id']) ?>" 
                                                    data-type="case">
                                                    <?= e($case['internal_id']) ?> - <?= e($case['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label for="quantity" class="form-label">Menge</label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="quantity" 
                                           name="quantity" 
                                           value="1" 
                                           min="1">
                                </div>

                                <div class="col-12">
                                    <label for="notes" class="form-label">Notizen (optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Notizen zum Artikel..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Abbrechen
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Artikel hinzufügen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// AJAX functions for packlist items
function toggleChecked(itemId, checked) {
    fetch('/packlists/<?= e($packlist['id']) ?>/toggle-item/' + itemId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
        },
        body: 'checked=' + (checked ? 1 : 0)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the UI
            const label = document.querySelector('#checked-' + itemId + ' + label');
            if (label) {
                label.textContent = checked ? 'Abgehakt' : 'Offen';
            }
            
            // Update statistics
            updateStatistics();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function updateQuantity(itemId, quantity) {
    fetch('/api/packlists/<?= e($packlist['id']) ?>/items/' + itemId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
        },
        body: JSON.stringify({quantity: quantity, action: 'update_quantity'})
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Error updating quantity');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function updateNotes(itemId, notes) {
    fetch('/api/packlists/<?= e($packlist['id']) ?>/items/' + itemId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
        },
        body: JSON.stringify({notes: notes, action: 'update_notes'})
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Error updating notes');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function removeItem(itemId) {
    if (!confirm('Sind Sie sicher, dass Sie diesen Artikel aus der Packliste entfernen möchten?')) {
        return;
    }

    fetch('/packlists/<?= e($packlist['id']) ?>/remove-item/' + itemId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
        }
    })
    .then(response => {
        if (response.ok) {
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function updateStatistics() {
    // This would update the statistics display
    // For now, just reload the page
    window.location.reload();
}
</script>

<?php $this->stop(); ?>
