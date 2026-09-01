<?php

declare(strict_types=1);

namespace DDWB\Modules\Inventory\Controllers;

use DDWB\Controller;
use DDWB\Modules\Inventory\Models\Device as DeviceModel;
use DDWB\Modules\Inventory\Models\Category as CategoryModel;
use DDWB\Modules\Cases\Models\Case as CaseModel;
use DDWB\Modules\Rentals\Models\Rental as RentalModel;
use DDWB\Modules\Maintenance\Models\Maintenance as MaintenanceModel;

/**
 * Devices Controller
 * 
 * Handles device management
 */
final class DevicesController extends Controller
{
    private DeviceModel $deviceModel;
    private CategoryModel $categoryModel;
    private CaseModel $caseModel;
    private RentalModel $rentalModel;
    private MaintenanceModel $maintenanceModel;

    /**
     * Create a new DevicesController instance
     */
    public function __construct()
    {
        parent::__construct($this->container);
        $this->deviceModel = new DeviceModel($this->getDatabase());
        $this->categoryModel = new CategoryModel($this->getDatabase());
        
        // Load other models if needed
        if ($this->isModuleLoaded('cases')) {
            $this->caseModel = new CaseModel($this->getDatabase());
        }
        if ($this->isModuleLoaded('rentals')) {
            $this->rentalModel = new RentalModel($this->getDatabase());
        }
        if ($this->isModuleLoaded('maintenance')) {
            $this->maintenanceModel = new MaintenanceModel($this->getDatabase());
        }
    }

