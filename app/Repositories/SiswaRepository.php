<?php

declare(strict_types=1);

namespace App\Repositories;

/**
 * Siswa Repository
 *
 * Encapsulates all complex SQL queries related to the `siswa` table.
 * Controllers/Services depend on this, not on raw DB calls.
 */
final class SiswaRepository extends BaseRepository
{
    protected string $table = 'siswa';

    // ── Read ──────────────────────────────────────────────────────────────────

    /**
     * Find a student by NISN.
     */
    public function findByNisn(string $nisn): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE nisn = :nisn LIMIT 1",
            ['nisn' => $nisn],
        );
    }

    /**
     * Paginated list with optional search and year filter.
     *
     * @return array{data: array, total: int, page: int, perPage: int, lastPage: int}
     */
    public function paginate(
        int     $page    = 1,
        int     $perPage = 15,
        string  $search  = '',
        ?int    $tahun   = null,
        ?string $status  = null,
    ): array {
        $conditions = ['1=1'];
        $bindings   = [];

        if ($search !== '') {
            $conditions[] = "(nisn LIKE :search OR nama LIKE :search)";
            $bindings['search'] = "%{$search}%";
        }

        if ($tahun !== null) {
            $conditions[]       = "tahun_lulus = :tahun";
            $bindings['tahun']  = $tahun;
        }

        if ($status !== null) {
            $conditions[]        = "status_kelulusan = :status";
            $bindings['status']  = $status;
        }

        $where  = implode(' AND ', $conditions);
        $offset = ($page - 1) * $perPage;

        $total = (int) ($this->db->fetchOne(
            "SELECT COUNT(*) AS c FROM {$this->table} WHERE {$where}",
            $bindings,
        )['c'] ?? 0);

        $data = $this->db->fetchAll(
            "SELECT * FROM {$this->table}
              WHERE {$where}
           ORDER BY nama ASC
              LIMIT :limit OFFSET :offset",
            array_merge($bindings, ['limit' => $perPage, 'offset' => $offset]),
        );

        return [
            'data'     => $data,
            'total'    => $total,
            'page'     => $page,
            'perPage'  => $perPage,
            'lastPage' => (int) ceil($total / $perPage) ?: 1,
        ];
    }

    /**
     * Aggregate statistics grouped by graduation year.
     */
    public function statistik(): array
    {
        return $this->db->fetchAll(
            "SELECT tahun_lulus,
                    COUNT(*) AS total,
                    SUM(status_kelulusan = 'lulus') AS lulus,
                    SUM(status_kelulusan = 'tidak_lulus') AS tidak_lulus,
                    ROUND(AVG(nilai_rata_rata), 2) AS rata_nilai
               FROM {$this->table}
           GROUP BY tahun_lulus
           ORDER BY tahun_lulus DESC"
        );
    }

    /**
     * Get available graduation years (for filter dropdowns).
     */
    public function availableYears(): array
    {
        return $this->db->fetchAll(
            "SELECT DISTINCT tahun_lulus FROM {$this->table} ORDER BY tahun_lulus DESC"
        );
    }
}
