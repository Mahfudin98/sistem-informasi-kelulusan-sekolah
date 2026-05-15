<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\SiswaRepository;
use App\Services\AuditService;
use App\Validation\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Kelulusan Service
 *
 * Business logic for checking graduation status and managing student data.
 * Controllers stay thin by delegating all business rules here.
 */
final class KelulusanService
{
    public function __construct(
        private readonly SiswaRepository $repo = new SiswaRepository(),
    ) {}

    // ── Public Lookup ─────────────────────────────────────────────────────────

    /**
     * Look up a student's graduation status by NISN.
     *
     * @return array{found: bool, siswa: array|null, message: string}
     */
    public function cekKelulusan(string $nisn): array
    {
        $nisn = trim($nisn);

        if ($nisn === '') {
            return ['found' => false, 'siswa' => null, 'message' => 'NISN tidak boleh kosong.'];
        }

        $siswa = $this->repo->findByNisn($nisn);

        if ($siswa === null) {
            return [
                'found'   => false,
                'siswa'   => null,
                'message' => "Data dengan NISN <strong>{$nisn}</strong> tidak ditemukan.",
            ];
        }

        $lulus   = $siswa['status_kelulusan'] === 'lulus';
        $message = $lulus
            ? "Selamat! <strong>{$siswa['nama']}</strong> dinyatakan <strong class=\"text-success\">LULUS</strong>."
            : "<strong>{$siswa['nama']}</strong> dinyatakan <strong class=\"text-danger\">TIDAK LULUS</strong>.";

        return ['found' => true, 'siswa' => $siswa, 'message' => $message];
    }

    // ── Admin CRUD ────────────────────────────────────────────────────────────

    /**
     * Get a paginated, filtered list of students.
     */
    public function getPaginatedList(
        int    $page,
        int    $perPage = 15,
        string $search  = '',
        ?int   $tahun   = null,
        ?string $status = null,
        string $sort    = 'nama',
        string $order   = 'ASC',
    ): array {
        return $this->repo->paginate($page, $perPage, $search, $tahun, $status, $sort, $order);
    }

