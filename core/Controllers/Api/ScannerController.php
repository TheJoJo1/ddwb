<?php

declare(strict_types=1);

namespace DDWB\Controllers\Api;

use DDWB\Controller;
use DDWB\Database;

/**
 * API Scanner Controller
 * 
 * Handles API requests for the scanner
 */
final class ScannerController extends Controller
{
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

        $database = $this->getDatabase();

        // Try to find a device with this internal ID
        $device = $database->selectOne(
            'SELECT id, internal_id, name, type FROM devices WHERE internal_id = ? AND deleted_at IS NULL',
            [$identifier]
        );

        if ($device !== null) {
            $this->success([
                'type' => 'device',
                'id' => $device['id'],
                'internal_id' => $device['internal_id'],
                'name' => $device['name'],
                'url' => $this->getRouter()->route('devices.show', ['id' => $device['id']]),
            ]);
            return;
        }

        // Try to find a case with this internal ID
        $case = $database->selectOne(
            'SELECT id, internal_id, name FROM cases WHERE internal_id = ? AND deleted_at IS NULL',
            [$identifier]
        );

        if ($case !== null) {
            $this->success([
                'type' => 'case',
                'id' => $case['id'],
                'internal_id' => $case['internal_id'],
                'name' => $case['name'],
                'url' => $this->getRouter()->route('cases.show', ['id' => $case['id']]),
            ]);
            return;
        }

        // Try to find by numeric ID
        if (is_numeric($identifier)) {
            $id = (int)$identifier;

            $device = $database->selectOne(
                'SELECT id, internal_id, name, type FROM devices WHERE id = ? AND deleted_at IS NULL',
                [$id]
            );

            if ($device !== null) {
                $this->success([
                    'type' => 'device',
                    'id' => $device['id'],
                    'internal_id' => $device['internal_id'],
                    'name' => $device['name'],
                    'url' => $this->getRouter()->route('devices.show', ['id' => $device['id']]),
                ]);
                return;
            }

            $case = $database->selectOne(
                'SELECT id, internal_id, name FROM cases WHERE id = ? AND deleted_at IS NULL',
                [$id]
            );

            if ($case !== null) {
                $this->success([
                    'type' => 'case',
                    'id' => $case['id'],
                    'internal_id' => $case['internal_id'],
                    'name' => $case['name'],
                    'url' => $this->getRouter()->route('cases.show', ['id' => $case['id']]),
                ]);
                return;
            }
        }

        // Item not found
        $this->error('Item not found', [], 404);
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