    /**
     * Display the device list
     */
    public function index(): void
    {
        $this->ensureAuthenticated();

        // Get filter parameters
        $page = (int)$this->query('page', 1);
        $perPage = (int)$this->query('per_page', 25);
        $search = trim($this->query('search', ''));
        $status = $this->query('status', '');
        $categoryId = $this->query('category_id', null);

        // Get filters
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

        // Get paginated devices
        $result = $this->deviceModel->paginateDevices($page, $perPage, $filters);

        // Get statistics
        $stats = $this->deviceModel->getStatistics();

        // Get categories for filter
        $categories = $this->categoryModel->getAllCategories(true);

        // Get status options
        $statusOptions = DeviceModel::getStatusOptions();

        $this->view('inventory/devices/index', [
            'devices' => $result['data'],
            'pagination' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'total_pages' => $result['total_pages'],
            ],
            'stats' => $stats,
            'categories' => $categories,
            'statusOptions' => $statusOptions,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'category_id' => $categoryId,
            ],
            'title' => 'Geräte',
        ]);
    }

    /**
     * Show the create device form
     */
    public function create(): void
    {
        $this->ensureAdmin();

        // Get categories for dropdown
        $categoryOptions = $this->categoryModel->getCategoryOptions(true);

        // Get status options
        $statusOptions = DeviceModel::getStatusOptions();

        // Generate next internal ID
        $nextInternalId = $this->deviceModel->generateNextInternalId();

        $this->view('inventory/devices/create', [
            'categoryOptions' => $categoryOptions,
            'statusOptions' => $statusOptions,
            'nextInternalId' => $nextInternalId,
            'title' => 'Gerät erstellen',
        ]);
    }

    /**
     * Store a new device
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
        ])->withMessages([
            'internal_id.required' => 'Bitte geben Sie eine interne ID ein.',
            'internal_id.unique' => 'Diese interne ID ist bereits vergeben.',
            'name.required' => 'Bitte geben Sie einen Gerätenamen ein.',
            'name.min' => 'Der Gerätename muss mindestens 2 Zeichen lang sein.',
            'name.max' => 'Der Gerätename darf maximal 255 Zeichen lang sein.',
            'description.max' => 'Die Beschreibung darf maximal 1000 Zeichen lang sein.',
            'category_id.integer' => 'Bitte wählen Sie eine gültige Kategorie aus.',
            'serial_number.max' => 'Die Seriennummer darf maximal 100 Zeichen lang sein.',
            'status.required' => 'Bitte wählen Sie einen Status aus.',
            'status.in' => 'Bitte wählen Sie einen gültigen Status aus.',
            'location.max' => 'Der Standort darf maximal 100 Zeichen lang sein.',
            'purchase_date.date' => 'Bitte geben Sie ein gültiges Kaufdatum ein.',
            'purchase_price.numeric' => 'Der Kaufpreis muss eine Zahl sein.',
            'purchase_price.min' => 'Der Kaufpreis darf nicht negativ sein.',
            'warranty_expires.date' => 'Bitte geben Sie ein gültiges Garantiedatum ein.',
            'notes.max' => 'Die Notizen dürfen maximal 1000 Zeichen lang sein.',
        ]);

        if (!$validator->validate()) {
            $this->flash('errors', $validator->getErrors());
            $this->flash('old', $data);
            $this->redirectToRoute('devices.create');
        }

        // Create device
        try {
            $deviceId = $this->deviceModel->createDevice($data);

            // Log the action
            $this->audit(
                'create',
                'devices',
                $deviceId,
                'Gerät erstellt: ' . $data['name'],
                ['internal_id' => $data['internal_id'], 'serial_number' => $data['serial_number']]
            );

            $this->flash('success', 'Gerät erfolgreich erstellt.');
            $this->redirectToRoute('devices.show', ['id' => $deviceId]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to create device: {error}', ['error' => $e->getMessage()]);
            $this->flash('error', 'Fehler beim Erstellen des Geräts: ' . $e->getMessage());
            $this->redirectToRoute('devices.create');
        }
    }

    /**
     * Show a device
     * 
     * @param int $id The device ID
     */
    public function show(int $id): void
    {
        $this->ensureAuthenticated();

        $device = $this->deviceModel->find($id);

        if ($device === null) {
            $this->flash('error', 'Gerät nicht gefunden.');
            $this->redirectToRoute('devices');
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

        // Get maintenance records
        $maintenanceRecords = $this->deviceModel->getMaintenanceRecords($device['id']);

        // Get rental history
        $rentalHistory = $this->getDeviceRentalHistory($device['id']);

        // Get case history
        $caseHistory = $this->getDeviceCaseHistory($device['id']);

        $this->view('inventory/devices/show', [
            'device' => $device,
            'category' => $category,
            'case' => $case,
            'rental' => $rental,
            'maintenanceRecords' => $maintenanceRecords,
            'rentalHistory' => $rentalHistory,
            'caseHistory' => $caseHistory,
            'title' => 'Gerät: ' . e($device['name']),
        ]);
    }

    /**
     * Show the edit device form
     * 
     * @param int $id The device ID
     */
    public function edit(int $id): void
    {
        $this->ensureAdmin();

        $device = $this->deviceModel->find($id);

        if ($device === null) {
            $this->flash('error', 'Gerät nicht gefunden.');
            $this->redirectToRoute('devices');
        }

        // Get categories for dropdown
        $categoryOptions = $this->categoryModel->getCategoryOptions(true);

        // Get status options
        $statusOptions = DeviceModel::getStatusOptions();

        $this->view('inventory/devices/edit', [
            'device' => $device,
            'categoryOptions' => $categoryOptions,
            'statusOptions' => $statusOptions,
            'title' => 'Gerät bearbeiten: ' . e($device['name']),
        ]);
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
            $this->flash('error', 'Gerät nicht gefunden.');
            $this->redirectToRoute('devices');
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
        ])->withMessages([
            'name.required' => 'Bitte geben Sie einen Gerätenamen ein.',
            'name.min' => 'Der Gerätename muss mindestens 2 Zeichen lang sein.',
            'name.max' => 'Der Gerätename darf maximal 255 Zeichen lang sein.',
            'description.max' => 'Die Beschreibung darf maximal 1000 Zeichen lang sein.',
            'category_id.integer' => 'Bitte wählen Sie eine gültige Kategorie aus.',
            'serial_number.max' => 'Die Seriennummer darf maximal 100 Zeichen lang sein.',
            'serial_number.unique' => 'Diese Seriennummer ist bereits vergeben.',
            'status.required' => 'Bitte wählen Sie einen Status aus.',
            'status.in' => 'Bitte wählen Sie einen gültigen Status aus.',
            'location.max' => 'Der Standort darf maximal 100 Zeichen lang sein.',
            'purchase_date.date' => 'Bitte geben Sie ein gültiges Kaufdatum ein.',
            'purchase_price.numeric' => 'Der Kaufpreis muss eine Zahl sein.',
            'purchase_price.min' => 'Der Kaufpreis darf nicht negativ sein.',
            'warranty_expires.date' => 'Bitte geben Sie ein gültiges Garantiedatum ein.',
            'notes.max' => 'Die Notizen dürfen maximal 1000 Zeichen lang sein.',
        ]);

        if (!$validator->validate()) {
            $this->flash('errors', $validator->getErrors());
            $this->flash('old', $data);
            $this->redirectToRoute('devices.edit', ['id' => $id]);
        }

        // Update device
        try {
            // Check if status change is valid
            $oldStatus = $device['status'];
            $newStatus = $data['status'];

            if ($oldStatus !== $newStatus) {
                $this->validateStatusChange($device['id'], $oldStatus, $newStatus);
            }

            $this->deviceModel->updateDevice($id, $data);

            // Log the action
            $this->audit(
                'update',
                'devices',
                $id,
                'Gerät aktualisiert: ' . $data['name']
            );

            $this->flash('success', 'Gerät erfolgreich aktualisiert.');
            $this->redirectToRoute('devices.show', ['id' => $id]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to update device: {error}', ['error' => $e->getMessage()]);
            $this->flash('error', 'Fehler beim Aktualisieren des Geräts: ' . $e->getMessage());
            $this->redirectToRoute('devices.edit', ['id' => $id]);
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
            $this->flash('error', 'Gerät nicht gefunden.');
            $this->redirectToRoute('devices');
        }

        // Check if device is in a case
        if ($this->deviceModel->isInCase($device['id'])) {
            $this->flash('error', 'Dieses Gerät befindet sich in einem Case und kann nicht gelöscht werden. Entfernen Sie es zuerst aus dem Case.');
            $this->redirectToRoute('devices.show', ['id' => $id]);
        }

        // Check if device is lent out
        if ($this->deviceModel->isLentOut($device['id'])) {
            $this->flash('error', 'Dieses Gerät ist ausgeliehen und kann nicht gelöscht werden. Beenden Sie zuerst die Ausleihe.');
            $this->redirectToRoute('devices.show', ['id' => $id]);
        }

        // Delete device
        try {
            $this->deviceModel->deleteDevice($device['id']);

            // Log the action
            $this->audit(
                'delete',
                'devices',
                $id,
                'Gerät gelöscht: ' . $device['name']
            );

            $this->flash('success', 'Gerät erfolgreich gelöscht.');
        } catch (\Exception $e) {
            $this->logger->error('Failed to delete device: {error}', ['error' => $e->getMessage()]);
            $this->flash('error', 'Fehler beim Löschen des Geräts: ' . $e->getMessage());
        }

        $this->redirectToRoute('devices');
    }

    /**
     * Generate QR code for a device
     * 
     * @param int $id The device ID
     */
    public function qr(int $id): void
    {
        $this->ensureAuthenticated();

        $device = $this->deviceModel->find($id);

        if ($device === null) {
            $this->notFound('Gerät nicht gefunden');
            return;
        }

        // Generate QR code
        try {
            $qrCode = $this->generateQrCode($device['internal_id']);

            $this->view('inventory/devices/qr', [
                'device' => $device,
                'qrCode' => $qrCode,
                'title' => 'QR-Code für: ' . e($device['name']),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to generate QR code: {error}', ['error' => $e->getMessage()]);
            $this->flash('error', 'Fehler beim Generieren des QR-Codes: ' . $e->getMessage());
            $this->redirectToRoute('devices.show', ['id' => $id]);
        }
    }

    /**
     * Generate barcode for a device
     * 
     * @param int $id The device ID
     */
    public function barcode(int $id): void
    {
        $this->ensureAuthenticated();

        $device = $this->deviceModel->find($id);

        if ($device === null) {
            $this->notFound('Gerät nicht gefunden');
            return;
        }

        // Generate barcode
        try {
            $barcode = $this->generateBarcode($device['internal_id']);

            $this->view('inventory/devices/barcode', [
                'device' => $device,
                'barcode' => $barcode,
                'title' => 'Barcode für: ' . e($device['name']),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to generate barcode: {error}', ['error' => $e->getMessage()]);
            $this->flash('error', 'Fehler beim Generieren des Barcodes: ' . $e->getMessage());
            $this->redirectToRoute('devices.show', ['id' => $id]);
        }
    }

    /**
     * Generate label for a device
     * 
     * @param int $id The device ID
     */
    public function label(int $id): void
    {
        $this->ensureAuthenticated();

        $device = $this->deviceModel->find($id);

        if ($device === null) {
            $this->notFound('Gerät nicht gefunden');
            return;
        }

        // Generate label
        try {
            $qrCode = $this->generateQrCode($device['internal_id']);
            $barcode = $this->generateBarcode($device['internal_id']);

            $this->view('inventory/devices/label', [
                'device' => $device,
                'qrCode' => $qrCode,
                'barcode' => $barcode,
                'title' => 'Label für: ' . e($device['name']),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to generate label: {error}', ['error' => $e->getMessage()]);
            $this->flash('error', 'Fehler beim Generieren des Labels: ' . $e->getMessage());
            $this->redirectToRoute('devices.show', ['id' => $id]);
        }
    }

    /**
     * Ensure the current user is authenticated
     */
    private function ensureAuthenticated(): void
    {
        if (!$this->isAuthenticated()) {
            $this->redirectToRoute('login');
        }
    }

    /**
     * Ensure the current user is an admin
     */
    private function ensureAdmin(): void
    {
        if (!$this->isAdmin()) {
            $this->forbidden('Keine Berechtigung für diese Aktion.');
        }
    }

    /**
     * Check if a module is loaded
     * 
     * @param string $module The module name
     * @return bool True if the module is loaded
     */
    private function isModuleLoaded(string $module): bool
    {
        $modules = config('modules', []);
        return in_array($module, $modules, true);
    }

    /**
     * Validate status change
     * 
     * @param int $deviceId The device ID
     * @param string $oldStatus The old status
     * @param string $newStatus The new status
     */
    private function validateStatusChange(int $deviceId, string $oldStatus, string $newStatus): void
    {
        // Prevent changing to in_case if device is lent out
        if ($newStatus === DeviceModel::STATUS_IN_CASE && $this->deviceModel->isLentOut($deviceId)) {
            throw new \RuntimeException('Ein ausgeliehenes Gerät kann nicht in einen Case verschoben werden.');
        }

        // Prevent changing to lent_out if device is in maintenance
        if ($newStatus === DeviceModel::STATUS_LENT_OUT && $this->deviceModel->isInMaintenance($deviceId)) {
            throw new \RuntimeException('Ein Gerät in Wartung kann nicht ausgeliehen werden.');
        }

        // Prevent changing to available if device is in a case
        if ($newStatus === DeviceModel::STATUS_AVAILABLE && $this->deviceModel->isInCase($deviceId)) {
            throw new \RuntimeException('Ein Gerät in einem Case kann nicht als verfügbar markiert werden.');
        }

        // Prevent changing to available if device is lent out
        if ($newStatus === DeviceModel::STATUS_AVAILABLE && $this->deviceModel->isLentOut($deviceId)) {
            throw new \RuntimeException('Ein ausgeliehenes Gerät kann nicht als verfügbar markiert werden.');
        }
    }

    /**
     * Get device rental history
     * 
     * @param int $deviceId The device ID
     * @return array The rental history
     */
    private function getDeviceRentalHistory(int $deviceId): array
    {
        if (!$this->isModuleLoaded('rentals')) {
            return [];
        }

        return $this->database->select(
            'SELECT r.*, u.name as user_name ' .
            'FROM rentals r ' .
            'LEFT JOIN users u ON r.created_by = u.id ' .
            'WHERE r.device_id = ? AND r.deleted_at IS NULL ' .
            'ORDER BY r.date_out DESC',
            [$deviceId]
        );
    }

    /**
     * Get device case history
     * 
     * @param int $deviceId The device ID
     * @return array The case history
     */
    private function getDeviceCaseHistory(int $deviceId): array
    {
        if (!$this->isModuleLoaded('cases')) {
            return [];
        }

        return $this->database->select(
            'SELECT cd.*, c.name as case_name, u.name as user_name ' .
            'FROM case_device cd ' .
            'JOIN cases c ON cd.case_id = c.id ' .
            'LEFT JOIN users u ON cd.assigned_by = u.id ' .
            'WHERE cd.device_id = ? ' .
            'ORDER BY cd.assigned_at DESC',
            [$deviceId]
        );
    }

    /**
     * Generate QR code for a device
     * 
     * @param string $identifier The device identifier
     * @return string The QR code HTML
     */
    private function generateQrCode(string $identifier): string
    {
        try {
            $qrCodeConfig = config('qr_code', []);
            $size = $qrCodeConfig['size'] ?? 200;
            $margin = $qrCodeConfig['margin'] ?? 10;
            $foregroundColor = $qrCodeConfig['foreground_color'] ?? ['r' => 0, 'g' => 0, 'b' => 0];
            $backgroundColor = $qrCodeConfig['background_color'] ?? ['r' => 255, 'g' => 255, 'b' => 255];

            $qrCode = new \Endroid\QrCode\QrCode($identifier);
            $qrCode->setSize($size);
            $qrCode->setMargin($margin);
            $qrCode->setForegroundColor($foregroundColor['r'], $foregroundColor['g'], $foregroundColor['b']);
            $qrCode->setBackgroundColor($backgroundColor['r'], $backgroundColor['g'], $backgroundColor['b']);

            $writer = new \Endroid\QrCode\Writer\HtmlWriter();
            return $writer->write($qrCode);
        } catch (\Exception $e) {
            $this->logger->error('Failed to generate QR code: {error}', ['error' => $e->getMessage()]);
            return '<div class="qr-placeholder">QR-Code konnte nicht generiert werden</div>';
        }
    }

    /**
     * Generate barcode for a device
     * 
     * @param string $identifier The device identifier
     * @return string The barcode HTML
     */
    private function generateBarcode(string $identifier): string
    {
        try {
            $barcodeConfig = config('barcode', []);
            $type = $barcodeConfig['type'] ?? 'code128';
            $width = $barcodeConfig['width'] ?? 2;
            $height = $barcodeConfig['height'] ?? 50;

            $generator = new \Picqer\PHPBarcodeGenerator\BarcodeGeneratorPNG();
            $barcodeData = $generator->getBarcode($identifier, $type, $width, $height);
            $barcodeBase64 = base64_encode($barcodeData);

            return '<img src="data:image/png;base64,' . $barcodeBase64 . '" alt="Barcode: ' . e($identifier) . '" />';
        } catch (\Exception $e) {
            $this->logger->error('Failed to generate barcode: {error}', ['error' => $e->getMessage()]);
            return '<div class="barcode-placeholder">Barcode konnte nicht generiert werden</div>';
        }
    }
}
