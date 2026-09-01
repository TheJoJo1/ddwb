<?php

declare(strict_types=1);

namespace DDWB\Modules\Maintenance\Controllers;

use DDWB\Controller;
use DDWB\Modules\Maintenance\Models\Maintenance;
use DDWB\Modules\Inventory\Models\Device;
use DDWB\Request;
use DDWB\Response;
use DDWB\Validator;

/**
 * Maintenance Controller
 * 
 * Handles maintenance-related HTTP requests (DGUV3)
 */
final class MaintenanceController extends Controller
{
    private Maintenance $maintenanceModel;
    private Device $deviceModel;
    private Validator $validator;

    /**
     * Create a new MaintenanceController instance
     * 
     * @param Maintenance $maintenanceModel The maintenance model
     * @param Device $deviceModel The device model
     * @param Validator $validator The validator
     */
    public function __construct(Maintenance $maintenanceModel, Device $deviceModel, Validator $validator)
    {
        $this->maintenanceModel = $maintenanceModel;
        $this->deviceModel = $deviceModel;
        $this->validator = $validator;
    }

    /**
     * List all maintenance records
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function index(Request $request, Response $response): void
    {
        $filters = [
            'status' => $request->getQuery('status'),
            'device_id' => $request->getQuery('device_id'),
            'search' => $request->getQuery('search'),
        ];

        $page = (int)$request->getQuery('page', 1);
        $perPage = 25;

        $result = $this->maintenanceModel->paginateMaintenance($page, $perPage, array_filter($filters));
        $statistics = $this->maintenanceModel->getStatistics();
        $devices = $this->deviceModel->getAllDevices();

        $this->render('maintenance/index', [
            'maintenanceRecords' => $result['data'],
            'pagination' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'total_pages' => $result['total_pages'],
            ],
            'filters' => $filters,
            'statistics' => $statistics,
            'statusOptions' => Maintenance::getStatusOptions(),
            'typeOptions' => Maintenance::getTypeOptions(),
            'devices' => $devices,
        ]);
    }

    /**
     * Show the create maintenance form
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function create(Request $request, Response $response): void
    {
        $deviceId = (int)$request->getQuery('device_id', 0);
        $device = null;

        if ($deviceId > 0) {
            $device = $this->deviceModel->find($deviceId);
        }

        $devices = $this->deviceModel->getAllDevices();

        $this->render('maintenance/create', [
            'maintenanceData' => [],
            'errors' => [],
            'device' => $device,
            'devices' => $devices,
            'typeOptions' => Maintenance::getTypeOptions(),
        ]);
    }

    /**
     * Store a new maintenance record
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function store(Request $request, Response $response): void
    {
        $data = [
            'device_id' => $request->getPost('device_id'),
            'type' => $request->getPost('type', Maintenance::TYPE_DGUV3),
            'last_inspection_date' => $request->getPost('last_inspection_date'),
            'interval_months' => $request->getPost('interval_months'),
            'next_inspection_date' => $request->getPost('next_inspection_date'),
            'inspector' => $request->getPost('inspector'),
            'notes' => $request->getPost('notes'),
        ];

        $rules = [
            'device_id' => ['required', 'int', 'min:1'],
            'type' => ['required', 'string', 'in:' . implode(',', array_keys(Maintenance::getTypeOptions()))],
            'last_inspection_date' => ['required', 'date'],
            'interval_months' => ['required', 'int', 'min:1'],
            'next_inspection_date' => ['nullable', 'date'],
            'inspector' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];

        $validation = $this->validator->validate($data, $rules);

        if ($validation->fails()) {
            $devices = $this->deviceModel->getAllDevices();
            $this->render('maintenance/create', [
                'maintenanceData' => $data,
                'errors' => $validation->errors(),
                'device' => $this->deviceModel->find((int)$data['device_id']),
                'devices' => $devices,
                'typeOptions' => Maintenance::getTypeOptions(),
            ], 422);
            return;
        }

        try {
            $maintenanceId = $this->maintenanceModel->createMaintenance($data);
            
            $this->addFlash('success', 'Wartungsdatensatz erfolgreich erstellt!');
            $response->redirect("/maintenance/{$maintenanceId}");
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Erstellen des Wartungsdatensatzes: ' . $e->getMessage());
            $response->redirect('/maintenance/create?' . http_build_query($data));
        }
    }

    /**
     * Show a maintenance record
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
            $response->abort(404, 'Wartungsdatensatz nicht gefunden');
            return;
        }

        // Get all maintenance records for this device
        $deviceMaintenance = $this->maintenanceModel->getMaintenanceForDevice($maintenance['device_id']);

        $this->render('maintenance/show', [
            'maintenance' => $maintenance,
            'deviceMaintenance' => $deviceMaintenance,
            'statusOptions' => Maintenance::getStatusOptions(),
            'typeOptions' => Maintenance::getTypeOptions(),
        ]);
    }

    /**
     * Show the edit maintenance form
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function edit(Request $request, Response $response, array $params): void
    {
        $maintenanceId = (int)$params['id'];
        $maintenance = $this->maintenanceModel->getMaintenanceById($maintenanceId);

        if ($maintenance === null) {
            $response->abort(404, 'Wartungsdatensatz nicht gefunden');
            return;
        }

        $devices = $this->deviceModel->getAllDevices();

        $this->render('maintenance/edit', [
            'maintenance' => $maintenance,
            'errors' => [],
            'devices' => $devices,
            'statusOptions' => Maintenance::getStatusOptions(),
            'typeOptions' => Maintenance::getTypeOptions(),
        ]);
    }

    /**
     * Update a maintenance record
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function update(Request $request, Response $response, array $params): void
    {
        $maintenanceId = (int)$params['id'];
        $maintenance = $this->maintenanceModel->getMaintenanceById($maintenanceId);

        if ($maintenance === null) {
            $response->abort(404, 'Wartungsdatensatz nicht gefunden');
            return;
        }

        $data = [
            'device_id' => $request->getPost('device_id'),
            'type' => $request->getPost('type', $maintenance['type']),
            'last_inspection_date' => $request->getPost('last_inspection_date'),
            'interval_months' => $request->getPost('interval_months'),
            'next_inspection_date' => $request->getPost('next_inspection_date'),
            'inspector' => $request->getPost('inspector'),
            'notes' => $request->getPost('notes'),
        ];

        $rules = [
            'device_id' => ['required', 'int', 'min:1'],
            'type' => ['required', 'string', 'in:' . implode(',', array_keys(Maintenance::getTypeOptions()))],
            'last_inspection_date' => ['required', 'date'],
            'interval_months' => ['required', 'int', 'min:1'],
            'next_inspection_date' => ['nullable', 'date'],
            'inspector' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];

        $validation = $this->validator->validate($data, $rules);

        if ($validation->fails()) {
            $devices = $this->deviceModel->getAllDevices();
            $this->render('maintenance/edit', [
                'maintenance' => array_merge($maintenance, $data),
                'errors' => $validation->errors(),
                'devices' => $devices,
                'statusOptions' => Maintenance::getStatusOptions(),
                'typeOptions' => Maintenance::getTypeOptions(),
            ], 422);
            return;
        }

        try {
            $this->maintenanceModel->updateMaintenance($maintenanceId, $data);
            
            $this->addFlash('success', 'Wartungsdatensatz erfolgreich aktualisiert!');
            $response->redirect("/maintenance/{$maintenanceId}");
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Aktualisieren des Wartungsdatensatzes: ' . $e->getMessage());
            $devices = $this->deviceModel->getAllDevices();
            $this->render('maintenance/edit', [
                'maintenance' => array_merge($maintenance, $data),
                'errors' => [],
                'devices' => $devices,
                'statusOptions' => Maintenance::getStatusOptions(),
                'typeOptions' => Maintenance::getTypeOptions(),
            ], 500);
        }
    }

    /**
     * Delete a maintenance record
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function destroy(Request $request, Response $response, array $params): void
    {
        $maintenanceId = (int)$params['id'];
        $maintenance = $this->maintenanceModel->getMaintenanceById($maintenanceId);

        if ($maintenance === null) {
            $response->abort(404, 'Wartungsdatensatz nicht gefunden');
            return;
        }

        try {
            $this->maintenanceModel->deleteMaintenance($maintenanceId);
            
            $this->addFlash('success', 'Wartungsdatensatz erfolgreich gelöscht!');
            $response->redirect('/maintenance');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Löschen des Wartungsdatensatzes: ' . $e->getMessage());
            $response->redirect("/maintenance/{$maintenanceId}");
        }
    }

    /**
     * Show overdue maintenance records
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function overdue(Request $request, Response $response): void
    {
        $filters = [
            'status' => Maintenance::STATUS_OVERDUE,
            'search' => $request->getQuery('search'),
        ];

        $page = (int)$request->getQuery('page', 1);
        $perPage = 25;

        $result = $this->maintenanceModel->paginateMaintenance($page, $perPage, array_filter($filters));
        $statistics = $this->maintenanceModel->getStatistics();

        $this->render('maintenance/overdue', [
            'maintenanceRecords' => $result['data'],
            'pagination' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'total_pages' => $result['total_pages'],
            ],
            'filters' => $filters,
            'statistics' => $statistics,
            'statusOptions' => Maintenance::getStatusOptions(),
        ]);
    }

    /**
     * Show upcoming maintenance records
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function upcoming(Request $request, Response $response): void
    {
        $filters = [
            'status' => Maintenance::STATUS_UPCOMING,
            'search' => $request->getQuery('search'),
        ];

        $page = (int)$request->getQuery('page', 1);
        $perPage = 25;

        $result = $this->maintenanceModel->paginateMaintenance($page, $perPage, array_filter($filters));
        $statistics = $this->maintenanceModel->getStatistics();

        $this->render('maintenance/upcoming', [
            'maintenanceRecords' => $result['data'],
            'pagination' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'total_pages' => $result['total_pages'],
            ],
            'filters' => $filters,
            'statistics' => $statistics,
            'statusOptions' => Maintenance::getStatusOptions(),
        ]);
    }

    /**
     * Update all maintenance statuses
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function updateStatuses(Request $request, Response $response): void
    {
        try {
            $count = $this->maintenanceModel->updateAllStatuses();
            
            $this->addFlash('success', "{$count} Wartungsdatensätze wurden aktualisiert!");
            $response->redirect('/maintenance');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Aktualisieren der Wartungsstatistiken: ' . $e->getMessage());
            $response->redirect('/maintenance');
        }
    }
}