    /**
     * Validate and create a new student record.
     *
     * @return array{success: bool, errors: array, id: string|false}
     */
    public function create(array $data): array
    {
        $validator = $this->validateSiswa($data);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors(), 'id' => false];
        }

        // Check NISN uniqueness
        if ($this->repo->findByNisn($data['nisn'])) {
            return [
                'success' => false,
                'errors'  => ['nisn' => ['NISN sudah terdaftar dalam sistem.']],
                'id'      => false,
            ];
        }

        $sanitized = $this->sanitize($data);
        $id = $this->repo->create($sanitized);

        AuditService::log('create', 'siswa', (int)$id, "Menambahkan siswa baru: {$sanitized['nama']} ({$sanitized['nisn']})", null, $sanitized);

        return ['success' => true, 'errors' => [], 'id' => $id];
    }

    /**
     * Validate and update an existing student record.
     *
     * @return array{success: bool, errors: array}
     */
    public function update(int $id, array $data): array
    {
        $validator = $this->validateSiswa($data, $id);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()];
        }

        $old = $this->repo->findById($id);
        $sanitized = $this->sanitize($data);
        $this->repo->update($id, $sanitized);

        AuditService::log('update', 'siswa', $id, "Memperbarui data siswa: {$sanitized['nama']}", $old, $sanitized);

        return ['success' => true, 'errors' => []];
    }

    /**
     * Delete a student by ID.
     */
    public function delete(int $id): bool
    {
        $old = $this->repo->findById($id);
        $deleted = $this->repo->delete($id) > 0;
        
        if ($deleted && $old) {
            AuditService::log('delete', 'siswa', $id, "Menghapus siswa: {$old['nama']} ({$old['nisn']})", $old);
        }

        return $deleted;
    }

    /**
     * Get aggregate statistics.
     */
    public function statistik(?int $tahun = null): array
    {
        return $this->repo->statistik($tahun);
    }

    public function availableYears(): array
    {
        return $this->repo->availableYears();
    }

    /**
     * Bulk update status for multiple students.
     */
    public function bulkUpdateStatus(array $ids, string $status): array
    {
        if (empty($ids)) {
            return ['success' => false, 'message' => 'Pilih data siswa terlebih dahulu.'];
        }

        if (!in_array($status, ['lulus', 'tidak_lulus'])) {
            return ['success' => false, 'message' => 'Status tidak valid.'];
        }

        $count = $this->repo->bulkUpdateStatus($ids, $status);
        
        AuditService::log('bulk_update', 'siswa', 0, "Update status massal untuk " . count($ids) . " siswa");

        return ['success' => true, 'message' => "Berhasil memperbarui {$count} data siswa."];
    }

    public function bulkDelete(array $ids): array
    {
        if (empty($ids)) {
            return ['success' => false, 'message' => 'Pilih data siswa terlebih dahulu.'];
        }

        $count = $this->repo->bulkDelete($ids);
        
        AuditService::log('bulk_delete', 'siswa', 0, "Hapus massal " . count($ids) . " siswa");

        return ['success' => true, 'message' => "Berhasil menghapus {$count} data siswa."];
    }

    // ── Bulk Excel ────────────────────────────────────────────────────────────

    public function importExcel(string $filePath): array
    {
        // Prevent timeout for large files
        set_time_limit(0);

        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet   = $spreadsheet->getActiveSheet();
            $rows        = $worksheet->toArray(null, true, true, false);
            
            // Remove header row
            array_shift($rows);

            $count = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                // Skip empty rows (checking NISN)
                if (empty($row[0])) {
                    continue;
                }

                // Handle NISN
                $nisn = trim((string) $row[0]);
                if (is_numeric($nisn)) {
                    $nisn = number_format((float)$nisn, 0, '', '');
                }
                $nisn = str_pad($nisn, 10, '0', STR_PAD_LEFT);

                // Handle Date
                $tgl = trim((string) $row[3]);
                if (is_numeric($tgl)) {
                    // Excel serialized date
                    $tglObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tgl);
                    $tgl = $tglObj->format('Y-m-d');
                } else {
                    $time = strtotime($tgl);
                    $tgl = $time ? date('Y-m-d', $time) : $tgl;
                }

                // Handle Nilai
                $nilai = trim((string) $row[8]);
                if (str_contains($nilai, ',')) {
                    $nilai = str_replace(',', '.', $nilai);
                }

                $data = [
                    'nisn'             => $nisn,
                    'nama'             => trim((string) $row[1]),
                    'tempat_lahir'     => trim((string) $row[2]),
                    'tanggal_lahir'    => $tgl,
                    'jenis_kelamin'    => strtoupper(trim((string) $row[4])),
                    'jurusan'          => trim((string) $row[5]),
                    'tahun_lulus'      => (int) $row[6],
                    'status_kelulusan' => strtolower(trim((string) $row[7])),
                    'nilai_rata_rata'  => (float) $nilai,
                    'keterangan'       => trim((string) ($row[9] ?? '')),
                ];

                $validator = $this->validateSiswa($data);

                if ($validator->fails()) {
                    $errors[] = "Baris " . ($index + 2) . " gagal divalidasi (" . implode(', ', array_map(fn($e) => implode(', ', $e), $validator->errors())) . ")";
                    continue;
                }

                // Check if NISN exists to decide whether to update or insert
                $existing = $this->repo->findByNisn($data['nisn']);
                
                if ($existing) {
                    $this->repo->update((int) $existing['id'], $this->sanitize($data));
                } else {
                    $this->repo->create($this->sanitize($data));
                }

                $count++;
            }

            if ($count === 0 && count($errors) > 0) {
                return ['success' => false, 'count' => 0, 'message' => 'Gagal mengimpor data. Error pertama: ' . $errors[0]];
            }

            $msg = "Berhasil mengimpor {$count} data.";
            if (count($errors) > 0) {
                $msg .= " Namun, " . count($errors) . " data gagal diimpor (contoh: " . $errors[0] . ").";
            }

            return ['success' => true, 'count' => $count, 'message' => $msg];

        } catch (\Exception $e) {
            return ['success' => false, 'count' => 0, 'message' => 'Terjadi kesalahan saat memproses file Excel: ' . $e->getMessage()];
        }
    }

    public function downloadExcelTemplate(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

        // Headers
        $headers = ['NISN', 'Nama Lengkap', 'Tempat Lahir', 'Tanggal Lahir (YYYY-MM-DD)', 'L/P', 'Jurusan', 'Tahun Lulus', 'lulus/tidak_lulus', 'Nilai Rata-rata', 'Keterangan'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $h);
        }

        // Add one example row
        $sheet->setCellValue('A2', '1234567890');
        $sheet->setCellValue('B2', 'Siswa Contoh');
        $sheet->setCellValue('C2', 'Jakarta');
        $sheet->setCellValue('D2', '2005-01-01');
        $sheet->setCellValue('E2', 'L');
        $sheet->setCellValue('F2', 'IPA');
        $sheet->setCellValue('G2', date('Y'));
        $sheet->setCellValue('H2', 'lulus');
        $sheet->setCellValue('I2', '85.50');
        $sheet->setCellValue('J2', 'Contoh keterangan');

        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="template_siswa.xlsx"');
        $writer->save('php://output');
    }

    public function exportExcel(string $search = '', ?int $tahun = null, ?string $status = null, string $sort = 'nama', string $order = 'ASC'): void
    {
        $data = $this->repo->getFilteredAll($search, $tahun, $status, $sort, $order);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

        // Headers
        $headers = ['NISN', 'Nama Lengkap', 'Tempat Lahir', 'Tanggal Lahir', 'L/P', 'Jurusan', 'Tahun Lulus', 'Status', 'Nilai', 'Keterangan'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $h);
            $sheet->getStyleByColumnAndRow($i + 1, 1)->getFont()->setBold(true);
        }

        $rowIdx = 2;
        foreach ($data as $row) {
            $sheet->setCellValue('A' . $rowIdx, $row['nisn']);
            $sheet->setCellValue('B' . $rowIdx, $row['nama']);
            $sheet->setCellValue('C' . $rowIdx, $row['tempat_lahir']);
            $sheet->setCellValue('D' . $rowIdx, $row['tanggal_lahir']);
            $sheet->setCellValue('E' . $rowIdx, $row['jenis_kelamin']);
            $sheet->setCellValue('F' . $rowIdx, $row['jurusan']);
            $sheet->setCellValue('G' . $rowIdx, $row['tahun_lulus']);
            $sheet->setCellValue('H' . $rowIdx, ucfirst($row['status_kelulusan']));
            $sheet->setCellValue('I' . $rowIdx, $row['nilai_rata_rata']);
            $sheet->setCellValue('J' . $rowIdx, $row['keterangan']);
            $rowIdx++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        
        $filename = 'Data_Siswa_' . date('Y-m-d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer->save('php://output');
    }

    // ── Internals ─────────────────────────────────────────────────────────────

    private function validateSiswa(array $data, ?int $id = null): Validator
    {
        return Validator::make($data, [
            'nisn'             => 'required|exact_length:10|numeric' . ($id ? '' : '|unique:siswa,nisn'),
            'nama'             => 'required|min:3|max:150',
            'tempat_lahir'     => 'required|max:100',
            'tanggal_lahir'    => 'required|date',
            'jenis_kelamin'    => 'required|in:L,P',
            'jurusan'          => 'max:100',
            'tahun_lulus'      => 'required|numeric',
            'status_kelulusan' => 'required|in:lulus,tidak_lulus',
            'nilai_rata_rata'  => 'numeric',
            'keterangan'       => '',
        ]);
    }

    private function sanitize(array $data): array
    {
        return [
            'nisn'             => trim($data['nisn']),
            'nama'             => trim($data['nama']),
            'tempat_lahir'     => trim($data['tempat_lahir']),
            'tanggal_lahir'    => $data['tanggal_lahir'],
            'jenis_kelamin'    => $data['jenis_kelamin'],
            'jurusan'          => trim($data['jurusan']),
            'tahun_lulus'      => (int) $data['tahun_lulus'],
            'status_kelulusan' => $data['status_kelulusan'],
            'nilai_rata_rata'  => (float) $data['nilai_rata_rata'],
            'keterangan'       => trim($data['keterangan'] ?? ''),
        ];
    }
}
