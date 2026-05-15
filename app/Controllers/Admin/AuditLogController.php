<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Services\AuditService;

/**
 * Audit Log Controller — View system activity.
 */
final class AuditLogController extends BaseController
{
    public function __construct(
        private readonly AuditService $service = new AuditService(),
    ) {
        // Enforce Superadmin access
        if (auth()->user()['role'] !== 'superadmin') {
            $this->abort(403, 'Akses ditolak. Hanya Superadmin yang dapat melihat log audit.');
        }
    }

    public function index(Request $request): void
    {
        $page = (int) $request->query('page', 1);
        $result = $this->service->getPaginatedLogs($page);

        $this->view('admin.audit_logs.index', [
            'title'      => 'Log Audit — ' . env('APP_NAME'),
            'logs'       => $result['data'],
            'pagination' => $result,
        ], 'layouts/admin');
    }
}
