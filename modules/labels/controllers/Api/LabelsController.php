<?php

declare(strict_types=1);

namespace DDWB\Modules\Labels\Controllers\Api;

use DDWB\Controller;
use DDWB\Modules\Labels\Models\LabelTemplate;
use DDWB\Request;
use DDWB\Response;

/**
 * API Labels Controller
 * 
 * Handles label-related API requests
 */
final class LabelsController extends Controller
{
    private LabelTemplate $labelTemplateModel;

    /**
     * Create a new LabelsController instance
     * 
     * @param LabelTemplate $labelTemplateModel The label template model
     */
    public function __construct(LabelTemplate $labelTemplateModel)
    {
        $this->labelTemplateModel = $labelTemplateModel;
    }

    /**
     * Get all label templates
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function templates(Request $request, Response $response): void
    {
        $filters = [
            'type' => $request->getQuery('type'),
            'is_default' => $request->getQuery('is_default'),
        ];

        $templates = $this->labelTemplateModel->getAllTemplates(array_filter($filters));

        $response->json([
            'success' => true,
            'data' => $templates,
            'count' => count($templates),
        ]);
    }

    /**
     * Generate labels
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function generate(Request $request, Response $response): void
    {
        $templateId = (int)$request->getPost('template_id', 0);
        $itemIds = $request->getPost('item_ids', []);
        $itemType = $request->getPost('item_type', 'device');

        if (empty($itemIds)) {
            $response->json([
                'success' => false,
                'error' => 'No items specified',
            ], 400);
            return;
        }

        $template = $templateId > 0 ? $this->labelTemplateModel->getTemplateById($templateId) : null;
        if ($template === null) {
            $template = $this->labelTemplateModel->getDefaultTemplate($itemType);
            if ($template === null) {
                $response->json([
                    'success' => false,
                    'error' => 'No template found',
                ], 404);
                return;
            }
        }

        // This will be implemented with TCPDF in a later phase
        // For now, return JSON with the data that would be used for PDF generation
        
        $response->json([
            'success' => true,
            'message' => 'Label generation not yet implemented',
            'template' => $template,
            'item_ids' => $itemIds,
            'item_type' => $itemType,
        ]);
    }
}
