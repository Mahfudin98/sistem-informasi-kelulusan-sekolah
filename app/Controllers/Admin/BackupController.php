<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Services\BackupService;
use App\Services\AuditService;

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

    public function download(Request $request): void
    {
        $sql = BackupService::export();
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';

        AuditService::log('other', 'database', 0, "Superadmin melakukan backup database.");

        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $sql;
        exit;
    }
}
