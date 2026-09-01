<?php

declare(strict_types=1);

namespace DDWB\Controllers\Api;

use DDWB\Controller;
use DDWB\Database;

/**
 * API Scanner Controller
 * 
 * Handles API requests for the scanner
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
            'fields' => ['id', 'internal_id', 'name', 'type', 'serial_number'],
        ],
        'case' => [
            'table' => 'cases',
            'id_field' => 'internal_id',
            'route' => 'cases.show',
            'display_name' => 'Case',
            'fields' => ['id', 'internal_id', 'name', 'description'],
        ],
        'rental' => [
            'table' => 'rentals',
            'id_field' => 'internal_id',
            'route' => 'rentals.show',
            'display_name' => 'Ausleihe',
            'fields' => ['id', 'internal_id', 'borrower_name', 'start_date', 'end_date'],
        ],
        'maintenance' => [
            'table' => 'maintenance',
            'id_field' => 'internal_id',
            'route' => 'maintenance.show',
            'display_name' => 'Wartung',
            'fields' => ['id', 'internal_id', 'device_id', 'maintenance_type', 'next_date'],
        ],
        'packlist' => [
            'table' => 'packlists',
            'id_field' => 'internal_id',
            'route' => 'packlists.show',
            'display_name' => 'Packliste',
            'fields' => ['id', 'internal_id', 'name', 'description'],
        ],
        'label_template' => [
            'table' => 'label_templates',
            'id_field' => 'internal_id',
            'route' => 'labels.showTemplate',
            'display_name' => 'Label-Vorlage',
            'fields' => ['id', 'internal_id', 'name', 'template_type'],
        ],
    ];

    /**
     * Resolve a scanned identifier
     */
    public function resolve(): void
    {
        $this->ensureAuthenticated();

        $identifier = $this->post('identifier', '');

        if (empty($identifier)) {
            $this->error('Identifier is required', [], 400);
            return;
        }

        $identifier = trim($identifier);
        $result = $this->resolveIdentifier($identifier);

        if ($result !== null) {
            $this->audit(
                'scan_api',
                $result['type'],
                $result['id'],
                'Resolved ' . $this->entityConfig[$result['type']]['display_name'] . ' via API',
                ['identifier' => $identifier, 'resolved_at' => now()]
            );
            $this->success($result);
            return;
        }

        // Item not found
        $this->error('Item not found', ['identifier' => $identifier], 404);
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

        // Try to find by internal_id across all entity types
        foreach ($this->entityTypes as $type) {
            $config = $this->entityConfig[$type];
            
            $fields = implode(', ', $config['fields']);
            $entity = $database->selectOne(
                "SELECT {$fields} FROM {$config['table']} " .
                "WHERE {$config['id_field']} = ? AND deleted_at IS NULL",
                [$identifier]
            );

            if ($entity !== null) {
                $displayName = $entity['name'] ?? $entity['internal_id'] ?? $entity['borrower_name'] ?? $identifier;
                return [
                    'type' => $type,
                    'id' => $entity['id'],
                    'internal_id' => $entity['internal_id'] ?? $identifier,
                    'name' => $displayName,
                    'url' => $this->getRouter()->route($config['route'], ['id' => $entity['id']]),
                    'display_type' => $config['display_name'],
                ];
            }
        }

        // Try to find by numeric ID across all entity types
        if (is_numeric($identifier)) {
            $id = (int)$identifier;

            foreach ($this->entityTypes as $type) {
                $config = $this->entityConfig[$type];
                
                $fields = implode(', ', $config['fields']);
                $entity = $database->selectOne(
                    "SELECT {$fields} FROM {$config['table']} " .
                    "WHERE id = ? AND deleted_at IS NULL",
                    [$id]
                );

                if ($entity !== null) {
                    $displayName = $entity['name'] ?? $entity['internal_id'] ?? $entity['borrower_name'] ?? $identifier;
                    return [
                        'type' => $type,
                        'id' => $entity['id'],
                        'internal_id' => $entity['internal_id'] ?? $identifier,
                        'name' => $displayName,
                        'url' => $this->getRouter()->route($config['route'], ['id' => $entity['id']]),
                        'display_type' => $config['display_name'],
                    ];
                }
            }
        }

        // Try to find devices by serial number
        $device = $database->selectOne(
            'SELECT id, internal_id, name, type, serial_number FROM devices WHERE serial_number = ? AND deleted_at IS NULL',
            [$identifier]
        );

        if ($device !== null) {
            return [
                'type' => 'device',
                'id' => $device['id'],
                'internal_id' => $device['internal_id'],
                'name' => $device['name'] ?? $device['internal_id'],
                'url' => $this->getRouter()->route('devices.show', ['id' => $device['id']]),
                'display_type' => 'Gerät',
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
            $this->error('Unauthenticated', [], 401);
        }
    }
}
