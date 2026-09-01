<?php

declare(strict_types=1);

namespace DDWB;

/**
 * Validator
 * 
 * Validates input data using a fluent interface
 */
final class Validator
{
    /** @var array<string, mixed> */
    private array $data;
    
    /** @var array<string, string> */
    private array $rules;
    
    /** @var array<string, array<string>> */
    private array $customMessages = [];
    
    /** @var array<string, string> */
    private array $errors = [];

    /** @var array<string, string> */
    private static array $defaultMessages = [
        'required' => 'The :field field is required.',
        'email' => 'The :field must be a valid email address.',
        'numeric' => 'The :field must be a number.',
        'integer' => 'The :field must be an integer.',
        'min' => 'The :field must be at least :param characters.',
        'max' => 'The :field may not be greater than :param characters.',
        'between' => 'The :field must be between :param1 and :param2 characters.',
        'alpha' => 'The :field may only contain letters.',
        'alpha_num' => 'The :field may only contain letters and numbers.',
        'alpha_dash' => 'The :field may only contain letters, numbers, and dashes.',
        'confirmed' => 'The :field confirmation does not match.',
        'unique' => 'The :field has already been taken.',
        'exists' => 'The selected :field is invalid.',
        'date' => 'The :field must be a valid date.',
        'date_format' => 'The :field does not match the format :param.',
        'url' => 'The :field must be a valid URL.',
        'ip' => 'The :field must be a valid IP address.',
        'regex' => 'The :field format is invalid.',
        'size' => 'The :field must be exactly :param.',
        'in' => 'The selected :field is invalid.',
        'not_in' => 'The selected :field is invalid.',
        'mimes' => 'The :field must be a file of type: :param.',
        'image' => 'The :field must be an image.',
    ];

    /**
     * Create a new Validator instance
     * 
     * @param array<string, mixed> $data The data to validate
     * @param array<string, string> $rules The validation rules
     */
    public function __construct(array $data = [], array $rules = [])
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    /**
     * Set the data to validate
     * 
     * @param array<string, mixed> $data The data to validate
     * @return self
     */
    public function withData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set the validation rules
     * 
     * @param array<string, string> $rules The validation rules
     * @return self
     */
    public function withRules(array $rules): self
    {
        $this->rules = $rules;
        return $this;
    }

    /**
     * Set custom error messages
     * 
     * @param array<string, string> $messages The custom messages
     * @return self
     */
    public function withMessages(array $messages): self
    {
        $this->customMessages = $messages;
        return $this;
    }

