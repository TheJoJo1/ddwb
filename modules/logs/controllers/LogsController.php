<?php

declare(strict_types=1);

namespace DDWB\Modules\Logs\Controllers;

use DDWB\Controller;
use DDWB\Modules\Logs\Models\Log;
use DDWB\Modules\Users\Models\User;
use DDWB\Request;
use DDWB\Response;

/**
 * Logs Controller
 * 
 * Handles log-related HTTP requests (Audit Trail)
 */
final class LogsController extends Controller
{
    private Log $logModel;
    private User $userModel;

    /**
     * Create a new LogsController instance
     * 
     * @param Log $logModel The log model
     * @param User $userModel The user model
     */
    public function __construct(Log $logModel, User $userModel)
    {
        $this->logModel = $logModel;
        $this->userModel = $userModel;
    }

    /**
     * List all logs
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
            'search' => $request->getQuery('search'),
        ];

        $page = (int)$request->getQuery('page', 1);
        $perPage = 50;

        $result = $this->logModel->paginateLogs($page, $perPage, array_filter($filters));
        $statistics = $this->logModel->getStatistics();
        $users = $this->userModel->getAllUsers();

        $this->render('logs/index', [
            'logs' => $result['data'],
            'pagination' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'total_pages' => $result['total_pages'],
            ],
            'filters' => $filters,
            'statistics' => $statistics,
            'users' => $users,
            'actionOptions' => Log::getActionOptions(),
        ]);
    }

    /**
     * Show a single log entry
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
            $response->abort(404, 'Protokolleintrag nicht gefunden');
            return;
        }

        // Decode metadata if present
        if (!empty($log['metadata_json'])) {
            $log['metadata'] = json_decode($log['metadata_json'], true);
        }

        $this->render('logs/show', [
            'log' => $log,
            'actionOptions' => Log::getActionOptions(),
        ]);
    }

    /**
     * Show recent logs
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function recent(Request $request, Response $response): void
    {
        $limit = (int)$request->getQuery('limit', 50);
        $logs = $this->logModel->getRecentLogs($limit);

        $this->render('logs/recent', [
            'logs' => $logs,
            'limit' => $limit,
            'actionOptions' => Log::getActionOptions(),
        ]);
    }

    /**
     * Show today's logs
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function today(Request $request, Response $response): void
    {
        $logs = $this->logModel->getLogsForToday();

        $this->render('logs/today', [
            'logs' => $logs,
            'actionOptions' => Log::getActionOptions(),
        ]);
    }

    /**
     * Show logs by user
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function byUser(Request $request, Response $response, array $params): void
    {
        $userId = (int)$params['user_id'];
        $user = $this->userModel->find($userId);

        if ($user === null) {
            $response->abort(404, 'Benutzer nicht gefunden');
            return;
        }

        $filters = [
            'user_id' => $userId,
            'action' => $request->getQuery('action'),
            'entity_type' => $request->getQuery('entity_type'),
            'start_date' => $request->getQuery('start_date'),
            'end_date' => $request->getQuery('end_date'),
        ];

        $page = (int)$request->getQuery('page', 1);
        $perPage = 50;

        $result = $this->logModel->paginateLogs($page, $perPage, array_filter($filters));

        $this->render('logs/by_user', [
            'logs' => $result['data'],
            'pagination' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'total_pages' => $result['total_pages'],
            ],
            'filters' => $filters,
            'user' => $user,
            'actionOptions' => Log::getActionOptions(),
        ]);
    }

    /**
     * Show logs by action
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function byAction(Request $request, Response $response, array $params): void
    {
        $action = $params['action'];

        $filters = [
            'action' => $action,
            'user_id' => $request->getQuery('user_id'),
            'entity_type' => $request->getQuery('entity_type'),
            'start_date' => $request->getQuery('start_date'),
            'end_date' => $request->getQuery('end_date'),
        ];

        $page = (int)$request->getQuery('page', 1);
        $perPage = 50;

        $result = $this->logModel->paginateLogs($page, $perPage, array_filter($filters));
        $users = $this->userModel->getAllUsers();

        $this->render('logs/by_action', [
            'logs' => $result['data'],
            'pagination' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'total_pages' => $result['total_pages'],
            ],
            'filters' => $filters,
            'action' => $action,
            'users' => $users,
            'actionOptions' => Log::getActionOptions(),
        ]);
    }

    /**
     * Clear old logs
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function clearOld(Request $request, Response $response): void
    {
        $days = (int)$request->getPost('days', 365);

        if ($days < 1 || $days > 3650) {
            $this->addFlash('error', 'Bitte geben Sie eine gültige Anzahl von Tagen ein (1-3650)!');
            $response->redirect('/logs');
            return;
        }

        try {
            $count = $this->logModel->clearOldLogs($days);
            
            $this->addFlash('success', "{$count} alte Protokolleinträge wurden gelöscht!");
            $response->redirect('/logs');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Löschen alter Protokolleinträge: ' . $e->getMessage());
            $response->redirect('/logs');
        }
    }

    /**
     * Export logs
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function export(Request $request, Response $response): void
    {
        $filters = [
            'user_id' => $request->getQuery('user_id'),
            'action' => $request->getQuery('action'),
            'entity_type' => $request->getQuery('entity_type'),
            'start_date' => $request->getQuery('start_date'),
            'end_date' => $request->getQuery('end_date'),
        ];

        $logs = $this->logModel->getAllLogs(array_filter($filters));

        // This will be implemented with CSV export in a later phase
        // For now, redirect with JSON
        
        $response->json([
            'success' => true,
            'message' => 'Export not yet implemented',
            'data' => $logs,
        ]);
    }
}
