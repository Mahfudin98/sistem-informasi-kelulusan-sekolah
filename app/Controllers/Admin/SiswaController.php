<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Core\Response;
use App\Services\KelulusanService;

/**
 * Admin Siswa Controller — CRUD for student records.
 */
final class SiswaController extends BaseController
{
    public function __construct(
        private readonly KelulusanService $service = new KelulusanService(),
    ) {}

    // ── Index ─────────────────────────────────────────────────────────────────

    /**
     * GET /admin/siswa
     */
    public function index(Request $request): void
    {
        $page    = max(1, (int) $request->query('page', 1));
        $perPage = (int) $request->query('limit', 15);
        $search  = $request->query('search', '');
        $tahun   = $request->query('tahun') ? (int) $request->query('tahun') : null;
        $status  = $request->query('status') ?: null;
        $sort    = $request->query('sort', 'nama');
        $order   = $request->query('order', 'ASC');

        $result = $this->service->getPaginatedList($page, $perPage, $search, $tahun, $status, $sort, $order);

        $this->view('admin.siswa.index', [
            'title'        => 'Data Siswa — ' . env('APP_NAME'),
            'siswa'        => $result['data'],
            'pagination'   => $result,
            'search'       => $search,
            'filterTahun'  => $tahun,
            'filterStatus' => $status,
            'limit'        => $perPage,
            'sort'         => $sort,
            'order'        => $order,
            'years'        => $this->service->availableYears(),
        ], 'layouts/admin');
    }

    // ── Create ────────────────────────────────────────────────────────────────

    /**
     * GET /admin/siswa/create
     */
    public function create(Request $request): void
    {
        $this->view('admin.siswa.create', [
            'title' => 'Tambah Siswa — ' . env('APP_NAME'),
        ], 'layouts/admin');
    }

    /**
     * POST /admin/siswa
     */
    public function store(Request $request): void
    {
        $result = $this->service->create($request->all());

        if (!$result['success']) {
            $this->withErrors($result['errors'])
                 ->withInput($request->all())
                 ->redirect('/admin/siswa/create');
        }

        $this->withSuccess('Data siswa berhasil ditambahkan.')
             ->redirect('/admin/siswa');
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    /**
     * GET /admin/siswa/:id/edit
     */
    public function edit(Request $request, array $params): void
    {
        $siswa = $this->findOrAbort((int) $params['id']);

        $this->view('admin.siswa.edit', [
            'title' => 'Edit Siswa — ' . env('APP_NAME'),
            'siswa' => $siswa,
        ], 'layouts/admin');
    }

    /**
     * POST /admin/siswa/:id/update
     */
    public function update(Request $request, array $params): void
    {
        $id     = (int) $params['id'];
        $result = $this->service->update($id, $request->all());

        if (!$result['success']) {
            $this->withErrors($result['errors'])
                 ->withInput($request->all())
                 ->redirect("/admin/siswa/{$id}/edit");
        }

        $this->withSuccess('Data siswa berhasil diperbarui.')
             ->redirect('/admin/siswa');
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    /**
     * POST /admin/siswa/:id/delete
     */
    public function destroy(Request $request, array $params): void
    {
        $id = (int) $params['id'];
        $this->findOrAbort($id);

        $this->service->delete($id);

        if ($request->isAjax()) {
            $this->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
        }

        $this->withSuccess('Data siswa berhasil dihapus.')
             ->redirect('/admin/siswa');
    }

    public function bulkUpdate(Request $request): void
    {
        $ids    = (array) ($request->body['ids'] ?? []);
        $status = (string) ($request->body['status'] ?? '');

        $result = $this->service->bulkUpdateStatus($ids, $status);

        if (!$result['success']) {
            $this->withError($result['message'])->redirect('/admin/siswa');
            return;
        }

        $this->withSuccess($result['message'])->redirect('/admin/siswa');
    }

    /**
     * POST /admin/siswa/bulk-delete
     */
    public function bulkDelete(Request $request): void
    {
        $ids = (array) ($request->body['ids'] ?? []);

        $result = $this->service->bulkDelete($ids);

        if (!$result['success']) {
            $this->withError($result['message'])->redirect('/admin/siswa');
            return;
        }

        $this->withSuccess($result['message'])->redirect('/admin/siswa');
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    /**
     * GET /admin/siswa/:id
     */
    public function show(Request $request, array $params): void
    {
        $siswa = $this->findOrAbort((int) $params['id']);

        $this->view('admin.siswa.show', [
            'title' => 'Detail Siswa — ' . env('APP_NAME'),
            'siswa' => $siswa,
        ], 'layouts/admin');
    }

    // ── Import Bulk Excel ─────────────────────────────────────────────────────

    /**
     * GET /admin/siswa/import
     */
    public function import(Request $request): void
    {
        $this->view('admin.siswa.import', [
            'title' => 'Import Data Siswa — ' . env('APP_NAME'),
        ], 'layouts/admin');
    }

    /**
     * POST /admin/siswa/import
     */
    public function processImport(Request $request): void
    {
        $file = $request->file('excel_file');
        
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $this->withError('Pilih file Excel yang valid untuk diunggah.')->redirect('/admin/siswa/import');
        }
        
        // Simple validation for extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['xls', 'xlsx'])) {
            $this->withError('Format file tidak didukung. Harap unggah file .xls atau .xlsx.')->redirect('/admin/siswa/import');
        }

        $result = $this->service->importExcel($file['tmp_name']);

        if (!$result['success']) {
            $this->withError($result['message'])->redirect('/admin/siswa/import');
        }

        $this->withSuccess("Berhasil mengimpor {$result['count']} data siswa baru.")
             ->redirect('/admin/siswa');
    }

    /**
     * GET /admin/siswa/template
     */
    public function downloadTemplate(Request $request): void
    {
        $this->service->downloadExcelTemplate();
        exit;
    }

    /**
     * GET /admin/siswa/export
     */
    public function export(Request $request): void
    {
        $search  = $request->query('search', '');
        $tahun   = $request->query('tahun') ? (int) $request->query('tahun') : null;
        $status  = $request->query('status') ?: null;
        $sort    = $request->query('sort', 'nama');
        $order   = $request->query('order', 'ASC');

        $this->service->exportExcel($search, $tahun, $status, $sort, $order);
        exit;
    }

    // ── Internals ─────────────────────────────────────────────────────────────

    private function findOrAbort(int $id): array
    {
        $repo  = new \App\Repositories\SiswaRepository();
        $siswa = $repo->findById($id);

        if ($siswa === null) {
            $this->abort(404, 'Data siswa tidak ditemukan.');
        }

        return $siswa;
    }
}
