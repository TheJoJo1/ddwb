<?php

declare(strict_types=1);

namespace DDWB\Controllers;

use DDWB\Controller;
use DDWB\Database;

/**
 * Scanner Controller
 * 
 * Handles scanner-related requests
 */
final class ScannerController extends Controller
{
    /**
     * Show the scanner interface
     */
    public function showScanner(): void
    {
        $this->ensureAuthenticated();

        $this->view('scanner/scanner', [
            'title' => 'Scanner',
            'csrf_token' => $this->getCsrf()->getToken(),
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

        $database = $this->getDatabase();

        // Try to find a device with this internal ID
        $device = $database->selectOne(
            'SELECT * FROM devices WHERE internal_id = ? AND deleted_at IS NULL',
            [$identifier]
        );

        if ($device !== null) {
            $this->redirectToRoute('devices.show', ['id' => $device['id']]);
            return;
        }

        // Try to find a case with this internal ID
        $case = $database->selectOne(
            'SELECT * FROM cases WHERE internal_id = ? AND deleted_at IS NULL',
            [$identifier]
        );

        if ($case !== null) {
            $this->redirectToRoute('cases.show', ['id' => $case['id']]);
            return;
        }

        // Try to find a device or case by ID
        $item = $database->selectOne(
            'SELECT id, internal_id FROM devices WHERE id = ? AND deleted_at IS NULL UNION ALL SELECT id, internal_id FROM cases WHERE id = ? AND deleted_at IS NULL',
            [$identifier, $identifier]
        );

        if ($item !== null) {
            // Check if it's a device or case by trying to find it
            $deviceCheck = $database->selectOne(
                'SELECT id FROM devices WHERE id = ? AND deleted_at IS NULL',
                [$identifier]
            );

            if ($deviceCheck !== null) {
                $this->redirectToRoute('devices.show', ['id' => $identifier]);
                return;
            }

            $caseCheck = $database->selectOne(
                'SELECT id FROM cases WHERE id = ? AND deleted_at IS NULL',
                [$identifier]
            );

            if ($caseCheck !== null) {
                $this->redirectToRoute('cases.show', ['id' => $identifier]);
                return;
            }
        }

        // Item not found
        $this->flash('error', 'Item with identifier "' . e($identifier) . '" not found.');
        $this->redirectToRoute('scanner');
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
