<?php

declare(strict_types=1);

namespace DDWB\Modules\Labels\Models;

use DDWB\Model;
use DDWB\Database;
use DDWB\Application;

/**
 * Label Template Model
 * 
 * Handles label template data and operations
 */
final class LabelTemplate extends Model
{
    protected string $table = 'label_templates';
    protected string $primaryKey = 'id';
    
    protected array $fillable = [
        'name',
        'description',
        'type',
        'width',
        'height',
        'unit',
        'orientation',
        'include_qr',
        'include_barcode',
        'include_name',
        'include_internal_id',
        'include_serial_number',
        'font_size',
        'qr_size',
        'barcode_type',
        'template_json',
        'is_default',
    ];
    
    protected array $casts = [
        'width' => 'float',
        'height' => 'float',
        'font_size' => 'float',
        'qr_size' => 'float',
        'include_qr' => 'bool',
        'include_barcode' => 'bool',
        'include_name' => 'bool',
        'include_internal_id' => 'bool',
        'include_serial_number' => 'bool',
        'is_default' => 'bool',
    ];
    
    protected array $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Valid types
     */
    public const TYPE_DEVICE = 'device';
    public const TYPE_CASE = 'case';
    public const TYPE_BOTH = 'both';

    /** @var array<string, string> */
    public static array $typeLabels = [
        self::TYPE_DEVICE => 'Gerät',
        self::TYPE_CASE => 'Case',
        self::TYPE_BOTH => 'Beides',
    ];

    /**
     * Valid units
     */
    public const UNIT_MM = 'mm';
    public const UNIT_CM = 'cm';
    public const UNIT_INCH = 'inch';

    /** @var array<string, string> */
    public static array $unitLabels = [
        self::UNIT_MM => 'Millimeter',
        self::UNIT_CM => 'Zentimeter',
        self::UNIT_INCH => 'Zoll',
    ];

    /**
     * Valid orientations
     */
    public const ORIENTATION_PORTRAIT = 'portrait';
    public const ORIENTATION_LANDSCAPE = 'landscape';

    /** @var array<string, string> */
    public static array $orientationLabels = [
        self::ORIENTATION_PORTRAIT => 'Hochformat',
        self::ORIENTATION_LANDSCAPE => 'Querformat',
    ];

    /**
     * Valid barcode types
     */
    public const BARCODE_CODE128 = 'code128';
    public const BARCODE_CODE39 = 'code39';
    public const BARCODE_EAN13 = 'ean13';
    public const BARCODE_UPC_A = 'upc_a';

    /** @var array<string, string> */
    public static array $barcodeTypeLabels = [
        self::BARCODE_CODE128 => 'Code 128',
        self::BARCODE_CODE39 => 'Code 39',
        self::BARCODE_EAN13 => 'EAN-13',
        self::BARCODE_UPC_A => 'UPC-A',
    ];

    /**
     * Standard template sizes
     */
    public const SIZE_SMALL = 'small';
    public const SIZE_MEDIUM = 'medium';
    public const SIZE_LARGE = 'large';
    public const SIZE_A4 = 'A4';

    /** @var array<string, array> */
    public static array $standardSizes = [
        self::SIZE_SMALL => [
            'name' => 'Klein',
            'width' => 50,
            'height' => 30,
            'unit' => self::UNIT_MM,
            'qr_size' => 15,
            'barcode_height' => 30,
            'font_size' => 8,
        ],
        self::SIZE_MEDIUM => [
            'name' => 'Mittel',
            'width' => 70,
            'height' => 40,
            'unit' => self::UNIT_MM,
            'qr_size' => 20,
            'barcode_height' => 40,
            'font_size' => 10,
        ],
        self::SIZE_LARGE => [
            'name' => 'Groß',
            'width' => 100,
            'height' => 60,
            'unit' => self::UNIT_MM,
            'qr_size' => 30,
            'barcode_height' => 50,
            'font_size' => 12,
        ],
        self::SIZE_A4 => [
            'name' => 'A4 Etikettenbogen',
            'width' => 210,
            'height' => 297,
            'unit' => self::UNIT_MM,
            'qr_size' => 40,
            'barcode_height' => 60,
            'font_size' => 14,
        ],
    ];

    /**
     * Create a new LabelTemplate instance
     * 
     * @param Database $database The database instance
     */
    public function __construct(Database $database)
    {
        parent::__construct($database);
    }

