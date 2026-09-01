<?php

declare(strict_types=1);

namespace DDWB;

/**
 * Base Model
 * 
 * All models should extend this base class
 */
abstract class Model
{
    protected Database $database;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $guarded = [];
    protected array $casts = [];
    protected array $dates = [];

    /**
     * Create a new Model instance
     * 
     * @param Database $database The database instance
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->table = $this->getTableName();
    }

    /**
     * Get the table name
     * 
     * @return string The table name
     */
    protected function getTableName(): string
    {
        // Convert class name to snake_case table name
        $className = substr(strrchr(get_class($this), '\\') ?: get_class($this), 1);
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $className)) . 's';
    }

    /**
     * Get the database instance
     * 
     * @return Database The database instance
     */
    public function getDatabase(): Database
    {
        return $this->database;
    }

    /**
     * Get the table name
     * 
     * @return string The table name
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Get the primary key
     * 
     * @return string The primary key
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * Set the table name
     * 
     * @param string $table The table name
     * @return self
     */
    public function setTable(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Set the primary key
     * 
     * @param string $primaryKey The primary key
     * @return self
     */
    public function setPrimaryKey(string $primaryKey): self
    {
        $this->primaryKey = $primaryKey;
        return $this;
    }

    /**
     * Get all records
     * 
     * @param array $columns The columns to select
     * @return array The records
     */
    public function all(array $columns = ['*']): array
    {
        $sql = sprintf(
            'SELECT %s FROM %s',
            implode(', ', array_map(fn($col) => $this->database->quoteIdentifier($col), $columns)),
            $this->database->quoteIdentifier($this->table)
        );

        return $this->database->select($sql);
    }

    /**
     * Find a record by ID
     * 
     * @param int|string $id The record ID
     * @param array $columns The columns to select
     * @return array|null The record or null if not found
     */
    public function find(int|string $id, array $columns = ['*']): ?array
    {
        $sql = sprintf(
            'SELECT %s FROM %s WHERE %s = ? LIMIT 1',
            implode(', ', array_map(fn($col) => $this->database->quoteIdentifier($col), $columns)),
            $this->database->quoteIdentifier($this->table),
            $this->database->quoteIdentifier($this->primaryKey)
        );

        return $this->database->selectOne($sql, [$id]);
    }

    /**
     * Find a record by a field
     * 
     * @param string $field The field name
     * @param mixed $value The field value
     * @param array $columns The columns to select
     * @return array|null The record or null if not found
     */
    public function findBy(string $field, mixed $value, array $columns = ['*']): ?array
    {
        $sql = sprintf(
            'SELECT %s FROM %s WHERE %s = ? LIMIT 1',
            implode(', ', array_map(fn($col) => $this->database->quoteIdentifier($col), $columns)),
            $this->database->quoteIdentifier($this->table),
            $this->database->quoteIdentifier($field)
        );

        return $this->database->selectOne($sql, [$value]);
    }

    /**
     * Get records by a field
     * 
     * @param string $field The field name
     * @param mixed $value The field value
     * @param array $columns The columns to select
     * @return array The records
     */
    public function getBy(string $field, mixed $value, array $columns = ['*']): array
    {
        $sql = sprintf(
            'SELECT %s FROM %s WHERE %s = ?',
            implode(', ', array_map(fn($col) => $this->database->quoteIdentifier($col), $columns)),
            $this->database->quoteIdentifier($this->table),
            $this->database->quoteIdentifier($field)
        );

        return $this->database->select($sql, [$value]);
    }

    /**
     * Create a new record
     * 
     * @param array $data The data to create
     * @return int|string The new record ID
     */
    public function create(array $data): int|string
    {
        // Apply timestamps
        $data = $this->applyTimestamps($data, 'create');

        // Filter fillable fields
        $data = $this->filterFillable($data);

        return $this->database->insert($this->table, $data);
    }

    /**
     * Update a record
     * 
     * @param int|string $id The record ID
     * @param array $data The data to update
     * @return int The number of affected rows
     */
    public function update(int|string $id, array $data): int
    {
        // Apply timestamps
        $data = $this->applyTimestamps($data, 'update');

        // Filter fillable fields
        $data = $this->filterFillable($data);

        return $this->database->update(
            $this->table,
            $data,
            [$this->primaryKey => $id]
        );
    }

    /**
     * Delete a record
     * 
     * @param int|string $id The record ID
     * @return int The number of affected rows
     */
    public function delete(int|string $id): int
    {
        return $this->database->delete($this->table, [$this->primaryKey => $id]);
    }

    /**
     * Delete records by a field
     * 
     * @param string $field The field name
     * @param mixed $value The field value
     * @return int The number of affected rows
     */
    public function deleteBy(string $field, mixed $value): int
    {
        return $this->database->delete($this->table, [$field => $value]);
    }

    /**
     * Count records
     * 
     * @return int The number of records
     */
    public function count(): int
    {
        return $this->database->count($this->table);
    }

    /**
     * Check if a record exists
     * 
     * @param int|string|null $id The record ID (optional)
     * @return bool True if a record exists
     */
    public function exists(?int $id = null): bool
    {
        if ($id !== null) {
            return $this->database->exists($this->table, [$this->primaryKey => $id]);
        }

        return $this->count() > 0;
    }

    /**
     * Query records with conditions
     * 
     * @param array $conditions The WHERE conditions
     * @param array $columns The columns to select
     * @param string|null $orderBy The ORDER BY clause
     * @param string|null $groupBy The GROUP BY clause
     * @param int|null $limit The LIMIT
     * @param int|null $offset The OFFSET
     * @return array The records
     */
    public function where(
        array $conditions = [],
        array $columns = ['*'],
        ?string $orderBy = null,
        ?string $groupBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $sql = sprintf(
            'SELECT %s FROM %s',
            implode(', ', array_map(fn($col) => $this->database->quoteIdentifier($col), $columns)),
            $this->database->quoteIdentifier($this->table)
        );

        $params = [];

        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $field => $value) {
                $whereClauses[] = $this->database->quoteIdentifier($field) . ' = ?';
                $params[] = $value;
            }
            $sql .= ' WHERE ' . implode(' AND ', $whereClauses);
        }

        if ($groupBy !== null) {
            $sql .= ' GROUP BY ' . $groupBy;
        }

        if ($orderBy !== null) {
            $sql .= ' ORDER BY ' . $orderBy;
        }

        if ($limit !== null) {
            $sql .= ' LIMIT ' . $limit;
            if ($offset !== null) {
                $sql .= ' OFFSET ' . $offset;
            }
        }

        return $this->database->select($sql, $params);
    }

    /**
     * Get the first record matching conditions
     * 
     * @param array $conditions The WHERE conditions
     * @param array $columns The columns to select
     * @param string|null $orderBy The ORDER BY clause
     * @return array|null The first record or null if not found
     */
    public function first(array $conditions = [], array $columns = ['*'], ?string $orderBy = null): ?array
    {
        $records = $this->where($conditions, $columns, $orderBy, null, 1);
        return $records[0] ?? null;
    }

    /**
     * Paginate records
     * 
     * @param int $perPage The number of records per page
     * @param array $conditions The WHERE conditions
     * @param array $columns The columns to select
     * @param string|null $orderBy The ORDER BY clause
     * @param int $currentPage The current page number
     * @return array The paginated results
     */
    public function paginate(
        int $perPage = 25,
        array $conditions = [],
        array $columns = ['*'],
        ?string $orderBy = null,
        int $currentPage = 1
    ): array {
        $total = $this->database->count($this->table, $conditions);
        $offset = ($currentPage - 1) * $perPage;

        $records = $this->where($conditions, $columns, $orderBy, null, $perPage, $offset);

        $totalPages = (int)ceil($total / $perPage);

        return [
            'data' => $records,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total),
        ];
    }

    /**
     * Search records
     * 
     * @param string $query The search query
     * @param array $columns The columns to search in
     * @param array $selectColumns The columns to select
     * @return array The matching records
     */
    public function search(string $query, array $columns = [], array $selectColumns = ['*']): array
    {
        if (empty($columns)) {
            $columns = ['*'];
        }

        $whereClauses = [];
        $params = [];

        foreach ($columns as $column) {
            $whereClauses[] = $this->database->quoteIdentifier($column) . ' LIKE ?';
            $params[] = '%' . $query . '%';
        }

        $sql = sprintf(
            'SELECT %s FROM %s WHERE %s',
            implode(', ', array_map(fn($col) => $this->database->quoteIdentifier($col), $selectColumns)),
            $this->database->quoteIdentifier($this->table),
            implode(' OR ', $whereClauses)
        );

        return $this->database->select($sql, $params);
    }

    /**
     * Apply timestamps to data
     * 
     * @param array $data The data
     * @param string $action The action (create or update)
     * @return array The data with timestamps
     */
    protected function applyTimestamps(array $data, string $action): array
    {
        $timestamp = date('Y-m-d H:i:s');

        if ($action === 'create') {
            if (!isset($data['created_at']) && in_array('created_at', $this->dates, true)) {
                $data['created_at'] = $timestamp;
            }
            if (!isset($data['updated_at']) && in_array('updated_at', $this->dates, true)) {
                $data['updated_at'] = $timestamp;
            }
        } elseif ($action === 'update') {
            if (!isset($data['updated_at']) && in_array('updated_at', $this->dates, true)) {
                $data['updated_at'] = $timestamp;
            }
        }

        return $data;
    }

    /**
     * Filter fillable fields
     * 
     * @param array $data The data
     * @return array The filtered data
     */
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }

        $filtered = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $this->fillable, true)) {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }

    /**
     * Cast a value
     * 
     * @param string $key The field key
     * @param mixed $value The value to cast
     * @return mixed The casted value
     */
    protected function castValue(string $key, mixed $value): mixed
    {
        if (!isset($this->casts[$key])) {
            return $value;
        }

        $cast = $this->casts[$key];

        return match ($cast) {
            'int', 'integer' => (int)$value,
            'float', 'double' => (float)$value,
            'bool', 'boolean' => (bool)$value,
            'string' => (string)$value,
            'array' => is_string($value) ? json_decode($value, true) : (array)$value,
            'object' => is_string($value) ? json_decode($value) : (object)$value,
            'json' => is_array($value) || is_object($value) ? json_encode($value) : $value,
            'date', 'datetime' => $value,
            default => $value,
        };
    }

    /**
     * Get a casted attribute
     * 
     * @param array $record The record
     * @param string $key The attribute key
     * @return mixed The casted attribute
     */
    public function getCastedAttribute(array $record, string $key): mixed
    {
        if (!array_key_exists($key, $record)) {
            return null;
        }

        return $this->castValue($key, $record[$key]);
    }

    /**
     * Convert a record to an array with casted attributes
     * 
     * @param array $record The record
     * @return array The record with casted attributes
     */
    public function castAttributes(array $record): array
    {
        foreach ($this->casts as $key => $cast) {
            if (array_key_exists($key, $record)) {
                $record[$key] = $this->castValue($key, $record[$key]);
            }
        }

        return $record;
    }

    /**
     * Convert multiple records to arrays with casted attributes
     * 
     * @param array $records The records
     * @return array The records with casted attributes
     */
    public function castAllAttributes(array $records): array
    {
        return array_map([$this, 'castAttributes'], $records);
    }

    /**
     * Begin a transaction
     * 
     * @return bool True if the transaction was started
     */
    public function beginTransaction(): bool
    {
        return $this->database->beginTransaction();
    }

    /**
     * Commit a transaction
     * 
     * @return bool True if the transaction was committed
     */
    public function commit(): bool
    {
        return $this->database->commit();
    }

    /**
     * Rollback a transaction
     * 
     * @return bool True if the transaction was rolled back
     */
    public function rollback(): bool
    {
        return $this->database->rollback();
    }

    /**
     * Execute a callback within a transaction
     * 
     * @param \Closure $callback The callback to execute
     * @return mixed The callback return value
     * @throws \Exception If the transaction fails
     */
    public function transaction(\Closure $callback): mixed
    {
        return $this->database->transaction($callback);
    }

    /**
     * Get the next available internal ID
     * 
     * @param string $prefix The prefix (e.g., DEV, CASE)
     * @return string The next internal ID
     */
    public function getNextInternalId(string $prefix): string
    {
        $sql = sprintf(
            'SELECT MAX(%s) as max_id FROM %s WHERE %s LIKE ?',
            $this->database->quoteIdentifier('internal_id'),
            $this->database->quoteIdentifier($this->table),
            $this->database->quoteIdentifier('internal_id')
        );

        $result = $this->database->selectOne($sql, [$prefix . '-%']);
        $maxId = $result['max_id'] ?? null;

        if ($maxId === null) {
            return generate_internal_id($prefix, 1);
        }

        // Extract the number from the internal ID
        $parts = explode('-', $maxId);
        $number = (int)(end($parts) ?? 0);

        return generate_internal_id($prefix, $number + 1);
    }

    /**
     * Increment a field
     * 
     * @param int|string $id The record ID
     * @param string $field The field to increment
     * @param int $amount The amount to increment by (default: 1)
     * @return int The number of affected rows
     */
    public function increment(int|string $id, string $field, int $amount = 1): int
    {
        return $this->database->execute(
            sprintf(
                'UPDATE %s SET %s = %s + ? WHERE %s = ?',
                $this->database->quoteIdentifier($this->table),
                $this->database->quoteIdentifier($field),
                $this->database->quoteIdentifier($field),
                $this->database->quoteIdentifier($this->primaryKey)
            ),
            [$amount, $id]
        );
    }

    /**
     * Decrement a field
     * 
     * @param int|string $id The record ID
     * @param string $field The field to decrement
     * @param int $amount The amount to decrement by (default: 1)
     * @return int The number of affected rows
     */
    public function decrement(int|string $id, string $field, int $amount = 1): int
    {
        return $this->increment($id, $field, -$amount);
    }

    /**
     * Soft delete a record
     * 
     * @param int|string $id The record ID
     * @param string $deletedAtField The deleted_at field name (default: deleted_at)
     * @return int The number of affected rows
     */
    public function softDelete(int|string $id, string $deletedAtField = 'deleted_at'): int
    {
        return $this->database->update(
            $this->table,
            [$deletedAtField => date('Y-m-d H:i:s')],
            [$this->primaryKey => $id]
        );
    }

    /**
     * Restore a soft-deleted record
     * 
     * @param int|string $id The record ID
     * @param string $deletedAtField The deleted_at field name (default: deleted_at)
     * @return int The number of affected rows
     */
    public function restore(int|string $id, string $deletedAtField = 'deleted_at'): int
    {
        return $this->database->update(
            $this->table,
            [$deletedAtField => null],
            [$this->primaryKey => $id]
        );
    }

    /**
     * Check if a record is soft-deleted
     * 
     * @param array $record The record
     * @param string $deletedAtField The deleted_at field name (default: deleted_at)
     * @return bool True if the record is soft-deleted
     */
    public function isSoftDeleted(array $record, string $deletedAtField = 'deleted_at'): bool
    {
        return isset($record[$deletedAtField]) && $record[$deletedAtField] !== null;
    }

    /**
     * Scope to only non-deleted records
     * 
     * @param string $deletedAtField The deleted_at field name (default: deleted_at)
     * @return self
     */
    public function whereNotDeleted(string $deletedAtField = 'deleted_at'): self
    {
        // This is a placeholder for query builder functionality
        // In a full query builder, this would return a query instance
        return $this;
    }

    /**
     * Scope to only deleted records
     * 
     * @param string $deletedAtField The deleted_at field name (default: deleted_at)
     * @return self
     */
    public function whereDeleted(string $deletedAtField = 'deleted_at'): self
    {
        // This is a placeholder for query builder functionality
        return $this;
    }
}
