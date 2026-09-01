<?php

/** @var array $packlist */
/** @var array $statusOptions */
/** @var array $itemTypeOptions */

use DDWB\Modules\Packlists\Models\Packlist;

$title = 'Drucken: ' . e($packlist['name']) . ' - DDWB';

$this->layout('layout', compact('title'));

$this->start('content');
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($packlist['name']) ?> - DDWB Packliste</title>
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 24px;
            margin: 0;
            font-weight: bold;
        }
        
        .header .subtitle {
            font-size: 14px;
            color: #666;
            margin: 5px 0 0 0;
        }
        
        .meta {
            margin-bottom: 20px;
            color: #666;
        }
        
        .meta .row {
            display: flex;
            margin-bottom: 5px;
        }
        
        .meta .label {
            width: 120px;
            font-weight: bold;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .checkbox-cell {
            text-align: center;
            width: 40px;
        }
        
        .quantity-cell {
            text-align: center;
            width: 60px;
        }
        
        .checked {
            text-decoration: line-through;
            color: #888;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            color: white;
        }
        
        .status-draft { background-color: #6c757d; }
        .status-active { background-color: #0d6efd; }
        .status-completed { background-color: #198754; }
        .status-archived { background-color: #212529; }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Print Header -->
    <div class="header">
        <h1>DDWB - Packliste</h1>
        <div class="subtitle">DingeDieWirBesitzen - Inventarverwaltung</div>
    </div>

    <!-- Metadata -->
    <div class="meta">
        <div class="row">
            <span class="label">Packlisten-ID:</span>
            <span>#<?= e($packlist['id']) ?></span>
        </div>
        <div class="row">
            <span class="label">Name:</span>
            <span><?= e($packlist['name']) ?></span>
        </div>
        <div class="row">
            <span class="label">Beschreibung:</span>
            <span><?= e($packlist['description'] ?? '-') ?></span>
        </div>
        <div class="row">
            <span class="label">Status:</span>
            <span>
                <span class="status-badge status-<?= e($packlist['status']) ?>">
                    <?= e(Packlist::getStatusLabel($packlist['status'])) ?>
                </span>
            </span>
        </div>
        <div class="row">
            <span class="label">Erstellt am:</span>
            <span><?= format_date($packlist['created_at']) ?> <?= format_time($packlist['created_at']) ?></span>
        </div>
        <div class="row">
            <span class="label">Erstellt von:</span>
            <span><?= e($packlist['created_by_name'] ?? 'Unbekannt') ?></span>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="checkbox-cell">#</th>
                <th>Typ</th>
                <th>Artikel</th>
                <th>Interne ID</th>
                <th class="quantity-cell">Menge</th>
                <th>Notizen</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($packlist['items'] ?? [])): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">
                        Keine Artikel in dieser Packliste
                    </td>
                </tr>
            <?php else: ?>
                <?php $i = 1; foreach ($packlist['items'] ?? [] as $item): ?>
                    <tr class="<?= $item['checked'] ? 'checked' : '' ?>">
                        <td class="checkbox-cell">
                            <?= $i++ ?>
                            <?php if ($item['checked']): ?>
                                <div style="margin-top: 5px; font-size: 20px;">✓</div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= e(Packlist::getItemTypeLabel($item['item_type'])) ?>
                        </td>
                        <td>
                            <?php if ($item['item_type'] === Packlist::ITEM_TYPE_DEVICE): ?>
                                <?= e($item['device_name'] ?? 'Unbekanntes Gerät') ?>
                            <?php elseif ($item['item_type'] === Packlist::ITEM_TYPE_CASE): ?>
                                <?= e($item['case_name'] ?? 'Unbekannter Case') ?>
                            <?php else: ?>
                                Unbekannter Typ
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($item['item_type'] === Packlist::ITEM_TYPE_DEVICE): ?>
                                <?= e($item['device_internal_id'] ?? '-') ?>
                            <?php elseif ($item['item_type'] === Packlist::ITEM_TYPE_CASE): ?>
                                <?= e($item['case_internal_id'] ?? '-') ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="quantity-cell">
                            <?= e($item['quantity']) ?>
                        </td>
                        <td>
                            <?= e($item['notes'] ?? '-') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Summary -->
    <div style="margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; max-width: 400px;">
            <div>
                <strong>Gesamt Artikel:</strong> <?= e($packlist['item_count'] ?? 0) ?>
            </div>
            <div>
                <strong>Abgehakt:</strong> <span style="color: #198754;"><?= e($packlist['checked_count'] ?? 0) ?></span>
            </div>
            <div>
                <strong>Offen:</strong> <span style="color: #fd7e14;"><?= e(($packlist['item_count'] ?? 0) - ($packlist['checked_count'] ?? 0)) ?></span>
            </div>
        </div>
        <div style="background-color: #f5f5f5; height: 10px; margin-top: 10px; border-radius: 5px; overflow: hidden;">
            <div style="background-color: #198754; height: 100%; width: <?= min(100, (($packlist['checked_count'] ?? 0) / max(1, $packlist['item_count'] ?? 1)) * 100) ?>%;"></div>
        </div>
        <div style="text-align: right; margin-top: 5px; font-size: 11px; color: #666;">
            <?= round((($packlist['checked_count'] ?? 0) / max(1, $packlist['item_count'] ?? 1)) * 100) ?>% abgeschlossen
        </div>
    </div>

    <!-- Notes -->
    <?php if (!empty($packlist['notes'])): ?>
        <div style="margin-bottom: 20px;">
            <h4 style="margin-bottom: 10px;">Notizen:</h4>
            <div style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9;">
                <?= nl2br(e($packlist['notes'])) ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="footer">
        Generiert am: <?= date('d.m.Y H:i:s') ?> | DDWB - DingeDieWirBesitzen
    </div>

    <!-- Print Button (visible only on screen) -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer"></i> Drucken
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary">
            <i class="bi bi-x-circle"></i> Schließen
        </button>
    </div>
</body>
</html>

<?php $this->stop(); ?>
