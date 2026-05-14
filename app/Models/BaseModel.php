<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Base Model
 *
 * Provides Active-Record-style helpers on top of the Database wrapper.
 * Subclasses must define $table and (optionally) $fillable.
 */
abstract class BaseModel
{
    protected string $table     = '';
    protected string $primary   = 'id';

    /** Columns that may be mass-assigned */
    protected array $fillable   = [];

    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── Finders ──────────────────────────────────────────────────────────────

    /**
     * Find a single record by primary key.
     */
    public function find(int|string $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE {$this->primary} = :id LIMIT 1",
            ['id' => $id],
        );
    }

    /**
     * Find a single record matching column = value.
     */
    public function findBy(string $column, mixed $value): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE {$column} = :value LIMIT 1",
            ['value' => $value],
        );
    }

    /**
     * Return all rows from the table.
     */
    public function all(string $orderBy = ''): array
    {
        $order = $orderBy ? "ORDER BY {$orderBy}" : '';
        return $this->db->fetchAll("SELECT * FROM {$this->table} {$order}");
    }

    /**
     * Fetch records matching a WHERE clause.
     */
    public function where(string $condition, array $bindings = []): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE {$condition}",
            $bindings,
        );
    }

    /**
     * Count records with optional WHERE.
     */
    public function count(string $condition = '1=1', array $bindings = []): int
    {
        $row = $this->db->fetchOne(
            "SELECT COUNT(*) AS total FROM {$this->table} WHERE {$condition}",
            $bindings,
        );

        return (int) ($row['total'] ?? 0);
    }

    // ── Mutations ─────────────────────────────────────────────────────────────

    /**
     * Insert a new record after filtering through $fillable.
     */
    public function create(array $data): string|false
    {
        return $this->db->insert($this->table, $this->filterFillable($data));
    }

    /**
     * Update a record by primary key.
     */
    public function update(int|string $id, array $data): int
    {
        return $this->db->update(
            $this->table,
            $this->filterFillable($data),
            "{$this->primary} = :__pk",
            ['__pk' => $id],
        );
    }

    /**
     * Delete a record by primary key.
     */
    public function delete(int|string $id): int
    {
        return $this->db->delete(
            $this->table,
            "{$this->primary} = :id",
            ['id' => $id],
        );
    }

    // ── Pagination ────────────────────────────────────────────────────────────

    /**
     * Return a paginated result set.
     *
     * @return array{data: array, total: int, page: int, perPage: int, lastPage: int}
     */
    public function paginate(int $page = 1, int $perPage = 15, string $condition = '1=1', array $bindings = []): array
    {
        $total    = $this->count($condition, $bindings);
        $offset   = ($page - 1) * $perPage;
        $lastPage = (int) ceil($total / $perPage);

        $data = $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE {$condition} LIMIT :limit OFFSET :offset",
            array_merge($bindings, ['limit' => $perPage, 'offset' => $offset]),
        );

        return compact('data', 'total', 'page', 'perPage', 'lastPage');
    }

    // ── Internal ──────────────────────────────────────────────────────────────

    private function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }
}
