<?php

declare(strict_types=1);

namespace DDWB\Modules\Cases\Controllers\Api;

use DDWB\Controller;
use DDWB\Modules\Cases\Models\CaseModel;
use DDWB\Request;
use DDWB\Response;

/**
 * API Cases Controller
 * 
 * Handles case-related API requests
 */
final class CasesController extends Controller
{
    private CaseModel $caseModel;

    /**
     * Create a new CasesController instance
     * 
     * @param CaseModel $caseModel The case model
     */
    public function __construct(CaseModel $caseModel)
    {
        $this->caseModel = $caseModel;
    }

    /**
     * Get all cases
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

        $cases = $this->caseModel->getAllCases(array_filter($filters));

        $response->json([
            'success' => true,
            'data' => $cases,
            'count' => count($cases),
        ]);
    }

    /**
     * Search cases
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

        $cases = $this->caseModel->search($query, array_filter($filters));

        $response->json([
            'success' => true,
            'data' => $cases,
            'count' => count($cases),
        ]);
    }

    /**
     * Get a single case
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
            $response->json([
                'success' => false,
                'error' => 'Case not found',
            ], 404);
            return;
        }

        $response->json([
            'success' => true,
            'data' => $case,
        ]);
    }
}
