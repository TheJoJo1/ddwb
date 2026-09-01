<?php

declare(strict_types=1);

namespace DDWB\Modules\Inventory\Controllers\Api;

use DDWB\Controller;
use DDWB\Modules\Inventory\Models\Device as DeviceModel;
use DDWB\Modules\Inventory\Models\Category as CategoryModel;

/**
 * API Devices Controller
 * 
 * Handles API requests for device management
 */
final class DevicesController extends Controller
{
    private DeviceModel $deviceModel;
    private CategoryModel $categoryModel;

    /**
     * Create a new DevicesController instance
     */
    public function __construct()
    {
        parent::__construct($this->container);
        $this->deviceModel = new DeviceModel($this->getDatabase());
        $this->categoryModel = new CategoryModel($this->getDatabase());
    }

    /**
     * Get all devices
     */
    public function index(): void
    {
        $this->ensureAuthenticated();

        $page = (int)$this->query('page', 1);
        $perPage = (int)$this->query('per_page', 25);
        $search = trim($this->query('search', ''));
        $status = $this->query('status', '');
        $categoryId = $this->query('category_id', null);

        $filters = [];
        if (!empty($search)) {
            $filters['search'] = $search;
        }
        if (!empty($status)) {
            $filters['status'] = $status;
        }
        if ($categoryId !== null) {
            $filters['category_id'] = (int)$categoryId;
        }

        $result = $this->deviceModel->paginateDevices($page, $perPage, $filters);

        // Remove sensitive data from devices
        foreach ($result['data'] as &$device) {
            unset($device['purchase_price']);
        }

        $this->json([
            'success' => true,
            'data' => $result['data'],
            'pagination' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'total_pages' => $result['total_pages'],
            ],
        ]);
    }

    /**
     * Get a specific device
     * 
     * @param int $id The device ID
     */
    public function show(int $id): void
    {
        $this->ensureAuthenticated();

        $device = $this->deviceModel->find($id);

        if ($device === null) {
            $this->notFound('Device not found');
            return;
        }

        // Get category
        $category = null;
        if ($device['category_id']) {
            $category = $this->categoryModel->getCategoryById($device['category_id']);
        }

        // Get case if in case
        $case = null;
        if ($device['status'] === DeviceModel::STATUS_IN_CASE) {
            $case = $this->deviceModel->getCase($device['id']);
        }

        // Get rental if lent out
        $rental = null;
        if ($device['status'] === DeviceModel::STATUS_LENT_OUT) {
            $rental = $this->deviceModel->getRental($device['id']);
        }

        // Remove sensitive data
        unset($device['purchase_price']);

        $this->json([
            'success' => true,
            'data' => [
                'device' => $device,
                'category' => $category,
                'case' => $case,
                'rental' => $rental,
            ],
        ]);
    }

    /**
     * Search devices
     */
    public function search(): void
    {
        $this->ensureAuthenticated();

        $query = trim($this->post('query', ''));
        $status = $this->post('status', '');
        $categoryId = $this->post('category_id', null);

        if (empty($query)) {
            $this->error('Query parameter is required', [], 400);
            return;
        }

        $filters = [];
        if (!empty($status)) {
            $filters['status'] = $status;
        }
        if ($categoryId !== null) {
            $filters['category_id'] = (int)$categoryId;
        }

        $devices = $this->deviceModel->search($query, $filters);

        // Remove sensitive data
        foreach ($devices as &$device) {
            unset($device['purchase_price']);
        }

        $this->json([
            'success' => true,
            'data' => $devices,
        ]);
    }

    /**
     * Create a new device
     */
    public function store(): void
    {
        $this->ensureAdmin();

        $data = [
            'internal_id' => trim($this->post('internal_id', ''));
            'name' => trim($this->post('name', ''));
            'description' => trim($this->post('description', ''));
            'category_id' => $this->post('category_id', null);
            'serial_number' => trim($this->post('serial_number', ''));
            'status' => $this->post('status', DeviceModel::STATUS_AVAILABLE);
            'location' => trim($this->post('location', ''));
            'purchase_date' => $this->post('purchase_date', null);
            'purchase_price' => $this->post('purchase_price', null);
            'warranty_expires' => $this->post('warranty_expires', null);
            'notes' => trim($this->post('notes', ''));
        ];

        // Validate input
        $validator = $this->validator->withData($data)->withRules([
            'internal_id' => 'required|unique:devices',
            'name' => 'required|min:2|max:255',
            'description' => 'max:1000',
            'category_id' => 'nullable|integer',
            'serial_number' => 'max:100',
            'status' => 'required|in:' . implode(',', DeviceModel::getValidStatuses()),
            'location' => 'max:100',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'warranty_expires' => 'nullable|date',
            'notes' => 'max:1000',
        ]);

        if (!$validator->validate()) {
            $this->validationError($validator->getErrors());
            return;
        }

        // Create device
        try {
            $deviceId = $this->deviceModel->createDevice($data);
            $device = $this->deviceModel->find($deviceId);

            // Remove sensitive data
            unset($device['purchase_price']);

            // Log the action
            $this->audit(
                'create',
                'devices',
                $deviceId,
                'Device created via API: ' . $data['name']
            );

            $this->json([
                'success' => true,
                'message' => 'Device created successfully',
                'data' => $device,
            ], 201);
        } catch (\Exception $e) {
            $this->logger->error('Failed to create device via API: {error}', ['error' => $e->getMessage()]);
            $this->error('Failed to create device: ' . $e->getMessage());
        }
    }

    /**
     * Update a device
     * 
     * @param int $id The device ID
     */
    public function update(int $id): void
    {
        $this->ensureAdmin();

        $device = $this->deviceModel->find($id);

        if ($device === null) {
            $this->notFound('Device not found');
            return;
        }

        $data = [
            'name' => trim($this->post('name', ''));
            'description' => trim($this->post('description', ''));
            'category_id' => $this->post('category_id', null);
            'serial_number' => trim($this->post('serial_number', ''));
            'status' => $this->post('status', DeviceModel::STATUS_AVAILABLE);
            'location' => trim($this->post('location', ''));
            'purchase_date' => $this->post('purchase_date', null);
            'purchase_price' => $this->post('purchase_price', null);
            'warranty_expires' => $this->post('warranty_expires', null);
            'notes' => trim($this->post('notes', ''));
        ];

        // Validate input
        $validator = $this->validator->withData($data)->withRules([
            'name' => 'required|min:2|max:255',
            'description' => 'max:1000',
            'category_id' => 'nullable|integer',
            'serial_number' => 'max:100|unique:devices,serial_number,' . $id,
            'status' => 'required|in:' . implode(',', DeviceModel::getValidStatuses()),
            'location' => 'max:100',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'warranty_expires' => 'nullable|date',
            'notes' => 'max:1000',
        ]);

        if (!$validator->validate()) {
            $this->validationError($validator->getErrors());
            return;
        }

        // Update device
        try {
            $this->deviceModel->updateDevice($id, $data);
            $updatedDevice = $this->deviceModel->find($id);

            // Remove sensitive data
            unset($updatedDevice['purchase_price']);

            // Log the action
            $this->audit(
                'update',
                'devices',
                $id,
                'Device updated via API: ' . $data['name']
            );

            $this->json([
                'success' => true,
                'message' => 'Device updated successfully',
                'data' => $updatedDevice,
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to update device via API: {error}', ['error' => $e->getMessage()]);
            $this->error('Failed to update device: ' . $e->getMessage());
        }
    }

    /**
     * Delete a device
     * 
     * @param int $id The device ID
     */
    public function destroy(int $id): void
    {
        $this->ensureAdmin();

        $device = $this->deviceModel->find($id);

        if ($device === null) {
            $this->notFound('Device not found');
            return;
        }

        // Check if device can be deleted
        if ($this->deviceModel->isInCase($device['id'])) {
            $this->error('Cannot delete device: Device is in a case', [], 400);
            return;
        }

        if ($this->deviceModel->isLentOut($device['id'])) {
            $this->error('Cannot delete device: Device is lent out', [], 400);
            return;
        }

        // Delete device
        try {
            $this->deviceModel->deleteDevice($device['id']);

            // Log the action
            $this->audit(
                'delete',
                'devices',
                $id,
                'Device deleted via API: ' . $device['name']
            );

            $this->json([
                'success' => true,
                'message' => 'Device deleted successfully',
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to delete device via API: {error}', ['error' => $e->getMessage()]);
            $this->error('Failed to delete device: ' . $e->getMessage());
        }
    }

    /**
     * Get device statistics
     */
    public function statistics(): void
    {
        $this->ensureAuthenticated();

        $stats = $this->deviceModel->getStatistics();

        $this->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Ensure the current user is authenticated
     */
    private function ensureAuthenticated(): void
    {
        if (!$this->isAuthenticated()) {
            $this->error('Unauthenticated', [], 401);
        }
    }

    /**
     * Ensure the current user is an admin
     */
    private function ensureAdmin(): void
    {
        if (!$this->isAdmin()) {
            $this->forbidden('Insufficient permissions');
        }
    }
}