    /**
     * Get all label templates
     * 
     * @param array $filters Filters to apply
     * @return array The label templates
     */
    public function getAllTemplates(array $filters = []): array
    {
        $query = 'SELECT * FROM label_templates WHERE deleted_at IS NULL';
        $params = [];

        if (isset($filters['type'])) {
            $query .= ' AND type = ?';
            $params[] = $filters['type'];
        }

        if (isset($filters['is_default'])) {
            $query .= ' AND is_default = ?';
            $params[] = $filters['is_default'] ? 1 : 0;
        }

        $query .= ' ORDER BY name ASC';

        return $this->database->select($query, $params);
    }

    /**
     * Get default template
     * 
     * @param string|null $type The type (device, case, both)
     * @return array|null The default template or null if not found
     */
    public function getDefaultTemplate(?string $type = null): ?array
    {
        $query = 'SELECT * FROM label_templates WHERE is_default = 1 AND deleted_at IS NULL';
        $params = [];

        if ($type !== null) {
            $query .= ' AND type = ?';
            $params[] = $type;
        }

        $query .= ' ORDER BY id ASC LIMIT 1';

        return $this->database->selectOne($query, $params) ?? null;
    }

    /**
     * Get template by ID
     * 
     * @param int $templateId The template ID
     * @return array|null The template or null if not found
     */
    public function getTemplateById(int $templateId): ?array
    {
        return $this->find($templateId);
    }

    /**
     * Create a new label template
     * 
     * @param array $data The template data
     * @return int|string The new template ID
     */
    public function createTemplate(array $data): int|string
    {
        // Set default values
        $data['type'] = $data['type'] ?? self::TYPE_DEVICE;
        $data['width'] = $data['width'] ?? 70.00;
        $data['height'] = $data['height'] ?? 40.00;
        $data['unit'] = $data['unit'] ?? self::UNIT_MM;
        $data['orientation'] = $data['orientation'] ?? self::ORIENTATION_PORTRAIT;
        $data['include_qr'] = $data['include_qr'] ?? true;
        $data['include_barcode'] = $data['include_barcode'] ?? true;
        $data['include_name'] = $data['include_name'] ?? true;
        $data['include_internal_id'] = $data['include_internal_id'] ?? true;
        $data['include_serial_number'] = $data['include_serial_number'] ?? false;
        $data['font_size'] = $data['font_size'] ?? 10.00;
        $data['qr_size'] = $data['qr_size'] ?? 20.00;
        $data['barcode_type'] = $data['barcode_type'] ?? self::BARCODE_CODE128;
        $data['is_default'] = $data['is_default'] ?? false;

        // If this is set as default, unset other defaults
        if (!empty($data['is_default']) && $data['is_default']) {
            $this->database->update(
                'label_templates',
                ['is_default' => false],
                ['is_default' => true]
            );
        }

        // Begin transaction
        $this->database->beginTransaction();

        try {
            // Create the template
            $templateId = $this->create($data);

            // Log the action
            $this->logAction('label_template_create', 'label_template', (string)$templateId, 
                "Created label template: {$data['name']}");

            // Commit transaction
            $this->database->commit();

            return $templateId;
        } catch (\Exception $e) {
            // Rollback transaction
            $this->database->rollback();
            throw $e;
        }
    }

    /**
     * Update a label template
     * 
     * @param int $templateId The template ID
     * @param array $data The template data
     * @return int The number of affected rows
     */
    public function updateTemplate(int $templateId, array $data): int
    {
        // If this is set as default, unset other defaults
        if (isset($data['is_default']) && $data['is_default']) {
            $this->database->update(
                'label_templates',
                ['is_default' => false],
                ['is_default' => true, 'id' => ['!=' => $templateId]]
            );
        }

        // Begin transaction
        $this->database->beginTransaction();

        try {
            // Update the template
            $result = $this->update($templateId, $data);

            // Log the action
            $this->logAction('label_template_update', 'label_template', (string)$templateId, 
                "Updated label template: {$data['name']}");

            // Commit transaction
            $this->database->commit();

            return $result;
        } catch (\Exception $e) {
            // Rollback transaction
            $this->database->rollback();
            throw $e;
        }
    }

    /**
     * Delete a label template
     * 
     * @param int $templateId The template ID
     * @return int The number of affected rows
     */
    public function deleteTemplate(int $templateId): int
    {
        // Soft delete
        return $this->softDelete($templateId);
    }

