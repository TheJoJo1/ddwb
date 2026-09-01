<?php

declare(strict_types=1);

namespace DDWB\Modules\Rentals\Controllers;

use DDWB\Controller;
use DDWB\Modules\Rentals\Models\Rental;
use DDWB\Modules\Inventory\Models\Device;
use DDWB\Modules\Cases\Models\CaseModel;
use DDWB\Request;
use DDWB\Response;
use DDWB\Validator;

/**
 * Rentals Controller
 * 
 * Handles rental-related HTTP requests
 */
final class RentalsController extends Controller
{
    private Rental $rentalModel;
    private Device $deviceModel;
    private CaseModel $caseModel;
    private Validator $validator;

    /**
     * Create a new RentalsController instance
     * 
     * @param Rental $rentalModel The rental model
     * @param Device $deviceModel The device model
     * @param CaseModel $caseModel The case model
     * @param Validator $validator The validator
     */
    public function __construct(Rental $rentalModel, Device $deviceModel, CaseModel $caseModel, Validator $validator)
    {
        $this->rentalModel = $rentalModel;
        $this->deviceModel = $deviceModel;
        $this->caseModel = $caseModel;
        $this->validator = $validator;
    }

    /**
     * List all rentals
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function index(Request $request, Response $response): void
    {
        $filters = [
            'status' => $request->getQuery('status'),
            'borrower' => $request->getQuery('borrower'),
            'search' => $request->getQuery('search'),
        ];

        $page = (int)$request->getQuery('page', 1);
        $perPage = 25;

        $result = $this->rentalModel->paginateRentals($page, $perPage, array_filter($filters));
        $statistics = $this->rentalModel->getStatistics();

        $this->render('rentals/index', [
            'rentals' => $result['data'],
            'pagination' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'total_pages' => $result['total_pages'],
            ],
            'filters' => $filters,
            'statistics' => $statistics,
            'statusOptions' => Rental::getStatusOptions(),
        ]);
    }

    /**
     * Show the create rental form
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function create(Request $request, Response $response): void
    {
        $deviceId = (int)$request->getQuery('device_id', 0);
        $caseId = (int)$request->getQuery('case_id', 0);

        $device = null;
        $case = null;

        if ($deviceId > 0) {
            $device = $this->deviceModel->find($deviceId);
        }

        if ($caseId > 0) {
            $case = $this->caseModel->find($caseId);
            // If case is selected, get all devices in the case
            if ($case !== null) {
                $devicesInCase = $this->caseModel->getDevicesInCase($caseId);
            }
        }

        // Get available devices
        $availableDevices = $this->deviceModel->getAvailableDevices();
        $availableCases = $this->caseModel->getAllCases(['status' => 'available']);

        $this->render('rentals/create', [
            'rentalData' => [],
            'errors' => [],
            'device' => $device,
            'case' => $case,
            'availableDevices' => $availableDevices,
            'availableCases' => $availableCases,
            'devicesInCase' => $devicesInCase ?? [],
        ]);
    }

    /**
     * Store a new rental
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function store(Request $request, Response $response): void
    {
        $data = [
            'device_id' => $request->getPost('device_id'),
            'case_id' => $request->getPost('case_id'),
            'borrower' => $request->getPost('borrower'),
            'borrower_email' => $request->getPost('borrower_email'),
            'borrower_phone' => $request->getPost('borrower_phone'),
            'date_out' => $request->getPost('date_out'),
            'expected_return' => $request->getPost('expected_return'),
            'notes' => $request->getPost('notes'),
        ];

        $rules = [
            'borrower' => ['required', 'string', 'max:255'],
            'borrower_email' => ['nullable', 'email'],
            'borrower_phone' => ['nullable', 'string', 'max:50'],
            'date_out' => ['required', 'date'],
            'expected_return' => ['required', 'date', 'after:date_out'],
            'notes' => ['nullable', 'string'],
        ];

        // Either device_id or case_id is required
        if ((empty($data['device_id']) || $data['device_id'] <= 0) && 
            (empty($data['case_id']) || $data['case_id'] <= 0)) {
            $this->addFlash('error', 'Bitte wählen Sie ein Gerät oder einen Case aus!');
            $response->redirect('/rentals/create');
            return;
        }

        $validation = $this->validator->validate($data, $rules);

        if ($validation->fails()) {
            $this->addFlash('error', 'Bitte korrigieren Sie die Fehler im Formular!');
            $response->redirect('/rentals/create?' . http_build_query($data));
            return;
        }

        // Validate that the selected item can be lent out
        $deviceId = $data['device_id'] ?? null;
        $caseId = $data['case_id'] ?? null;
        
        $validation = $this->rentalModel->validateLendable($deviceId, $caseId);
        if (!$validation['valid']) {
            $this->addFlash('error', $validation['message']);
            $response->redirect('/rentals/create?' . http_build_query($data));
            return;
        }

        try {
            $rentalId = $this->rentalModel->createRental($data);
            
            $this->addFlash('success', 'Ausleihe erfolgreich erstellt!');
            $response->redirect("/rentals/{$rentalId}");
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Erstellen der Ausleihe: ' . $e->getMessage());
            $response->redirect('/rentals/create?' . http_build_query($data));
        }
    }

    /**
     * Show a rental
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function show(Request $request, Response $response, array $params): void
    {
        $rentalId = (int)$params['id'];
        $rental = $this->rentalModel->getRentalById($rentalId);

        if ($rental === null) {
            $response->abort(404, 'Ausleihe nicht gefunden');
            return;
        }

        // Get devices in case if this is a case rental
        $devicesInCase = [];
        if ($rental['case_id'] > 0) {
            $devicesInCase = $this->caseModel->getDevicesInCase($rental['case_id']);
        }

        $this->render('rentals/show', [
            'rental' => $rental,
            'devicesInCase' => $devicesInCase,
            'statusOptions' => Rental::getStatusOptions(),
        ]);
    }

    /**
     * Show the edit rental form
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function edit(Request $request, Response $response, array $params): void
    {
        $rentalId = (int)$params['id'];
        $rental = $this->rentalModel->getRentalById($rentalId);

        if ($rental === null) {
            $response->abort(404, 'Ausleihe nicht gefunden');
            return;
        }

        // Only allow editing of active rentals
        if ($rental['status'] !== Rental::STATUS_ACTIVE) {
            $this->addFlash('error', 'Nur aktive Ausleihen können bearbeitet werden!');
            $response->redirect("/rentals/{$rentalId}");
            return;
        }

        $this->render('rentals/edit', [
            'rental' => $rental,
            'errors' => [],
        ]);
    }

    /**
     * Update a rental
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function update(Request $request, Response $response, array $params): void
    {
        $rentalId = (int)$params['id'];
        $rental = $this->rentalModel->getRentalById($rentalId);

        if ($rental === null) {
            $response->abort(404, 'Ausleihe nicht gefunden');
            return;
        }

        $data = [
            'borrower' => $request->getPost('borrower'),
            'borrower_email' => $request->getPost('borrower_email'),
            'borrower_phone' => $request->getPost('borrower_phone'),
            'expected_return' => $request->getPost('expected_return'),
            'notes' => $request->getPost('notes'),
        ];

        $rules = [
            'borrower' => ['required', 'string', 'max:255'],
            'borrower_email' => ['nullable', 'email'],
            'borrower_phone' => ['nullable', 'string', 'max:50'],
            'expected_return' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];

        $validation = $this->validator->validate($data, $rules);

        if ($validation->fails()) {
            $this->render('rentals/edit', [
                'rental' => array_merge($rental, $data),
                'errors' => $validation->errors(),
            ], 422);
            return;
        }

        try {
            $this->rentalModel->updateRental($rentalId, $data);
            
            $this->addFlash('success', 'Ausleihe erfolgreich aktualisiert!');
            $response->redirect("/rentals/{$rentalId}");
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Aktualisieren der Ausleihe: ' . $e->getMessage());
            $this->render('rentals/edit', [
                'rental' => array_merge($rental, $data),
                'errors' => [],
            ], 500);
        }
    }

    /**
     * Return a rental
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function returnRental(Request $request, Response $response, array $params): void
    {
        $rentalId = (int)$params['id'];
        $rental = $this->rentalModel->getRentalById($rentalId);

        if ($rental === null) {
            $response->abort(404, 'Ausleihe nicht gefunden');
            return;
        }

        // Only allow returning of active rentals
        if ($rental['status'] !== Rental::STATUS_ACTIVE) {
            $this->addFlash('error', 'Nur aktive Ausleihen können zurückgegeben werden!');
            $response->redirect("/rentals/{$rentalId}");
            return;
        }

        $data = [
            'notes' => $request->getPost('notes'),
        ];

        try {
            $this->rentalModel->returnRental($rentalId, $data);
            
            $this->addFlash('success', 'Ausleihe erfolgreich zurückgegeben!');
            $response->redirect("/rentals/{$rentalId}");
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Zurückgeben der Ausleihe: ' . $e->getMessage());
            $response->redirect("/rentals/{$rentalId}");
        }
    }

    /**
     * Delete a rental
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function destroy(Request $request, Response $response, array $params): void
    {
        $rentalId = (int)$params['id'];
        $rental = $this->rentalModel->getRentalById($rentalId);

        if ($rental === null) {
            $response->abort(404, 'Ausleihe nicht gefunden');
            return;
        }

        try {
            $this->rentalModel->deleteRental($rentalId);
            
            $this->addFlash('success', 'Ausleihe erfolgreich gelöscht!');
            $response->redirect('/rentals');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Löschen der Ausleihe: ' . $e->getMessage());
            $response->redirect("/rentals/{$rentalId}");
        }
    }

    /**
     * Extend a rental
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function extend(Request $request, Response $response, array $params): void
    {
        $rentalId = (int)$params['id'];
        $rental = $this->rentalModel->getRentalById($rentalId);

        if ($rental === null) {
            $response->abort(404, 'Ausleihe nicht gefunden');
            return;
        }

        $newExpectedReturn = $request->getPost('expected_return');

        if (empty($newExpectedReturn)) {
            $this->addFlash('error', 'Bitte geben Sie ein neues Rückgabedatum an!');
            $response->redirect("/rentals/{$rentalId}");
            return;
        }

        try {
            $this->rentalModel->updateRental($rentalId, [
                'expected_return' => $newExpectedReturn,
                'notes' => $rental['notes'] . "\n\nVerlängert am: " . date('Y-m-d H:i:s'),
            ]);
            
            $this->addFlash('success', 'Ausleihe erfolgreich verlängert!');
            $response->redirect("/rentals/{$rentalId}");
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Verlängern der Ausleihe: ' . $e->getMessage());
            $response->redirect("/rentals/{$rentalId}");
        }
    }
}
