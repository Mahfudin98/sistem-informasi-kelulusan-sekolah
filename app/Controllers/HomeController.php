<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Services\KelulusanService;

/**
 * HomeController
 *
 * Handles the public-facing graduation lookup page.
 */
final class HomeController extends BaseController
{
    public function __construct(
        private readonly KelulusanService $service = new KelulusanService(),
    ) {}

    /**
     * GET / — Landing + lookup form.
     */
    public function index(Request $request): void
    {
        $profil = profil_sekolah();
        $this->view('home.index', [
            'title'    => 'Cek Kelulusan — ' . ($profil['nama_sekolah'] ?? env('APP_NAME')),
            'result'   => null,
            'profil'   => $profil,
        ]);
    }

    /**
     * POST /cek — Process NISN lookup.
     */
    public function cek(Request $request): void
    {
        $profil = profil_sekolah();
        if (!empty($profil['tgl_pengumuman']) && strtotime($profil['tgl_pengumuman']) > time()) {
            redirect('/');
        }

        $nisn   = $request->input('nisn', '');
        $result = $this->service->cekKelulusan($nisn);

        $this->view('home.index', [
            'title'  => 'Hasil Cek Kelulusan — ' . env('APP_NAME'),
            'nisn'   => $nisn,
            'result' => $result,
            'profil' => $profil,
        ]);
    }

    /**
     * GET /cetak/:nisn — Cetak Surat Kelulusan
     */
    public function cetak(Request $request, array $params): void
    {
        $nisn = $params['nisn'];
        $profil = profil_sekolah();
        if (!empty($profil['tgl_pengumuman']) && strtotime($profil['tgl_pengumuman']) > time()) {
            abort(403, 'Pengumuman belum dibuka.');
        }

        $result = $this->service->cekKelulusan($nisn);
        if (!$result['found'] || $result['siswa']['status_kelulusan'] !== 'lulus') {
            abort(404, 'Data tidak ditemukan atau siswa tidak lulus.');
        }

        $this->view('home.cetak', [
            'title'  => 'Cetak SKL — ' . $result['siswa']['nama'],
            'siswa'  => $result['siswa'],
            'profil' => $profil,
        ], null);
    }
}
