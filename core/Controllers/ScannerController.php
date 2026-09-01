<?php

declare(strict_types=1);

namespace DDWB\Controllers;

use DDWB\Controller;
use DDWB\Database;

/**
 * Scanner Controller
 * 
 * Handles scanner-related requests for QR/barcode scanning
 * Supports: devices, cases, rentals, maintenance, packlists, label templates
 */
final class ScannerController extends Controller
{
    private array $entityTypes = [
        'device',
        'case',
        'rental',
        'maintenance',
        'packlist',
        'label_template',
    ];

    private array $entityConfig = [
        'device' => [
            'table' => 'devices',
            'id_field' => 'internal_id',
            'route' => 'devices.show',
            'display_name' => 'Gerät',
        ],
        'case' => [
            'table' => 'cases',
            'id_field' => 'internal_id',
            'route' => 'cases.show',
            'display_name' => 'Case',
        ],
        'rental' => [
            'table' => 'rentals',
            'id_field' => 'internal_id',
            'route' => 'rentals.show',
            'display_name' => 'Ausleihe',
        ],
        'maintenance' => [
            'table' => 'maintenance',
            'id_field' => 'internal_id',
            'route' => 'maintenance.show',
            'display_name' => 'Wartung',
        ],
        'packlist' => [
            'table' => 'packlists',
            'id_field' => 'internal_id',
            'route' => 'packlists.show',
            'display_name' => 'Packliste',
        ],
        'label_template' => [
            'table' => 'label_templates',
            'id_field' => 'internal_id',
            'route' => 'labels.showTemplate',
            'display_name' => 'Label-Vorlage',
        ],
    ];

    /**
     * Show the scanner interface
     */
    public function showScanner(): void
    {
        $this->ensureAuthenticated();

        $this->view('scanner/scanner', [
            'title' => 'Scanner',
            'csrf_token' => csrf_token(),
            'entityTypes' => $this->entityTypes,
            'entityDisplayNames' => array_map(fn($type) => $this->entityConfig[$type]['display_name'], $this->entityTypes),
        ]);
    }

    /**
     * Handle a scanned identifier
     * 
     * @param string $identifier The scanned identifier
     */
    public function scan(string $identifier): void
    {
        $this->ensureAuthenticated();

        $result = $this->resolveIdentifier($identifier);

        if ($result !== null) {
            $this->audit(
                'scan',
                $result['type'],
                $result['id'],
                'Scanned ' . $this->entityConfig[$result['type']]['display_name'] . ' via scanner',
                ['identifier' => $identifier, 'scanned_at' => now()]
            );
            $this->redirectToRoute($this->entityConfig[$result['type']]['route'], ['id' => $result['id']]);
            return;
        }

        // Item not found
        $this->flash('error', 'Item mit Identifier "' . e($identifier) . '" nicht gefunden.');
        $this->redirectToRoute('scanner');
    }

    /**
     * Resolve an identifier to an entity
     * 
     * @param string $identifier The identifier to resolve
     * @return array|null The resolved entity info or null
     */
    private function resolveIdentifier(string $identifier): ?array
    {
        $database = $this->getDatabase();
        $identifier = trim($identifier);

        // Try to find by internal_id across all entity types
        foreach ($this->entityTypes as $type) {
            $config = $this->entityConfig[$type];
            
            $entity = $database->selectOne(
                "SELECT id, {$config['id_field']} as internal_id, name FROM {$config['table']} " .
                "WHERE {$config['id_field']} = ? AND deleted_at IS NULL",
                [$identifier]
            );

            if ($entity !== null) {
                return [
                    'type' => $type,
                    'id' => $entity['id'],
                    'internal_id' => $entity['internal_id'],
                    'name' => $entity['name'] ?? $entity['internal_id'],
                ];
            }
        }

        // Try to find by numeric ID across all entity types
        if (is_numeric($identifier)) {
            $id = (int)$identifier;

            foreach ($this->entityTypes as $type) {
                $config = $this->entityConfig[$type];
                
                $entity = $database->selectOne(
                    "SELECT id, {$config['id_field']} as internal_id, name FROM {$config['table']} " .
                    "WHERE id = ? AND deleted_at IS NULL",
                    [$id]
                );

                if ($entity !== null) {
                    return [
                        'type' => $type,
                        'id' => $entity['id'],
                        'internal_id' => $entity['internal_id'],
                        'name' => $entity['name'] ?? $entity['internal_id'],
                    ];
                }
            }
        }

        // Try to find devices/cases by serial number or other unique fields
        $device = $database->selectOne(
            'SELECT id, internal_id, name, serial_number FROM devices WHERE serial_number = ? AND deleted_at IS NULL',
            [$identifier]
        );

        if ($device !== null) {
            return [
                'type' => 'device',
                'id' => $device['id'],
                'internal_id' => $device['internal_id'],
                'name' => $device['name'] ?? $device['internal_id'],
            ];
        }

        return null;
    }

    /**
     * Ensure the user is authenticated
     */
    private function ensureAuthenticated(): void
    {
        if (!$this->isAuthenticated()) {
            $this->redirectToRoute('login');
        }
    }
}
