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
        string  $sort    = 'nama',
        string  $order   = 'ASC'
    ): array {
        $conditions = ['1=1'];
        $bindings   = [];

        if ($search !== '') {
            $conditions[] = "(nisn LIKE :search1 OR nama LIKE :search2)";
            $bindings['search1'] = "%{$search}%";
            $bindings['search2'] = "%{$search}%";
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

        // Sort validation
        $allowedSort = ['nama', 'nisn', 'tahun_lulus', 'jurusan', 'status_kelulusan', 'nilai_rata_rata'];
        $sortColumn  = in_array($sort, $allowedSort) ? $sort : 'nama';
        $sortOrder   = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

        $data = $this->db->fetchAll(
            "SELECT * FROM {$this->table}
              WHERE {$where}
           ORDER BY {$sortColumn} {$sortOrder}
              LIMIT {$perPage} OFFSET {$offset}",
            $bindings,
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
     * Get all filtered records without pagination (for export).
     */
    public function getFilteredAll(
        string  $search  = '',
        ?int    $tahun   = null,
        ?string $status  = null,
        string  $sort    = 'nama',
        string  $order   = 'ASC'
    ): array {
        $conditions = ['1=1'];
        $bindings   = [];

        if ($search !== '') {
            $conditions[] = "(nisn LIKE :search1 OR nama LIKE :search2)";
            $bindings['search1'] = "%{$search}%";
            $bindings['search2'] = "%{$search}%";
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

        $allowedSort = ['nama', 'nisn', 'tahun_lulus', 'jurusan', 'status_kelulusan', 'nilai_rata_rata'];
        $sortColumn  = in_array($sort, $allowedSort) ? $sort : 'nama';
        $sortOrder   = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

        return $this->db->fetchAll(
            "SELECT * FROM {$this->table}
              WHERE {$where}
           ORDER BY {$sortColumn} {$sortOrder}",
            $bindings,
        );
    }

    /**
     * Aggregate statistics grouped by graduation year.
     */
    public function statistik(?int $tahun = null): array
    {
        $where = $tahun ? "WHERE tahun_lulus = :tahun" : "";
        $bindings = $tahun ? ['tahun' => $tahun] : [];

        return $this->db->fetchAll(
            "SELECT tahun_lulus,
                    COUNT(*) AS total,
                    SUM(status_kelulusan = 'lulus') AS lulus,
                    SUM(status_kelulusan = 'tidak_lulus') AS tidak_lulus,
                    ROUND(AVG(nilai_rata_rata), 2) AS rata_nilai
               FROM {$this->table}
               {$where}
           GROUP BY tahun_lulus
           ORDER BY tahun_lulus DESC",
            $bindings
        );
    }

    /**
     * Get available graduation years (for filter dropdowns).
     */
    public function availableYears(): array
    {
        $rows = $this->db->fetchAll(
            "SELECT DISTINCT tahun_lulus FROM {$this->table} ORDER BY tahun_lulus DESC"
        );
        return array_column($rows, 'tahun_lulus');
    }

    /**
     * Bulk update student graduation status.
     *
     * @param int[] $ids
     */
    public function bulkUpdateStatus(array $ids, string $status): int
    {
        if (empty($ids)) {
            return 0;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE {$this->table} SET status_kelulusan = ? WHERE id IN ($placeholders)";
        
        return $this->db->query($sql, array_merge([$status], $ids))->rowCount();
    }

    /**
     * Bulk delete students.
     *
     * @param int[] $ids
     */
    public function bulkDelete(array $ids): int
    {
        if (empty($ids)) {
            return 0;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "DELETE FROM {$this->table} WHERE id IN ($placeholders)";
        
        return $this->db->query($sql, $ids)->rowCount();
    }
}
