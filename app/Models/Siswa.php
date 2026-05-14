<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Siswa Model
 *
 * Represents a student record.
 */
final class Siswa extends BaseModel
{
    protected string $table   = 'siswa';
    protected string $primary = 'id';

    protected array $fillable = [
        'nisn',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'jurusan',
        'tahun_lulus',
        'status_kelulusan',   // 'lulus' | 'tidak_lulus'
        'nilai_rata_rata',
        'keterangan',
    ];

    // ── Custom Queries ────────────────────────────────────────────────────────

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
     * Fetch students filtered by year and optional status.
     */
    public function getByTahun(int $tahun, ?string $status = null): array
    {
        if ($status !== null) {
            return $this->db->fetchAll(
                "SELECT * FROM {$this->table}
                  WHERE tahun_lulus = :tahun AND status_kelulusan = :status
                  ORDER BY nama ASC",
                ['tahun' => $tahun, 'status' => $status],
            );
        }

        return $this->db->fetchAll(
            "SELECT * FROM {$this->table}
              WHERE tahun_lulus = :tahun
              ORDER BY nama ASC",
            ['tahun' => $tahun],
        );
    }

    /**
     * Count graduates per year.
     */
    public function statistikPerTahun(): array
    {
        return $this->db->fetchAll(
            "SELECT tahun_lulus,
                    COUNT(*) AS total,
                    SUM(status_kelulusan = 'lulus') AS lulus,
                    SUM(status_kelulusan = 'tidak_lulus') AS tidak_lulus
               FROM {$this->table}
           GROUP BY tahun_lulus
           ORDER BY tahun_lulus DESC"
        );
    }
}
