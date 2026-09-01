<?php

declare(strict_types=1);

namespace DDWB\Modules\Packlists\Controllers\Api;

use DDWB\Controller;
use DDWB\Modules\Packlists\Models\Packlist;
use DDWB\Request;
use DDWB\Response;

/**
 * API Packlists Controller
 * 
 * Handles packlist-related API requests
 */
final class PacklistsController extends Controller
{
    private Packlist $packlistModel;

    /**
     * Create a new PacklistsController instance
     * 
     * @param Packlist $packlistModel The packlist model
     */
    public function __construct(Packlist $packlistModel)
    {
        $this->packlistModel = $packlistModel;
    }

    /**
     * Get all packlists
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

        $packlists = $this->packlistModel->getAllPacklists(array_filter($filters));

        $response->json([
            'success' => true,
            'data' => $packlists,
            'count' => count($packlists),
        ]);
    }

    /**
     * Search packlists
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

        $packlists = $this->packlistModel->search($query, array_filter($filters));

        $response->json([
            'success' => true,
            'data' => $packlists,
            'count' => count($packlists),
        ]);
    }

    /**
     * Get a single packlist
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
            $response->json([
                'success' => false,
                'error' => 'Packlist not found',
            ], 404);
            return;
        }

        $response->json([
            'success' => true,
            'data' => $packlist,
        ]);
    }

    /**
     * Get packlist items
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function items(Request $request, Response $response, array $params): void
    {
        $packlistId = (int)$params['id'];
        $packlist = $this->packlistModel->getPacklistWithItems($packlistId);

        if ($packlist === null) {
            $response->json([
                'success' => false,
                'error' => 'Packlist not found',
            ], 404);
            return;
        }

        $response->json([
            'success' => true,
            'data' => $packlist['items'] ?? [],
        ]);
    }

    /**
     * Generate PDF for packlist
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function pdf(Request $request, Response $response, array $params): void
    {
        $packlistId = (int)$params['id'];
        $packlist = $this->packlistModel->getPacklistWithItems($packlistId);

        if ($packlist === null) {
            $response->abort(404, 'Packlist not found');
            return;
        }

        // This will be implemented with TCPDF in a later phase
        // For now, return JSON with the data that would be used for PDF generation
        
        $response->json([
            'success' => true,
            'message' => 'PDF generation not yet implemented',
            'data' => $packlist,
        ]);
    }
}
