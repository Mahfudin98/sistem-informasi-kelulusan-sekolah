<?php

declare(strict_types=1);

namespace App\Repositories;

/**
 * Audit Repository — Handles logging and retrieving audit logs.
 */
final class AuditRepository extends BaseRepository
{
    protected string $table = 'audit_logs';

    /**
     * Get paginated logs.
     */
    public function paginate(int $page = 1, int $perPage = 25): array
    {
        $offset = ($page - 1) * $perPage;
        
        $total = (int) ($this->db->fetchOne("SELECT COUNT(*) as c FROM {$this->table}")['c'] ?? 0);
        
        $data = $this->db->fetchAll(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}"
        );

        return [
            'data'     => $data,
            'total'    => $total,
            'page'     => $page,
            'perPage'  => $perPage,
            'lastPage' => (int) ceil($total / $perPage) ?: 1,
        ];
    }
}
