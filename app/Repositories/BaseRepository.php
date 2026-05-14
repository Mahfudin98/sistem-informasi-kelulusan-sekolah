<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

/**
 * Base Repository
 *
 * Abstracts raw DB access behind a domain interface.
 * Subclasses add domain-specific query methods here,
 * keeping complex SQL out of Models and Controllers.
 */
abstract class BaseRepository
{
    protected Database $db;
    protected string $table = '';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int|string $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1",
            ['id' => $id],
        );
    }

    public function all(): array
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table}");
    }

    public function create(array $data): string|false
    {
        return $this->db->insert($this->table, $data);
    }

    public function update(int|string $id, array $data): int
    {
        return $this->db->update($this->table, $data, 'id = :id', ['id' => $id]);
    }

    public function delete(int|string $id): int
    {
        return $this->db->delete($this->table, 'id = :id', ['id' => $id]);
    }
}