    /**
     * Create standard templates
     * 
     * @return int The number of templates created
     */
    public function createStandardTemplates(): int
    {
        $count = 0;

        foreach (self::$standardSizes as $size => $config) {
            // Check if template already exists
            $existing = $this->database->selectOne(
                'SELECT id FROM label_templates WHERE name = ? AND deleted_at IS NULL',
                [$config['name']]
            );

            if ($existing === null) {
                $templateId = $this->createTemplate([
                    'name' => $config['name'],
                    'description' => "Standard-Labelvorlage - " . $config['name'],
                    'type' => self::TYPE_BOTH,
                    'width' => $config['width'],
                    'height' => $config['height'],
                    'unit' => $config['unit'],
                    'orientation' => self::ORIENTATION_PORTRAIT,
                    'include_qr' => true,
                    'include_barcode' => true,
                    'include_name' => true,
                    'include_internal_id' => true,
                    'include_serial_number' => false,
                    'font_size' => $config['font_size'],
                    'qr_size' => $config['qr_size'],
                    'barcode_type' => self::BARCODE_CODE128,
                    'is_default' => $size === self::SIZE_MEDIUM, // Medium is default
                ]);

                $count++;
            }
        }

        return $count;
    }

    /**
     * Get template statistics
     * 
     * @return array The template statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->count(),
            'device' => $this->database->count($this->table, ['type' => self::TYPE_DEVICE, 'deleted_at' => null]),
            'case' => $this->database->count($this->table, ['type' => self::TYPE_CASE, 'deleted_at' => null]),
            'both' => $this->database->count($this->table, ['type' => self::TYPE_BOTH, 'deleted_at' => null]),
            'default' => $this->database->count($this->table, ['is_default' => true, 'deleted_at' => null]),
        ];
    }

    /**
     * Get type label
     * 
     * @param string $type The type
     * @return string The label
     */
    public static function getTypeLabel(string $type): string
    {
        return self::$typeLabels[$type] ?? $type;
    }

    /**
     * Get unit label
     * 
     * @param string $unit The unit
     * @return string The label
     */
    public static function getUnitLabel(string $unit): string
    {
        return self::$unitLabels[$unit] ?? $unit;
    }

    /**
     * Get orientation label
     * 
     * @param string $orientation The orientation
     * @return string The label
     */
    public static function getOrientationLabel(string $orientation): string
    {
        return self::$orientationLabels[$orientation] ?? $orientation;
    }

    /**
     * Get barcode type label
     * 
     * @param string $barcodeType The barcode type
     * @return string The label
     */
    public static function getBarcodeTypeLabel(string $barcodeType): string
    {
        return self::$barcodeTypeLabels[$barcodeType] ?? $barcodeType;
    }

    /**
     * Get type options for select dropdown
     * 
     * @return array The type options
     */
    public static function getTypeOptions(): array
    {
        $options = [];
        
        foreach (self::$typeLabels as $value => $label) {
            $options[$value] = $label;
        }

        return $options;
    }

    /**
     * Get unit options for select dropdown
     * 
     * @return array The unit options
     */
    public static function getUnitOptions(): array
    {
        $options = [];
        
        foreach (self::$unitLabels as $value => $label) {
            $options[$value] = $label;
        }

        return $options;
    }

    /**
     * Get orientation options for select dropdown
     * 
     * @return array The orientation options
     */
    public static function getOrientationOptions(): array
    {
        $options = [];
        
        foreach (self::$orientationLabels as $value => $label) {
            $options[$value] = $label;
        }

        return $options;
    }

    /**
     * Get barcode type options for select dropdown
     * 
     * @return array The barcode type options
     */
    public static function getBarcodeTypeOptions(): array
    {
        $options = [];
        
        foreach (self::$barcodeTypeLabels as $value => $label) {
            $options[$value] = $label;
        }

        return $options;
    }

    /**
     * Get standard size options
     * 
     * @return array The standard size options
     */
    public static function getStandardSizeOptions(): array
    {
        $options = [];
        
        foreach (self::$standardSizes as $value => $config) {
            $options[$value] = $config['name'];
        }

        return $options;
    }

    /**
     * Log an action
     * 
     * @param string $action The action
     * @param string $entityType The entity type
     * @param string $entityId The entity ID
     * @param string $description The description
     */
    private function logAction(string $action, string $entityType, string $entityId, string $description): void
    {
        $auth = Application::getInstance()->getContainer()->resolve(\DDWB\Auth::class);
        $userId = $auth->id();
        
        $this->database->insert(
            'logs',
            [
                'user_id' => $userId,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'description' => $description,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'timestamp' => date('Y-m-d H:i:s'),
            ]
        );
    }
}