    /**
     * Validate the data
     * 
     * @return bool True if validation passes
     */
    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);

            foreach ($fieldRules as $rule) {
                $ruleParts = explode(':', $rule, 2);
                $ruleName = $ruleParts[0];
                $ruleParam = $ruleParts[1] ?? null;

                $value = $this->getFieldValue($field);

                if ($this->shouldSkipRule($ruleName, $value)) {
                    continue;
                }

                $valid = $this->validateRule($field, $value, $ruleName, $ruleParam);

                if (!$valid) {
                    $this->addError($field, $ruleName, $ruleParam);
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Get the value for a field
     * 
     * @param string $field The field name
     * @return mixed The field value
     */
    private function getFieldValue(string $field): mixed
    {
        // Support dot notation for nested arrays
        if (str_contains($field, '.')) {
            $parts = explode('.', $field);
            $value = $this->data;

            foreach ($parts as $part) {
                if (!is_array($value) || !array_key_exists($part, $value)) {
                    return null;
                }
                $value = $value[$part];
            }

            return $value;
        }

        return $this->data[$field] ?? null;
    }

    /**
     * Check if a rule should be skipped
     * 
     * @param string $ruleName The rule name
     * @param mixed $value The field value
     * @return bool True if the rule should be skipped
     */
    private function shouldSkipRule(string $ruleName, mixed $value): bool
    {
        // Skip validation if value is empty and rule is not 'required'
        if ($ruleName !== 'required' && empty($value) && $value !== '0') {
            return true;
        }

        return false;
    }

    /**
     * Validate a single rule
     * 
     * @param string $field The field name
     * @param mixed $value The field value
     * @param string $ruleName The rule name
     * @param string|null $ruleParam The rule parameter
     * @return bool True if validation passes
     */
    private function validateRule(string $field, mixed $value, string $ruleName, ?string $ruleParam): bool
    {
        return match ($ruleName) {
            'required' => $this->validateRequired($value),
            'email' => $this->validateEmail($value),
            'numeric' => $this->validateNumeric($value),
            'integer' => $this->validateInteger($value),
            'min' => $this->validateMin($value, $ruleParam),
            'max' => $this->validateMax($value, $ruleParam),
            'between' => $this->validateBetween($value, $ruleParam),
            'alpha' => $this->validateAlpha($value),
            'alpha_num' => $this->validateAlphaNum($value),
            'alpha_dash' => $this->validateAlphaDash($value),
            'confirmed' => $this->validateConfirmed($field, $value),
            'unique' => $this->validateUnique($field, $value, $ruleParam),
            'exists' => $this->validateExists($field, $value, $ruleParam),
            'date' => $this->validateDate($value),
            'date_format' => $this->validateDateFormat($value, $ruleParam),
            'url' => $this->validateUrl($value),
            'ip' => $this->validateIp($value),
            'regex' => $this->validateRegex($value, $ruleParam),
            'size' => $this->validateSize($value, $ruleParam),
            'in' => $this->validateIn($value, $ruleParam),
            'not_in' => $this->validateNotIn($value, $ruleParam),
            'mimes' => $this->validateMimes($field, $value, $ruleParam),
            'image' => $this->validateImage($field, $value),
            default => true,
        };
    }

    /**
     * Add an error message
     * 
     * @param string $field The field name
     * @param string $ruleName The rule name
     * @param string|null $ruleParam The rule parameter
     */
    private function addError(string $field, string $ruleName, ?string $ruleParam): void
    {
        $message = $this->getErrorMessage($field, $ruleName, $ruleParam);
        $this->errors[$field] = $message;
    }

    /**
     * Get the error message for a rule
     * 
     * @param string $field The field name
     * @param string $ruleName The rule name
     * @param string|null $ruleParam The rule parameter
     * @return string The error message
     */
    private function getErrorMessage(string $field, string $ruleName, ?string $ruleParam): string
    {
        // Check for custom message
        $fieldKey = $field . '.' . $ruleName;
        if (isset($this->customMessages[$fieldKey])) {
            return $this->customMessages[$fieldKey];
        }

        // Check for rule-specific custom message
        if (isset($this->customMessages[$ruleName])) {
            return $this->customMessages[$ruleName];
        }

        // Use default message
        $message = self::$defaultMessages[$ruleName] ?? 'The :field field is invalid.';

        // Replace placeholders
        $message = str_replace(':field', $this->getFieldLabel($field), $message);

        if ($ruleParam !== null) {
            $message = str_replace(':param', $ruleParam, $message);

            // Handle multiple parameters (e.g., between:min,max)
            if (str_contains($ruleParam, ',')) {
                $params = explode(',', $ruleParam);
                $message = str_replace(':param1', $params[0] ?? '', $message);
                $message = str_replace(':param2', $params[1] ?? '', $message);
            }
        }

        return $message;
    }

    /**
     * Get the label for a field
     * 
     * @param string $field The field name
     * @return string The field label
     */
    private function getFieldLabel(string $field): string
    {
        // Convert field name to human-readable format
        $label = str_replace('.', ' ', $field);
        $label = str_replace('_', ' ', $label);
        $label = ucwords($label);
        return $label;
    }

    /**
     * Check if validation passes
     * 
     * @return bool True if validation passes
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Check if validation fails
     * 
     * @return bool True if validation fails
     */
    public function fails(): bool
    {
        return !$this->passes();
    }

    /**
     * Get all error messages
     * 
     * @return array<string, string> The error messages
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get the first error message
     * 
     * @return string|null The first error message or null if no errors
     */
    public function getFirstError(): ?string
    {
        return $this->errors[array_key_first($this->errors)] ?? null;
    }

    /**
     * Get error messages for a specific field
     * 
     * @param string $field The field name
     * @return string|null The error message or null if no error
     */
    public function getError(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }

    /**
     * Validate required
     * 
     * @param mixed $value The value to validate
     * @return bool True if validation passes
     */
    private function validateRequired(mixed $value): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_string($value) && trim($value) === '') {
            return false;
        }

        if (is_array($value) && empty($value)) {
            return false;
        }

        return true;
    }

    /**
     * Validate email
     * 
     * @param mixed $value The value to validate
     * @return bool True if validation passes
     */
    private function validateEmail(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate numeric
     * 
     * @param mixed $value The value to validate
     * @return bool True if validation passes
     */
    private function validateNumeric(mixed $value): bool
    {
        return is_numeric($value);
    }

    /**
     * Validate integer
     * 
     * @param mixed $value The value to validate
     * @return bool True if validation passes
     */
    private function validateInteger(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Validate minimum length
     * 
     * @param mixed $value The value to validate
     * @param string|null $param The minimum length
     * @return bool True if validation passes
     */
    private function validateMin(mixed $value, ?string $param): bool
    {
        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }

        $min = (int)$param;
        return strlen((string)$value) >= $min;
    }

    /**
     * Validate maximum length
     * 
     * @param mixed $value The value to validate
     * @param string|null $param The maximum length
     * @return bool True if validation passes
     */
    private function validateMax(mixed $value, ?string $param): bool
    {
        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }

        $max = (int)$param;
        return strlen((string)$value) <= $max;
    }

    /**
     * Validate between
     * 
     * @param mixed $value The value to validate
     * @param string|null $param The min and max values (min,max)
     * @return bool True if validation passes
     */
    private function validateBetween(mixed $value, ?string $param): bool
    {
        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }

        $parts = explode(',', $param ?? '');
        $min = (int)($parts[0] ?? 0);
        $max = (int)($parts[1] ?? PHP_INT_MAX);

        $length = strlen((string)$value);
        return $length >= $min && $length <= $max;
    }

    /**
     * Validate alpha
     * 
     * @param mixed $value The value to validate
     * @return bool True if validation passes
     */
    private function validateAlpha(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return ctype_alpha($value);
    }

    /**
     * Validate alpha numeric
     * 
     * @param mixed $value The value to validate
     * @return bool True if validation passes
     */
    private function validateAlphaNum(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return ctype_alnum($value);
    }

    /**
     * Validate alpha dash
     * 
     * @param mixed $value The value to validate
     * @return bool True if validation passes
     */
    private function validateAlphaDash(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return preg_match('/^[a-zA-Z0-9_\-]+$/', $value) === 1;
    }

    /**
     * Validate confirmed
     * 
     * @param string $field The field name
     * @param mixed $value The field value
     * @return bool True if validation passes
     */
    private function validateConfirmed(string $field, mixed $value): bool
    {
        $confirmedField = $field . '_confirmation';
        $confirmedValue = $this->getFieldValue($confirmedField);

        return $value === $confirmedValue;
    }

    /**
     * Validate unique
     * 
     * @param string $field The field name
     * @param mixed $value The field value
     * @param string|null $param The table name
     * @return bool True if validation passes
     */
    private function validateUnique(string $field, mixed $value, ?string $param): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        $table = $param ?? $field;

        $database = Application::getInstance()->getContainer()->resolve(Database::class);

        // Check if there's an ID to exclude (for updates)
        $id = $this->getFieldValue('id');
        $sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s = ?', 
            $database->quoteIdentifier($table),
            $database->quoteIdentifier($field)
        );
        $params = [$value];

        if ($id !== null) {
            $sql .= ' AND id != ?';
            $params[] = $id;
        }

        $count = (int)$database->selectValue($sql, $params);

        return $count === 0;
    }

    /**
     * Validate exists
     * 
     * @param string $field The field name
     * @param mixed $value The field value
     * @param string|null $param The table name
     * @return bool True if validation passes
     */
    private function validateExists(string $field, mixed $value, ?string $param): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        $table = $param ?? $field;

        $database = Application::getInstance()->getContainer()->resolve(Database::class);

        $sql = sprintf(
            'SELECT COUNT(*) FROM %s WHERE %s = ?',
            $database->quoteIdentifier($table),
            $database->quoteIdentifier($field)
        );

        $count = (int)$database->selectValue($sql, [$value]);

        return $count > 0;
    }

    /**
     * Validate date
     * 
     * @param mixed $value The value to validate
     * @return bool True if validation passes
     */
    private function validateDate(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return strtotime($value) !== false;
    }

    /**
     * Validate date format
     * 
     * @param mixed $value The value to validate
     * @param string|null $param The date format
     * @return bool True if validation passes
     */
    private function validateDateFormat(mixed $value, ?string $param): bool
    {
        if (!is_string($value) || $param === null) {
            return false;
        }

        $date = DateTime::createFromFormat($param, $value);
        return $date !== false && $date->format($param) === $value;
    }

    /**
     * Validate URL
     * 
     * @param mixed $value The value to validate
     * @return bool True if validation passes
     */
    private function validateUrl(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validate IP
     * 
     * @param mixed $value The value to validate
     * @return bool True if validation passes
     */
    private function validateIp(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Validate regex
     * 
     * @param mixed $value The value to validate
     * @param string|null $param The regex pattern
     * @return bool True if validation passes
     */
    private function validateRegex(mixed $value, ?string $param): bool
    {
        if (!is_string($value) || $param === null) {
            return false;
        }

        return preg_match($param, $value) === 1;
    }

    /**
     * Validate size
     * 
     * @param mixed $value The value to validate
     * @param string|null $param The exact size
     * @return bool True if validation passes
     */
    private function validateSize(mixed $value, ?string $param): bool
    {
        if ($param === null) {
            return false;
        }

        $size = (int)$param;

        if (is_string($value)) {
            return strlen($value) === $size;
        }

        if (is_numeric($value)) {
            return (int)$value === $size;
        }

        if (is_array($value)) {
            return count($value) === $size;
        }

        return false;
    }

    /**
     * Validate in
     * 
     * @param mixed $value The value to validate
     * @param string|null $param The allowed values (comma-separated)
     * @return bool True if validation passes
     */
    private function validateIn(mixed $value, ?string $param): bool
    {
        if ($param === null) {
            return false;
        }

        $allowed = explode(',', $param);
        return in_array($value, $allowed, true);
    }

    /**
     * Validate not in
     * 
     * @param mixed $value The value to validate
     * @param string|null $param The disallowed values (comma-separated)
     * @return bool True if validation passes
     */
    private function validateNotIn(mixed $value, ?string $param): bool
    {
        if ($param === null) {
            return true;
        }

        $disallowed = explode(',', $param);
        return !in_array($value, $disallowed, true);
    }

    /**
     * Validate mimes
     * 
     * @param string $field The field name
     * @param mixed $value The value to validate
     * @param string|null $param The allowed MIME types (comma-separated)
     * @return bool True if validation passes
     */
    private function validateMimes(string $field, mixed $value, ?string $param): bool
    {
        if (!isset($_FILES[$field])) {
            return false;
        }

        $file = $_FILES[$field];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if ($param === null) {
            return true;
        }

        $allowedMimes = explode(',', $param);
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        return in_array($mime, $allowedMimes, true);
    }

    /**
     * Validate image
     * 
     * @param string $field The field name
     * @param mixed $value The value to validate
     * @return bool True if validation passes
     */
    private function validateImage(string $field, mixed $value): bool
    {
        if (!isset($_FILES[$field])) {
            return false;
        }

        $file = $_FILES[$field];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        $imageMimes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
        ];

        return in_array($mime, $imageMimes, true);
    }

    /**
     * Validate a value using a custom callback
     * 
     * @param string $field The field name
     * @param mixed $value The value to validate
     * @param callable $callback The validation callback
     * @param string|null $message The custom error message
     * @return self
     */
    public function sometimes(string $field, callable $callback, ?string $message = null): self
    {
        if ($message !== null) {
            $this->customMessages[$field . '.sometimes'] = $message;
        }

        if (!isset($this->data[$field])) {
            return $this;
        }

        $result = $callback($this->data[$field]);

        if (!$result) {
            $this->errors[$field] = $message ?? 'The :field field is invalid.';
        }

        return $this;
    }

    /**
     * Add a custom validation rule
     * 
     * @param string $name The rule name
     * @param callable $callback The validation callback
     * @param string $message The error message
     */
    public static function extend(string $name, callable $callback, string $message): void
    {
        self::$defaultMessages[$name] = $message;
    }
}
