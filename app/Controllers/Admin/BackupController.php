<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Services\BackupService;
use App\Services\AuditService;
use PDO;

/**
 * Backup Controller — Database management.
 */
final class BackupController extends BaseController
{
    public function __construct()
    {
        // Enforce Superadmin access
        if (auth()->user()['role'] !== 'superadmin') {
            $this->abort(403, 'Akses ditolak. Hanya Superadmin yang dapat melakukan backup.');
        }
    }

    public function index(Request $request): void
    {
        $db = \App\Core\Database::getInstance();
        $pdo = $db->getPdo();
        
        $tables = [];
        $result = $pdo->query("SHOW TABLES");
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $siswaRepo = new \App\Repositories\SiswaRepository();
        $years = $siswaRepo->availableYears();

        $this->view('admin.backup.index', [
            'title'  => 'Backup Database — ' . env('APP_NAME'),
            'tables' => $tables,
            'years'  => $years,
        ], 'layouts/admin');
    }

    public function download(Request $request): void
    {
        $selectedTables = (array) ($request->body['tables'] ?? []);
        $filterYear     = (int) ($request->body['tahun_lulus'] ?? 0);

        $filters = [];
        if ($filterYear > 0) {
            $filters['tahun_lulus'] = $filterYear;
        }

        $sql = BackupService::export($selectedTables, $filters);
        
        $desc = "Superadmin melakukan backup database. " . 
                (!empty($selectedTables) ? "Tabel: " . implode(', ', $selectedTables) : "Semua Tabel") .
                ($filterYear > 0 ? " (Filter Tahun: $filterYear)" : "");

        AuditService::log('other', 'database', 0, $desc);

        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $sql;
        exit;
    }
}
