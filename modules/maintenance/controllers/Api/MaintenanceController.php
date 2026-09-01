<?php

declare(strict_types=1);

namespace DDWB\Modules\Maintenance\Controllers\Api;

use DDWB\Controller;
use DDWB\Modules\Maintenance\Models\Maintenance;
use DDWB\Request;
use DDWB\Response;

/**
 * API Maintenance Controller
 * 
 * Handles maintenance-related API requests
 */
final class MaintenanceController extends Controller
{
    private Maintenance $maintenanceModel;

    /**
     * Create a new MaintenanceController instance
     * 
     * @param Maintenance $maintenanceModel The maintenance model
     */
    public function __construct(Maintenance $maintenanceModel)
    {
        $this->maintenanceModel = $maintenanceModel;
    }

    /**
     * Get all maintenance records
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function index(Request $request, Response $response): void
    {
        $filters = [
            'status' => $request->getQuery('status'),
            'device_id' => $request->getQuery('device_id'),
        ];

        $maintenance = $this->maintenanceModel->getAllMaintenance(array_filter($filters));

        $response->json([
            'success' => true,
            'data' => $maintenance,
            'count' => count($maintenance),
        ]);
    }

    /**
     * Search maintenance records
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function search(Request $request, Response $response): void
    {
        $query = $request->getPost('query', $request->getQuery('query', ''));
        $filters = [
            'status' => $request->getQuery('status'),
        ];

        if (empty($query)) {
            $response->json([
                'success' => false,
                'error' => 'Query parameter is required',
            ], 400);
            return;
        }

        $maintenance = $this->maintenanceModel->search($query, array_filter($filters));

        $response->json([
            'success' => true,
            'data' => $maintenance,
            'count' => count($maintenance),
        ]);
    }

    /**
     * Get a single maintenance record
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function show(Request $request, Response $response, array $params): void
    {
        $maintenanceId = (int)$params['id'];
        $maintenance = $this->maintenanceModel->getMaintenanceById($maintenanceId);

        if ($maintenance === null) {
            $response->json([
                'success' => false,
                'error' => 'Maintenance record not found',
            ], 404);
            return;
        }

        $response->json([
            'success' => true,
            'data' => $maintenance,
        ]);
    }
}
