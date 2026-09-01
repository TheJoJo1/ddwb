<?php

declare(strict_types=1);

namespace DDWB;

use PDO;
use PDOException;

/**
 * Database
 * 
 * PDO-based database connection and query builder
 */
final class Database
{
    private PDO $pdo;
    private array $config;

    /**
     * Create a new Database instance
     * 
     * @param array $config The database configuration
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->connect();
    }

    /**
     * Connect to the database
     * 
     * @throws PDOException If connection fails
     */
    private function connect(): void
    {
        $driver = $this->config['driver'] ?? 'mysql';
        $host = $this->config['host'] ?? 'localhost';
        $port = $this->config['port'] ?? 3306;
        $name = $this->config['name'] ?? '';
        $user = $this->config['user'] ?? '';
        $password = $this->config['password'] ?? '';
        $charset = $this->config['charset'] ?? 'utf8mb4';
        $options = $this->config['options'] ?? [];

        $dsn = match ($driver) {
            'mysql' => "mysql:host={$host};port={$port};dbname={$name};charset={$charset}",
            'pgsql' => "pgsql:host={$host};port={$port};dbname={$name}",
            'sqlite' => "sqlite:{$name}",
            default => throw new PDOException("Unsupported database driver: {$driver}"),
        };

        // Merge default options
        $defaultOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ];

