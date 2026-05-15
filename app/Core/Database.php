<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Database — PDO Singleton Wrapper
 *
 * Provides a single PDO connection with helper methods for
 * prepared-statement CRUD operations.
 */
final class Database
{
    private static ?self $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $this->connect();
    }

    // Prevent cloning / unserialization
    private function __clone() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    // ── Core Query Helpers ────────────────────────────────────────────────────

    /**
     * Execute a prepared statement and return the statement object.
     */
    public function query(string $sql, array $bindings = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt;
    }

    /**
     * Fetch a single row as an associative array.
     */
    public function fetchOne(string $sql, array $bindings = []): ?array
    {
        $result = $this->query($sql, $bindings)->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Fetch all rows as associative arrays.
     */
    public function fetchAll(string $sql, array $bindings = []): array
    {
        return $this->query($sql, $bindings)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Insert a row and return the last insert ID.
     */
    public function insert(string $table, array $data): string|false
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($k) => ":{$k}", array_keys($data)));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);

        return $this->pdo->lastInsertId();
    }

    /**
     * Update rows matching a WHERE clause.
     */
    public function update(string $table, array $data, string $where, array $whereBindings = []): int
    {
        $set = implode(', ', array_map(fn($k) => "{$k} = :{$k}", array_keys($data)));
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";

        $bindings = array_merge($data, $whereBindings);
        return $this->query($sql, $bindings)->rowCount();
    }

    /**
     * Delete rows matching a WHERE clause.
     */
    public function delete(string $table, string $where, array $bindings = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->query($sql, $bindings)->rowCount();
    }

    /**
     * Begin a transaction.
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    /**
     * Run a callable inside a transaction, auto-rollback on exception.
     */
    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();

        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }

    // ── Internal ──────────────────────────────────────────────────────────────

    private function connect(): void
    {
        $host    = env('DB_HOST', '127.0.0.1');
        $port    = env('DB_PORT', '3306');
        $name    = env('DB_NAME', '');
        $user    = env('DB_USER', 'root');
        $pass    = env('DB_PASS', '');
        $charset = env('DB_CHARSET', 'utf8mb4');

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT         => false,
            PDO::MYSQL_ATTR_FOUND_ROWS   => true,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // Never leak credentials in error messages
            throw new \RuntimeException(
                'Database connection failed. Check your .env configuration.',
                (int) $e->getCode(),
            );
        }
    }
}
