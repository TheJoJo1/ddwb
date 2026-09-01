<?php

declare(strict_types=1);

namespace DDWB\Modules\Labels\Controllers;

use DDWB\Controller;
use DDWB\Modules\Labels\Models\LabelTemplate;
use DDWB\Modules\Inventory\Models\Device;
use DDWB\Modules\Cases\Models\CaseModel;
use DDWB\Request;
use DDWB\Response;
use DDWB\Validator;

/**
 * Labels Controller
 * 
 * Handles label-related HTTP requests
 */
final class LabelsController extends Controller
{
    private LabelTemplate $labelTemplateModel;
    private Device $deviceModel;
    private CaseModel $caseModel;
    private Validator $validator;

    /**
     * Create a new LabelsController instance
     * 
     * @param LabelTemplate $labelTemplateModel The label template model
     * @param Device $deviceModel The device model
     * @param CaseModel $caseModel The case model
     * @param Validator $validator The validator
     */
    public function __construct(LabelTemplate $labelTemplateModel, Device $deviceModel, CaseModel $caseModel, Validator $validator)
    {
        $this->labelTemplateModel = $labelTemplateModel;
        $this->deviceModel = $deviceModel;
        $this->caseModel = $caseModel;
        $this->validator = $validator;
    }

    /**
     * List all label templates
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function index(Request $request, Response $response): void
    {
        $filters = [
            'type' => $request->getQuery('type'),
        ];

        $templates = $this->labelTemplateModel->getAllTemplates(array_filter($filters));
        $statistics = $this->labelTemplateModel->getStatistics();

        $this->render('labels/index', [
            'templates' => $templates,
            'filters' => $filters,
            'statistics' => $statistics,
            'typeOptions' => LabelTemplate::getTypeOptions(),
            'unitOptions' => LabelTemplate::getUnitOptions(),
            'orientationOptions' => LabelTemplate::getOrientationOptions(),
            'barcodeTypeOptions' => LabelTemplate::getBarcodeTypeOptions(),
        ]);
    }

    /**
     * Show the create template form
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function create(Request $request, Response $response): void
    {
        $this->render('labels/create', [
            'templateData' => [],
            'errors' => [],
            'typeOptions' => LabelTemplate::getTypeOptions(),
            'unitOptions' => LabelTemplate::getUnitOptions(),
            'orientationOptions' => LabelTemplate::getOrientationOptions(),
            'barcodeTypeOptions' => LabelTemplate::getBarcodeTypeOptions(),
            'standardSizeOptions' => LabelTemplate::getStandardSizeOptions(),
        ]);
    }

    /**
     * Store a new label template
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function store(Request $request, Response $response): void
    {
        $data = [
            'name' => $request->getPost('name'),
            'description' => $request->getPost('description'),
            'type' => $request->getPost('type', LabelTemplate::TYPE_DEVICE),
            'width' => (float)$request->getPost('width'),
            'height' => (float)$request->getPost('height'),
            'unit' => $request->getPost('unit', LabelTemplate::UNIT_MM),
            'orientation' => $request->getPost('orientation', LabelTemplate::ORIENTATION_PORTRAIT),
            'include_qr' => (bool)$request->getPost('include_qr', true),
            'include_barcode' => (bool)$request->getPost('include_barcode', true),
            'include_name' => (bool)$request->getPost('include_name', true),
            'include_internal_id' => (bool)$request->getPost('include_internal_id', true),
            'include_serial_number' => (bool)$request->getPost('include_serial_number', false),
            'font_size' => (float)$request->getPost('font_size'),
            'qr_size' => (float)$request->getPost('qr_size'),
            'barcode_type' => $request->getPost('barcode_type', LabelTemplate::BARCODE_CODE128),
            'is_default' => (bool)$request->getPost('is_default', false),
            'template_json' => $request->getPost('template_json'),
        ];

        $rules = [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:' . implode(',', array_keys(LabelTemplate::getTypeOptions()))],
            'width' => ['required', 'numeric', 'min:10'],
            'height' => ['required', 'numeric', 'min:10'],
            'unit' => ['required', 'in:' . implode(',', array_keys(LabelTemplate::getUnitOptions()))],
            'orientation' => ['required', 'in:' . implode(',', array_keys(LabelTemplate::getOrientationOptions()))],
            'font_size' => ['required', 'numeric', 'min:6', 'max:24'],
            'qr_size' => ['required', 'numeric', 'min:10', 'max:100'],
            'barcode_type' => ['required', 'in:' . implode(',', array_keys(LabelTemplate::getBarcodeTypeOptions()))],
        ];

        $validation = $this->validator->validate($data, $rules);

        if ($validation->fails()) {
            $this->render('labels/create', [
                'templateData' => $data,
                'errors' => $validation->errors(),
                'typeOptions' => LabelTemplate::getTypeOptions(),
                'unitOptions' => LabelTemplate::getUnitOptions(),
                'orientationOptions' => LabelTemplate::getOrientationOptions(),
                'barcodeTypeOptions' => LabelTemplate::getBarcodeTypeOptions(),
                'standardSizeOptions' => LabelTemplate::getStandardSizeOptions(),
            ], 422);
            return;
        }

        try {
            $templateId = $this->labelTemplateModel->createTemplate($data);
            
            $this->addFlash('success', 'Labelvorlage erfolgreich erstellt!');
            $response->redirect("/labels/templates/{$templateId}");
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Erstellen der Labelvorlage: ' . $e->getMessage());
            $this->render('labels/create', [
                'templateData' => $data,
                'errors' => [],
                'typeOptions' => LabelTemplate::getTypeOptions(),
                'unitOptions' => LabelTemplate::getUnitOptions(),
                'orientationOptions' => LabelTemplate::getOrientationOptions(),
                'barcodeTypeOptions' => LabelTemplate::getBarcodeTypeOptions(),
                'standardSizeOptions' => LabelTemplate::getStandardSizeOptions(),
            ], 500);
        }
    }

    /**
     * Show a label template
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function showTemplate(Request $request, Response $response, array $params): void
    {
        $templateId = (int)$params['id'];
        $template = $this->labelTemplateModel->getTemplateById($templateId);

        if ($template === null) {
            $response->abort(404, 'Labelvorlage nicht gefunden');
            return;
        }

        $this->render('labels/show_template', [
            'template' => $template,
            'typeOptions' => LabelTemplate::getTypeOptions(),
            'unitOptions' => LabelTemplate::getUnitOptions(),
            'orientationOptions' => LabelTemplate::getOrientationOptions(),
            'barcodeTypeOptions' => LabelTemplate::getBarcodeTypeOptions(),
        ]);
    }

    /**
     * Show the edit template form
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function editTemplate(Request $request, Response $response, array $params): void
    {
        $templateId = (int)$params['id'];
        $template = $this->labelTemplateModel->getTemplateById($templateId);

        if ($template === null) {
            $response->abort(404, 'Labelvorlage nicht gefunden');
            return;
        }

        $this->render('labels/edit_template', [
            'template' => $template,
            'errors' => [],
            'typeOptions' => LabelTemplate::getTypeOptions(),
            'unitOptions' => LabelTemplate::getUnitOptions(),
            'orientationOptions' => LabelTemplate::getOrientationOptions(),
            'barcodeTypeOptions' => LabelTemplate::getBarcodeTypeOptions(),
        ]);
    }

    /**
     * Update a label template
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function updateTemplate(Request $request, Response $response, array $params): void
    {
        $templateId = (int)$params['id'];
        $template = $this->labelTemplateModel->getTemplateById($templateId);

        if ($template === null) {
            $response->abort(404, 'Labelvorlage nicht gefunden');
            return;
        }

        $data = [
            'name' => $request->getPost('name'),
            'description' => $request->getPost('description'),
            'type' => $request->getPost('type', $template['type']),
            'width' => (float)$request->getPost('width'),
            'height' => (float)$request->getPost('height'),
            'unit' => $request->getPost('unit', $template['unit']),
            'orientation' => $request->getPost('orientation', $template['orientation']),
            'include_qr' => (bool)$request->getPost('include_qr', $template['include_qr']),
            'include_barcode' => (bool)$request->getPost('include_barcode', $template['include_barcode']),
            'include_name' => (bool)$request->getPost('include_name', $template['include_name']),
            'include_internal_id' => (bool)$request->getPost('include_internal_id', $template['include_internal_id']),
            'include_serial_number' => (bool)$request->getPost('include_serial_number', $template['include_serial_number']),
            'font_size' => (float)$request->getPost('font_size'),
            'qr_size' => (float)$request->getPost('qr_size'),
            'barcode_type' => $request->getPost('barcode_type', $template['barcode_type']),
            'is_default' => (bool)$request->getPost('is_default', $template['is_default']),
            'template_json' => $request->getPost('template_json'),
        ];

        $rules = [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:' . implode(',', array_keys(LabelTemplate::getTypeOptions()))],
            'width' => ['required', 'numeric', 'min:10'],
            'height' => ['required', 'numeric', 'min:10'],
            'unit' => ['required', 'in:' . implode(',', array_keys(LabelTemplate::getUnitOptions()))],
            'orientation' => ['required', 'in:' . implode(',', array_keys(LabelTemplate::getOrientationOptions()))],
            'font_size' => ['required', 'numeric', 'min:6', 'max:24'],
            'qr_size' => ['required', 'numeric', 'min:10', 'max:100'],
            'barcode_type' => ['required', 'in:' . implode(',', array_keys(LabelTemplate::getBarcodeTypeOptions()))],
        ];

        $validation = $this->validator->validate($data, $rules);

        if ($validation->fails()) {
            $this->render('labels/edit_template', [
                'template' => array_merge($template, $data),
                'errors' => $validation->errors(),
                'typeOptions' => LabelTemplate::getTypeOptions(),
                'unitOptions' => LabelTemplate::getUnitOptions(),
                'orientationOptions' => LabelTemplate::getOrientationOptions(),
                'barcodeTypeOptions' => LabelTemplate::getBarcodeTypeOptions(),
            ], 422);
            return;
        }

        try {
            $this->labelTemplateModel->updateTemplate($templateId, $data);
            
            $this->addFlash('success', 'Labelvorlage erfolgreich aktualisiert!');
            $response->redirect("/labels/templates/{$templateId}");
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Aktualisieren der Labelvorlage: ' . $e->getMessage());
            $this->render('labels/edit_template', [
                'template' => array_merge($template, $data),
                'errors' => [],
                'typeOptions' => LabelTemplate::getTypeOptions(),
                'unitOptions' => LabelTemplate::getUnitOptions(),
                'orientationOptions' => LabelTemplate::getOrientationOptions(),
                'barcodeTypeOptions' => LabelTemplate::getBarcodeTypeOptions(),
            ], 500);
        }
    }

    /**
     * Delete a label template
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function destroyTemplate(Request $request, Response $response, array $params): void
    {
        $templateId = (int)$params['id'];
        $template = $this->labelTemplateModel->getTemplateById($templateId);

        if ($template === null) {
            $response->abort(404, 'Labelvorlage nicht gefunden');
            return;
        }

        try {
            $this->labelTemplateModel->deleteTemplate($templateId);
            
            $this->addFlash('success', 'Labelvorlage erfolgreich gelöscht!');
            $response->redirect('/labels/templates');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Löschen der Labelvorlage: ' . $e->getMessage());
            $response->redirect("/labels/templates/{$templateId}");
        }
    }

    /**
     * Create standard templates
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function createStandardTemplates(Request $request, Response $response): void
    {
        try {
            $count = $this->labelTemplateModel->createStandardTemplates();
            
            $this->addFlash('success', "{$count} Standard-Labelvorlagen wurden erstellt!");
            $response->redirect('/labels/templates');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Fehler beim Erstellen der Standard-Labelvorlagen: ' . $e->getMessage());
            $response->redirect('/labels/templates');
        }
    }

    /**
     * Show label designer
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function designer(Request $request, Response $response): void
    {
        $templateId = (int)$request->getQuery('template_id', 0);
        $template = null;

        if ($templateId > 0) {
            $template = $this->labelTemplateModel->getTemplateById($templateId);
        }

        $templates = $this->labelTemplateModel->getAllTemplates();

        $this->render('labels/designer', [
            'template' => $template,
            'templates' => $templates,
            'typeOptions' => LabelTemplate::getTypeOptions(),
            'unitOptions' => LabelTemplate::getUnitOptions(),
            'orientationOptions' => LabelTemplate::getOrientationOptions(),
            'barcodeTypeOptions' => LabelTemplate::getBarcodeTypeOptions(),
            'standardSizeOptions' => LabelTemplate::getStandardSizeOptions(),
        ]);
    }

    /**
     * Generate labels for devices or cases
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function generate(Request $request, Response $response): void
    {
        $templateId = (int)$request->getPost('template_id');
        $itemIds = $request->getPost('item_ids', []);
        $itemType = $request->getPost('item_type', 'device');

        if (empty($itemIds)) {
            $this->addFlash('error', 'Bitte wählen Sie mindestens einen Artikel aus!');
            $response->redirect('/labels/designer');
            return;
        }

        if ($templateId <= 0) {
            $template = $this->labelTemplateModel->getDefaultTemplate($itemType);
            if ($template === null) {
                $this->addFlash('error', 'Keine Standard-Labelvorlage gefunden!');
                $response->redirect('/labels/templates');
                return;
            }
            $templateId = $template['id'];
        }

        $template = $this->labelTemplateModel->getTemplateById($templateId);

        if ($template === null) {
            $this->addFlash('error', 'Labelvorlage nicht gefunden!');
            $response->redirect('/labels/templates');
            return;
        }

        // Get the items
        $items = [];
        if ($itemType === 'device') {
            foreach ($itemIds as $id) {
                $device = $this->deviceModel->find((int)$id);
                if ($device !== null) {
                    $items[] = [
                        'type' => 'device',
                        'id' => $device['id'],
                        'name' => $device['name'],
                        'internal_id' => $device['internal_id'],
                        'serial_number' => $device['serial_number'] ?? '',
                    ];
                }
            }
        } elseif ($itemType === 'case') {
            foreach ($itemIds as $id) {
                $case = $this->caseModel->find((int)$id);
                if ($case !== null) {
                    $items[] = [
                        'type' => 'case',
                        'id' => $case['id'],
                        'name' => $case['name'],
                        'internal_id' => $case['internal_id'],
                        'serial_number' => '',
                    ];
                }
            }
        }

        if (empty($items)) {
            $this->addFlash('error', 'Keine gültigen Artikel gefunden!');
            $response->redirect('/labels/designer');
            return;
        }

        // Generate PDF or show preview
        $preview = $request->getPost('preview', true);

        if ($preview) {
            $this->render('labels/preview', [
                'template' => $template,
                'items' => $items,
                'itemType' => $itemType,
            ]);
        } else {
            // Generate PDF
            $response->redirect("/labels/generate-pdf?template_id={$templateId}&item_type={$itemType}&item_ids=" . implode(',', $itemIds));
        }
    }

    /**
     * Generate PDF labels
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function generatePdf(Request $request, Response $response): void
    {
        $templateId = (int)$request->getQuery('template_id', 0);
        $itemIds = explode(',', $request->getQuery('item_ids', ''));
        $itemType = $request->getQuery('item_type', 'device');

        if (empty($itemIds)) {
            $response->abort(400, 'No items specified');
            return;
        }

        $template = $templateId > 0 ? $this->labelTemplateModel->getTemplateById($templateId) : null;
        if ($template === null) {
            $template = $this->labelTemplateModel->getDefaultTemplate($itemType);
            if ($template === null) {
                $response->abort(404, 'No template found');
                return;
            }
        }

        // This will be implemented with TCPDF in a later phase
        // For now, return JSON with the data that would be used for PDF generation
        
        $response->json([
            'success' => true,
            'message' => 'PDF generation not yet implemented',
            'template' => $template,
            'item_ids' => $itemIds,
            'item_type' => $itemType,
        ]);
    }
}
