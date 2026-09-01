<?php

declare(strict_types=1);

namespace DDWB\Controllers;

use DDWB\Controller;
use DDWB\Database;

/**
 * Dashboard Controller
 * 
 * Handles dashboard-related requests
 */
final class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index(): void
    {
        $this->ensureAuthenticated();

        $database = $this->getDatabase();

        // Get statistics
        $stats = [
            'total_devices' => (int)$database->selectValue(
                'SELECT COUNT(*) FROM devices WHERE deleted_at IS NULL'
            ),
            'available_devices' => (int)$database->selectValue(
                'SELECT COUNT(*) FROM devices WHERE status = ? AND deleted_at IS NULL',
                ['available']
            ),
            'devices_in_cases' => (int)$database->selectValue(
                'SELECT COUNT(*) FROM devices WHERE status = ? AND deleted_at IS NULL',
                ['in_case']
            ),
            'rented_devices' => (int)$database->selectValue(
                'SELECT COUNT(*) FROM devices WHERE status = ? AND deleted_at IS NULL',
                ['lent_out']
            ),
            'devices_in_maintenance' => (int)$database->selectValue(
                'SELECT COUNT(*) FROM devices WHERE status = ? AND deleted_at IS NULL',
                ['maintenance']
            ),
            'total_cases' => (int)$database->selectValue(
                'SELECT COUNT(*) FROM cases WHERE deleted_at IS NULL'
            ),
            'active_rentals' => (int)$database->selectValue(
                'SELECT COUNT(*) FROM rentals WHERE status = ? AND deleted_at IS NULL',
                ['active']
            ),
            'overdue_rentals' => (int)$database->selectValue(
                'SELECT COUNT(*) FROM rentals WHERE status = ? AND deleted_at IS NULL',
                ['overdue']
            ),
        ];

        // Get upcoming maintenance
        $maintenanceConfig = config('maintenance', []);
        $upcomingDays = $maintenanceConfig['upcoming_days'] ?? 30;
        $dueDays = $maintenanceConfig['due_days'] ?? 7;

        $upcomingMaintenance = $database->select(
            'SELECT d.*, m.last_inspection_date, m.next_inspection_date, m.status as maintenance_status ' .
            'FROM devices d ' .
            'LEFT JOIN maintenance m ON d.id = m.device_id ' .
            'WHERE d.deleted_at IS NULL ' .
            'AND m.next_inspection_date IS NOT NULL ' .
            'AND m.next_inspection_date <= DATE_ADD(NOW(), INTERVAL ? DAY) ' .
            'AND m.next_inspection_date > NOW() ' .
            'ORDER BY m.next_inspection_date ASC ' .
            'LIMIT 10',
            [$upcomingDays]
        );

        $overdueMaintenance = $database->select(
            'SELECT d.*, m.last_inspection_date, m.next_inspection_date, m.status as maintenance_status ' .
            'FROM devices d ' .
            'LEFT JOIN maintenance m ON d.id = m.device_id ' .
            'WHERE d.deleted_at IS NULL ' .
            'AND m.next_inspection_date IS NOT NULL ' .
            'AND m.next_inspection_date <= NOW() ' .
            'ORDER BY m.next_inspection_date ASC ' .
            'LIMIT 10',
            []
        );

        // Get recent activity
        $recentLogs = $database->select(
            'SELECT l.*, u.name as user_name, u.email as user_email ' .
            'FROM logs l ' .
            'LEFT JOIN users u ON l.user_id = u.id ' .
            'ORDER BY l.timestamp DESC ' .
            'LIMIT 10'
        );

        $this->view('dashboard', [
            'stats' => $stats,
            'upcoming_maintenance' => $upcomingMaintenance,
            'overdue_maintenance' => $overdueMaintenance,
            'recent_logs' => $recentLogs,
            'title' => 'Dashboard',
        ]);
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