        $this->pdo = new PDO($dsn, $user, $password, array_merge($defaultOptions, $options));
    }

    /**
     * Get the PDO instance
     * 
     * @return PDO The PDO instance
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Execute a SELECT query
     * 
     * @param string $sql The SQL query
     * @param array $params The query parameters
     * @param string|null $fetchStyle The fetch style (FETCH_ASSOC, FETCH_OBJ, etc.)
     * @return array The query results
     */
    public function select(string $sql, array $params = [], ?string $fetchStyle = null): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $fetchMode = $fetchStyle !== null ? constant('PDO::' . $fetchStyle) : PDO::FETCH_ASSOC;
        
        return $stmt->fetchAll($fetchMode);
    }

    /**
     * Execute a SELECT query and return a single row
     * 
     * @param string $sql The SQL query
     * @param array $params The query parameters
     * @param string|null $fetchStyle The fetch style
     * @return array|null The single row or null if no results
     */
    public function selectOne(string $sql, array $params = [], ?string $fetchStyle = null): ?array
    {
        $results = $this->select($sql, $params, $fetchStyle);
        return $results[0] ?? null;
    }

    /**
     * Execute a SELECT query and return a single column value
     * 
     * @param string $sql The SQL query
     * @param array $params The query parameters
     * @return mixed The column value or null if no results
     */
    public function selectValue(string $sql, array $params = []): mixed
    {
        $result = $this->selectOne($sql, $params);
        if ($result === null) {
            return null;
        }

        return array_values($result)[0] ?? null;
    }

    /**
     * Execute a SELECT query and return a single column as an array
     * 
     * @param string $sql The SQL query
     * @param array $params The query parameters
     * @return array The column values
     */
    public function selectColumn(string $sql, array $params = []): array
    {
        $results = $this->select($sql, $params);
        $column = [];

        foreach ($results as $row) {
            $column[] = array_values($row)[0];
        }

        return $column;
    }

    /**
     * Execute an INSERT query
     * 
     * @param string $table The table name
     * @param array $data The data to insert
     * @return int|string The last insert ID
     */
    public function insert(string $table, array $data): int|string
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":{$col}", $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->quoteIdentifier($table),
            implode(', ', array_map(fn($col) => $this->quoteIdentifier($col), $columns)),
            implode(', ', $placeholders)
        );

        $this->execute($sql, $data);

        return $this->pdo->lastInsertId();
    }

    /**
     * Execute an UPDATE query
     * 
     * @param string $table The table name
     * @param array $data The data to update
     * @param array|string $where The WHERE conditions
     * @param array $whereParams The WHERE parameters
     * @return int The number of affected rows
     */
    public function update(string $table, array $data, array|string $where = [], array $whereParams = []): int
    {
        $setParts = [];
        foreach ($data as $column => $value) {
            $setParts[] = $this->quoteIdentifier($column) . ' = :set_' . $column;
            $whereParams['set_' . $column] = $value;
        }

        $sql = sprintf(
            'UPDATE %s SET %s',
            $this->quoteIdentifier($table),
            implode(', ', $setParts)
        );

        if (!empty($where)) {
            $sql .= ' WHERE ' . $this->buildWhereClause($where, $whereParams);
        }

        return $this->execute($sql, $whereParams);
    }

    /**
     * Execute a DELETE query
     * 
     * @param string $table The table name
     * @param array|string $where The WHERE conditions
     * @param array $params The query parameters
     * @return int The number of affected rows
     */
    public function delete(string $table, array|string $where = [], array $params = []): int
    {
        $sql = sprintf('DELETE FROM %s', $this->quoteIdentifier($table));

        if (!empty($where)) {
            $sql .= ' WHERE ' . $this->buildWhereClause($where, $params);
        }

        return $this->execute($sql, $params);
    }

    /**
     * Execute a raw SQL query
     * 
     * @param string $sql The SQL query
     * @param array $params The query parameters
     * @return int The number of affected rows
     */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Begin a transaction
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit a transaction
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback a transaction
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
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
        $this->beginTransaction();

        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Quote an identifier (table name, column name, etc.)
     * 
     * @param string $identifier The identifier to quote
     * @return string The quoted identifier
     */
    public function quoteIdentifier(string $identifier): string
    {
        // Split identifier by dot (for table.column notation)
        $parts = explode('.', $identifier);
        $quoted = array_map(fn($part) => '`' . str_replace('`', '``', $part) . '`', $parts);
        return implode('.', $quoted);
    }

    /**
     * Build a WHERE clause from conditions
     * 
     * @param array|string $where The WHERE conditions
     * @param array $params The parameters array (will be modified)
     * @return string The WHERE clause
     */
    private function buildWhereClause(array|string $where, array &$params): string
    {
        if (is_string($where)) {
            return $where;
        }

        $clauses = [];
        
        foreach ($where as $column => $value) {
            $paramName = 'where_' . str_replace('.', '_', $column);
            $clauses[] = $this->quoteIdentifier($column) . ' = :' . $paramName;
            $params[$paramName] = $value;
        }

        return implode(' AND ', $clauses);
    }

    /**
     * Count rows in a table
     * 
     * @param string $table The table name
     * @param array|string $where The WHERE conditions
     * @param array $params The query parameters
     * @return int The row count
     */
    public function count(string $table, array|string $where = [], array $params = []): int
    {
        $sql = sprintf('SELECT COUNT(*) as count FROM %s', $this->quoteIdentifier($table));

        if (!empty($where)) {
            $sql .= ' WHERE ' . $this->buildWhereClause($where, $params);
        }

        return (int)$this->selectValue($sql, $params);
    }

    /**
     * Check if a row exists
     * 
     * @param string $table The table name
     * @param array|string $where The WHERE conditions
     * @param array $params The query parameters
     * @return bool True if a row exists
     */
    public function exists(string $table, array|string $where = [], array $params = []): bool
    {
        return $this->count($table, $where, $params) > 0;
    }

    /**
     * Get the ID of the last inserted row
     * 
     * @return int|string The last insert ID
     */
    public function lastInsertId(): int|string
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Get the number of affected rows from the last query
     * 
     * @return int The number of affected rows
     */
    public function rowCount(): int
    {
        return $this->pdo->rowCount();
    }

    /**
     * Prepare a statement
     * 
     * @param string $sql The SQL query
     * @return \PDOStatement The prepared statement
     */
    public function prepare(string $sql): \PDOStatement
    {
        return $this->pdo->prepare($sql);
    }

    /**
     * Quote a value for use in a query
     * 
     * @param mixed $value The value to quote
     * @return string The quoted value
     */
    public function quote(mixed $value): string
    {
        return $this->pdo->quote($value);
    }

    /**
     * Get the database driver name
     * 
     * @return string The driver name
     */
    public function getDriver(): string
    {
        return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    }
}
