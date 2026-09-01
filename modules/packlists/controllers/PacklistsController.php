<?php

declare(strict_types=1);

namespace DDWB\Modules\Packlists\Controllers;

use DDWB\Controller;
use DDWB\Modules\Packlists\Models\Packlist;
use DDWB\Modules\Inventory\Models\Device;
use DDWB\Modules\Cases\Models\CaseModel;
use DDWB\Request;
use DDWB\Response;
use DDWB\Validator;

/**
 * Packlists Controller
 * 
 * Handles packlist-related HTTP requests
 */
final class PacklistsController extends Controller
{
    private Packlist $packlistModel;
    private Device $deviceModel;
    private CaseModel $caseModel;
    private Validator $validator;

    /**
     * Create a new PacklistsController instance
     * 
     * @param Packlist $packlistModel The packlist model
     * @param Device $deviceModel The device model
     * @param CaseModel $caseModel The case model
     * @param Validator $validator The validator
     */
    public function __construct(Packlist $packlistModel, Device $deviceModel, CaseModel $caseModel, Validator $validator)
    {
        $this->packlistModel = $packlistModel;
        $this->deviceModel = $deviceModel;
        $this->caseModel = $caseModel;
        $this->validator = $validator;
    }

    /**
     * List all packlists
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

        $result = $this->packlistModel->paginatePacklists($page, $perPage, array_filter($filters));
        $statistics = $this->packlistModel->getStatistics();

        $this->render('packlists/index', [
            'packlists' => $result['data'],
            'pagination' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'total_pages' => $result['total_pages'],
            ],
            'filters' => $filters,
            'statistics' => $statistics,
            'statusOptions' => Packlist::getStatusOptions(),
        ]);
    }

    /**
     * Show the create packlist form
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function create(Request $request, Response $response): void
    {
        $availableDevices = $this->packlistModel->getAvailableDevices();
        $availableCases = $this->packlistModel->getAvailableCases();

        $this->render('packlists/create', [
            'packlistData' => [],
            'errors' => [],
            'availableDevices' => $availableDevices,
            'availableCases' => $availableCases,
            'statusOptions' => Packlist::getStatusOptions(),
            'itemTypeOptions' => Packlist::getItemTypeOptions(),
        ]);
    }

    /**
     * Store a new packlist
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function store(Request $request, Response $response): void
    {
        $data = [
            'name' => $request->getPost('name'),
            'description' => $request->getPost('description'),
            'status' => $request->getPost('status', Packlist::STATUS_DRAFT),
            'notes' => $request->getPost('notes'),
        ];

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:' . implode(',', Packlist::getValidStatuses())],
            'notes' => ['nullable', 'string'],
        ];

        $validation = $this->validator->validate($data, $rules);

        if ($validation->fails()) {
            $availableDevices = $this->packlistModel->getAvailableDevices();
            $availableCases = $this->packlistModel->getAvailableCases();

            $this->render('packlists/create', [
                'packlistData' => $data,
                'errors' => $validation->errors(),
                'availableDevices' => $availableDevices,
                'availableCases' => $availableCases,
                'statusOptions' => Packlist::getStatusOptions(),
                'itemTypeOptions' => Packlist::getItemTypeOptions(),
            ], 422);
            return;
        }

        try {
            $packlistId = $this->packlistModel->createPacklist($data);
            
            // Add items if provided
            $itemType = $request->getPost('item_type');
            $itemId = $request->getPost('item_id');
            $quantity = $request->getPost('quantity', 1);
            $itemNotes = $request->getPost('item_notes');

            if ($itemType && $itemId) {
                $this->packlistModel->addItemToPacklist(
                    $packlistId,
                    $itemType,
                    (int)$itemId,
                    (int)$quantity,
                    $itemNotes
                );
            }

            $this->addFlash('success', 'Packliste erfolgreich erstellt!');
            $response->redirect("/packlists/{$packlistId}");
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Erstellen der Packliste: ' . $e->getMessage());
            $response->redirect('/packlists/create?' . http_build_query($data));
        }
    }

    /**
     * Show a packlist
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function show(Request $request, Response $response, array $params): void
    {
        $packlistId = (int)$params['id'];
        $packlist = $this->packlistModel->getPacklistWithItems($packlistId);

        if ($packlist === null) {
            $response->abort(404, 'Packliste nicht gefunden');
            return;
        }

        $availableDevices = $this->packlistModel->getAvailableDevices();
        $availableCases = $this->packlistModel->getAvailableCases();

        // Filter out devices and cases already in the packlist
        $availableDevices = array_filter($availableDevices, function($device) use ($packlist) {
            return !$this->packlistModel->isDeviceInPacklist($packlist['id'], $device['id']);
        });

        $availableCases = array_filter($availableCases, function($case) use ($packlist) {
            return !$this->packlistModel->isCaseInPacklist($packlist['id'], $case['id']);
        });

        $this->render('packlists/show', [
            'packlist' => $packlist,
            'availableDevices' => $availableDevices,
            'availableCases' => $availableCases,
            'statusOptions' => Packlist::getStatusOptions(),
            'itemTypeOptions' => Packlist::getItemTypeOptions(),
        ]);
    }

    /**
     * Show the edit packlist form
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function edit(Request $request, Response $response, array $params): void
    {
        $packlistId = (int)$params['id'];
        $packlist = $this->packlistModel->find($packlistId);

        if ($packlist === null) {
            $response->abort(404, 'Packliste nicht gefunden');
            return;
        }

        $this->render('packlists/edit', [
            'packlist' => $packlist,
            'errors' => [],
            'statusOptions' => Packlist::getStatusOptions(),
        ]);
    }

    /**
     * Update a packlist
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function update(Request $request, Response $response, array $params): void
    {
        $packlistId = (int)$params['id'];
        $packlist = $this->packlistModel->find($packlistId);

        if ($packlist === null) {
            $response->abort(404, 'Packliste nicht gefunden');
            return;
        }

        $data = [
            'name' => $request->getPost('name'),
            'description' => $request->getPost('description'),
            'status' => $request->getPost('status', $packlist['status']),
            'notes' => $request->getPost('notes'),
        ];

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:' . implode(',', Packlist::getValidStatuses())],
            'notes' => ['nullable', 'string'],
        ];

        $validation = $this->validator->validate($data, $rules);

        if ($validation->fails()) {
            $this->render('packlists/edit', [
                'packlist' => array_merge($packlist, $data),
                'errors' => $validation->errors(),
                'statusOptions' => Packlist::getStatusOptions(),
            ], 422);
            return;
        }

        try {
            $this->packlistModel->updatePacklist($packlistId, $data);
            
            $this->addFlash('success', 'Packliste erfolgreich aktualisiert!');
            $response->redirect("/packlists/{$packlistId}");
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Aktualisieren der Packliste: ' . $e->getMessage());
            $this->render('packlists/edit', [
                'packlist' => array_merge($packlist, $data),
                'errors' => [],
                'statusOptions' => Packlist::getStatusOptions(),
            ], 500);
        }
    }

    /**
     * Delete a packlist
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function destroy(Request $request, Response $response, array $params): void
    {
        $packlistId = (int)$params['id'];
        $packlist = $this->packlistModel->find($packlistId);

        if ($packlist === null) {
            $response->abort(404, 'Packliste nicht gefunden');
            return;
        }

        try {
            $this->packlistModel->deletePacklist($packlistId);
            
            $this->addFlash('success', 'Packliste erfolgreich gelöscht!');
            $response->redirect('/packlists');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Löschen der Packliste: ' . $e->getMessage());
            $response->redirect("/packlists/{$packlistId}");
        }
    }

    /**
     * Add item to packlist
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function addItem(Request $request, Response $response, array $params): void
    {
        $packlistId = (int)$params['id'];
        $packlist = $this->packlistModel->find($packlistId);

        if ($packlist === null) {
            $response->abort(404, 'Packliste nicht gefunden');
            return;
        }

        $itemType = $request->getPost('item_type');
        $itemId = (int)$request->getPost('item_id');
        $quantity = (int)$request->getPost('quantity', 1);
        $notes = $request->getPost('notes');

        if (empty($itemType) || $itemId <= 0) {
            $this->addFlash('error', 'Bitte wählen Sie einen Artikel aus!');
            $response->redirect("/packlists/{$packlistId}");
            return;
        }

        try {
            $this->packlistModel->addItemToPacklist($packlistId, $itemType, $itemId, $quantity, $notes);
            
            $this->addFlash('success', 'Artikel erfolgreich zur Packliste hinzugefügt!');
            $response->redirect("/packlists/{$packlistId}");
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Hinzufügen des Artikels: ' . $e->getMessage());
            $response->redirect("/packlists/{$packlistId}");
        }
    }

    /**
     * Remove item from packlist
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function removeItem(Request $request, Response $response, array $params): void
    {
        $packlistId = (int)$params['id'];
        $itemId = (int)$params['item_id'];

        $packlist = $this->packlistModel->find($packlistId);

        if ($packlist === null) {
            $response->abort(404, 'Packliste nicht gefunden');
            return;
        }

        try {
            $this->packlistModel->removeItemFromPacklist($packlistId, $itemId);
            
            $this->addFlash('success', 'Artikel erfolgreich aus der Packliste entfernt!');
            $response->redirect("/packlists/{$packlistId}");
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Entfernen des Artikels: ' . $e->getMessage());
            $response->redirect("/packlists/{$packlistId}");
        }
    }

    /**
     * Toggle item checked status
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function toggleItem(Request $request, Response $response, array $params): void
    {
        $packlistId = (int)$params['id'];
        $itemId = (int)$params['item_id'];

        $packlist = $this->packlistModel->find($packlistId);

        if ($packlist === null) {
            $response->abort(404, 'Packliste nicht gefunden');
            return;
        }

        try {
            $this->packlistModel->toggleItemChecked($itemId);
            
            $response->json(['success' => true]);
        } catch (\Exception $e) {
            $response->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Reorder items
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function reorderItems(Request $request, Response $response, array $params): void
    {
        $packlistId = (int)$params['id'];
        $packlist = $this->packlistModel->find($packlistId);

        if ($packlist === null) {
            $response->abort(404, 'Packliste nicht gefunden');
            return;
        }

        $itemOrders = $request->getPost('item_orders', []);

        if (empty($itemOrders)) {
            $response->json(['success' => false, 'error' => 'No item orders provided'], 400);
            return;
        }

        try {
            $this->packlistModel->reorderItems($packlistId, $itemOrders);
            
            $response->json(['success' => true]);
        } catch (\Exception $e) {
            $response->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Export packlist as PDF
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function exportPdf(Request $request, Response $response, array $params): void
    {
        $packlistId = (int)$params['id'];
        $packlist = $this->packlistModel->getPacklistWithItems($packlistId);

        if ($packlist === null) {
            $response->abort(404, 'Packliste nicht gefunden');
            return;
        }

        // This will be implemented with TCPDF in a later phase
        // For now, redirect to a simple PDF generation endpoint
        $response->redirect("/api/packlists/{$packlistId}/pdf");
    }

    /**
     * Print packlist
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function print(Request $request, Response $response, array $params): void
    {
        $packlistId = (int)$params['id'];
        $packlist = $this->packlistModel->getPacklistWithItems($packlistId);

        if ($packlist === null) {
            $response->abort(404, 'Packliste nicht gefunden');
            return;
        }

        $this->render('packlists/print', [
            'packlist' => $packlist,
            'statusOptions' => Packlist::getStatusOptions(),
            'itemTypeOptions' => Packlist::getItemTypeOptions(),
        ]);
    }
}
