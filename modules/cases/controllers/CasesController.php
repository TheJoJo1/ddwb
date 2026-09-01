<?php

declare(strict_types=1);

namespace DDWB\Modules\Cases\Controllers;

use DDWB\Controller;
use DDWB\Modules\Cases\Models\CaseModel;
use DDWB\Modules\Inventory\Models\Device;
use DDWB\Request;
use DDWB\Response;
use DDWB\Session;
use DDWB\Validator;

/**
 * Cases Controller
 * 
 * Handles case-related HTTP requests
 */
final class CasesController extends Controller
{
    private CaseModel $caseModel;
    private Device $deviceModel;
    private Validator $validator;

    /**
     * Create a new CasesController instance
     * 
     * @param CaseModel $caseModel The case model
     * @param Device $deviceModel The device model
     * @param Validator $validator The validator
     */
    public function __construct(CaseModel $caseModel, Device $deviceModel, Validator $validator)
    {
        $this->caseModel = $caseModel;
        $this->deviceModel = $deviceModel;
        $this->validator = $validator;
    }

    /**
     * List all cases
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function index(Request $request, Response $response): void
    {
        $filters = [
            'status' => $request->getQuery('status'),
            'search' => $request->getQuery('search'),
        ];

        $page = (int)$request->getQuery('page', 1);
        $perPage = 25;

        $result = $this->caseModel->paginateCases($page, $perPage, array_filter($filters));
        $statistics = $this->caseModel->getStatistics();

        $this->render('cases/index', [
            'cases' => $result['data'],
            'pagination' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'total_pages' => $result['total_pages'],
            ],
            'filters' => $filters,
            'statistics' => $statistics,
            'statusOptions' => CaseModel::getStatusOptions(),
        ]);
    }

    /**
     * Show the create case form
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function create(Request $request, Response $response): void
    {
        $this->render('cases/create', [
            'caseData' => [],
            'errors' => [],
        ]);
    }

    /**
     * Store a new case
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function store(Request $request, Response $response): void
    {
        $data = [
            'internal_id' => $request->getPost('internal_id'),
            'name' => $request->getPost('name'),
            'description' => $request->getPost('description'),
            'status' => $request->getPost('status', CaseModel::STATUS_AVAILABLE),
            'location' => $request->getPost('location'),
            'notes' => $request->getPost('notes'),
        ];

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'internal_id' => ['nullable', 'string', 'max:50', 'unique:cases,internal_id'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:' . implode(',', CaseModel::getValidStatuses())],
            'location' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];

        $validation = $this->validator->validate($data, $rules);

        if ($validation->fails()) {
            $this->render('cases/create', [
                'caseData' => $data,
                'errors' => $validation->errors(),
            ], 422);
            return;
        }

        try {
            $caseId = $this->caseModel->createCase($data);
            
            $this->addFlash('success', 'Case erfolgreich erstellt!');
            $response->redirect("/cases/{$caseId}");
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Erstellen des Cases: ' . $e->getMessage());
            $this->render('cases/create', [
                'caseData' => $data,
                'errors' => [],
            ], 500);
        }
    }

    /**
     * Show a case
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function show(Request $request, Response $response, array $params): void
    {
        $caseId = (int)$params['id'];
        $case = $this->caseModel->getCaseWithDevices($caseId);

        if ($case === null) {
            $response->abort(404, 'Case nicht gefunden');
            return;
        }

        // Get available devices that can be added to this case
        $availableDevices = $this->caseModel->getDevicesNotInCase($caseId);

        $this->render('cases/show', [
            'case' => $case,
            'availableDevices' => $availableDevices,
            'statusOptions' => CaseModel::getStatusOptions(),
        ]);
    }

    /**
     * Show the edit case form
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function edit(Request $request, Response $response, array $params): void
    {
        $caseId = (int)$params['id'];
        $case = $this->caseModel->find($caseId);

        if ($case === null) {
            $response->abort(404, 'Case nicht gefunden');
            return;
        }

        $this->render('cases/edit', [
            'case' => $case,
            'errors' => [],
            'statusOptions' => CaseModel::getStatusOptions(),
        ]);
    }

    /**
     * Update a case
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function update(Request $request, Response $response, array $params): void
    {
        $caseId = (int)$params['id'];
        $case = $this->caseModel->find($caseId);

        if ($case === null) {
            $response->abort(404, 'Case nicht gefunden');
            return;
        }

        $data = [
            'name' => $request->getPost('name'),
            'description' => $request->getPost('description'),
            'status' => $request->getPost('status', $case['status']),
            'location' => $request->getPost('location'),
            'notes' => $request->getPost('notes'),
        ];

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:' . implode(',', CaseModel::getValidStatuses())],
            'location' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];

        $validation = $this->validator->validate($data, $rules);

        if ($validation->fails()) {
            $this->render('cases/edit', [
                'case' => array_merge($case, $data),
                'errors' => $validation->errors(),
                'statusOptions' => CaseModel::getStatusOptions(),
            ], 422);
            return;
        }

        try {
            $this->caseModel->updateCase($caseId, $data);
            
            $this->addFlash('success', 'Case erfolgreich aktualisiert!');
            $response->redirect("/cases/{$caseId}");
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Aktualisieren des Cases: ' . $e->getMessage());
            $this->render('cases/edit', [
                'case' => array_merge($case, $data),
                'errors' => [],
                'statusOptions' => CaseModel::getStatusOptions(),
            ], 500);
        }
    }

    /**
     * Delete a case
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function destroy(Request $request, Response $response, array $params): void
    {
        $caseId = (int)$params['id'];
        $case = $this->caseModel->find($caseId);

        if ($case === null) {
            $response->abort(404, 'Case nicht gefunden');
            return;
        }

        // Check if case has devices
        $devicesInCase = $this->caseModel->getDevicesInCase($caseId);
        if (!empty($devicesInCase)) {
            $this->addFlash('error', 'Case kann nicht gelöscht werden, da noch Geräte enthalten sind!');
            $response->redirect("/cases/{$caseId}");
            return;
        }

        try {
            $this->caseModel->deleteCase($caseId);
            
            $this->addFlash('success', 'Case erfolgreich gelöscht!');
            $response->redirect('/cases');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Löschen des Cases: ' . $e->getMessage());
            $response->redirect("/cases/{$caseId}");
        }
    }

    /**
     * Add device to case
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function addDevice(Request $request, Response $response, array $params): void
    {
        $caseId = (int)$params['id'];
        $case = $this->caseModel->find($caseId);

        if ($case === null) {
            $response->abort(404, 'Case nicht gefunden');
            return;
        }

        $deviceId = (int)$request->getPost('device_id');
        $notes = $request->getPost('notes');

        if ($deviceId <= 0) {
            $this->addFlash('error', 'Bitte wählen Sie ein Gerät aus!');
            $response->redirect("/cases/{$caseId}");
            return;
        }

        try {
            $this->caseModel->addDeviceToCase($caseId, $deviceId, $this->auth()->id(), $notes);
            
            $this->addFlash('success', 'Gerät erfolgreich zum Case hinzugefügt!');
            $response->redirect("/cases/{$caseId}");
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Hinzufügen des Geräts: ' . $e->getMessage());
            $response->redirect("/cases/{$caseId}");
        }
    }

    /**
     * Remove device from case
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function removeDevice(Request $request, Response $response, array $params): void
    {
        $caseId = (int)$params['id'];
        $case = $this->caseModel->find($caseId);

        if ($case === null) {
            $response->abort(404, 'Case nicht gefunden');
            return;
        }

        $deviceId = (int)$params['device_id'];

        try {
            $this->caseModel->removeDeviceFromCase($caseId, $deviceId, $this->auth()->id());
            
            $this->addFlash('success', 'Gerät erfolgreich aus dem Case entfernt!');
            $response->redirect("/cases/{$caseId}");
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Entfernen des Geräts: ' . $e->getMessage());
            $response->redirect("/cases/{$caseId}");
        }
    }

    /**
     * Show QR code for case
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function qr(Request $request, Response $response, array $params): void
    {
        $caseId = (int)$params['id'];
        $case = $this->caseModel->find($caseId);

        if ($case === null) {
            $response->abort(404, 'Case nicht gefunden');
            return;
        }

        $this->render('cases/qr', [
            'case' => $case,
            'qrUrl' => $this->generateQrUrl('case', $caseId),
        ]);
    }

    /**
     * Show barcode for case
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function barcode(Request $request, Response $response, array $params): void
    {
        $caseId = (int)$params['id'];
        $case = $this->caseModel->find($caseId);

        if ($case === null) {
            $response->abort(404, 'Case nicht gefunden');
            return;
        }

        $this->render('cases/barcode', [
            'case' => $case,
            'barcodeData' => $case['internal_id'],
        ]);
    }

    /**
     * Show label for case
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function label(Request $request, Response $response, array $params): void
    {
        $caseId = (int)$params['id'];
        $case = $this->caseModel->find($caseId);

        if ($case === null) {
            $response->abort(404, 'Case nicht gefunden');
            return;
        }

        $this->render('cases/label', [
            'case' => $case,
            'qrUrl' => $this->generateQrUrl('case', $caseId),
            'barcodeData' => $case['internal_id'],
        ]);
    }

    /**
     * Generate QR URL for a case
     * 
     * @param string $type The type (case or device)
     * @param int $id The ID
     * @return string The QR URL
     */
    private function generateQrUrl(string $type, int $id): string
    {
        $app = \DDWB\Application::getInstance();
        $baseUrl = $app->getConfigValue('app.url', 'http://localhost/ddwb');
        
        return $baseUrl . "/scan/{$type}/{$id}";
    }
}
