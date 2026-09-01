<?php

declare(strict_types=1);

namespace DDWB\Modules\Logs\Controllers\Api;

use DDWB\Controller;
use DDWB\Modules\Logs\Models\Log;
use DDWB\Request;
use DDWB\Response;

/**
 * API Logs Controller
 * 
 * Handles log-related API requests
 */
final class LogsController extends Controller
{
    private Log $logModel;

    /**
     * Create a new LogsController instance
     * 
     * @param Log $logModel The log model
     */
    public function __construct(Log $logModel)
    {
        $this->logModel = $logModel;
    }

    /**
     * Get all logs
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function index(Request $request, Response $response): void
    {
        $filters = [
            'user_id' => $request->getQuery('user_id'),
            'action' => $request->getQuery('action'),
            'entity_type' => $request->getQuery('entity_type'),
            'entity_id' => $request->getQuery('entity_id'),
            'start_date' => $request->getQuery('start_date'),
            'end_date' => $request->getQuery('end_date'),
        ];

        $logs = $this->logModel->getAllLogs(array_filter($filters));

        $response->json([
            'success' => true,
            'data' => $logs,
            'count' => count($logs),
        ]);
    }

    /**
     * Search logs
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function search(Request $request, Response $response): void
    {
        $query = $request->getPost('query', $request->getQuery('query', ''));
        $filters = [
            'user_id' => $request->getQuery('user_id'),
            'action' => $request->getQuery('action'),
            'entity_type' => $request->getQuery('entity_type'),
        ];

        if (empty($query)) {
            $response->json([
                'success' => false,
                'error' => 'Query parameter is required',
            ], 400);
            return;
        }

        $logs = $this->logModel->search($query, array_filter($filters));

        $response->json([
            'success' => true,
            'data' => $logs,
            'count' => count($logs),
        ]);
    }

    /**
     * Get a single log entry
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function show(Request $request, Response $response, array $params): void
    {
        $logId = (int)$params['id'];
        $log = $this->database->selectOne(
            'SELECT l.*, u.name as user_name, u.email as user_email ' .
            'FROM logs l ' .
            'LEFT JOIN users u ON l.user_id = u.id ' .
            'WHERE l.id = ?',
            [$logId]
        );

        if ($log === null) {
            $response->json([
                'success' => false,
                'error' => 'Log entry not found',
            ], 404);
            return;
        }

        // Decode metadata if present
        if (!empty($log['metadata_json'])) {
            $log['metadata'] = json_decode($log['metadata_json'], true);
        }

        $response->json([
            'success' => true,
            'data' => $log,
        ]);
    }
}
