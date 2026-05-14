<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Services\KelulusanService;

/**
 * Admin Dashboard Controller
 */
final class DashboardController extends BaseController
{
    public function __construct(
        private readonly KelulusanService $service = new KelulusanService(),
    ) {}

    /**
     * GET /admin/dashboard
     */
    public function index(Request $request): void
    {
        $statistik = $this->service->statistik();

        // Quick summary for top cards
        $totalSiswa  = array_sum(array_column($statistik, 'total'));
        $totalLulus  = array_sum(array_column($statistik, 'lulus'));
        $totalTidak  = array_sum(array_column($statistik, 'tidak_lulus'));
        $persentase  = $totalSiswa > 0
            ? round(($totalLulus / $totalSiswa) * 100, 1)
            : 0;

        $this->view('admin.dashboard', [
            'title'       => 'Dashboard — ' . env('APP_NAME'),
            'statistik'   => $statistik,
            'totalSiswa'  => $totalSiswa,
            'totalLulus'  => $totalLulus,
            'totalTidak'  => $totalTidak,
            'persentase'  => $persentase,
        ], 'layouts/admin');
    }
}
