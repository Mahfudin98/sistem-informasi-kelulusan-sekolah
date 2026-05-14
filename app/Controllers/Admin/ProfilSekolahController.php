<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Services\ProfilSekolahService;

final class ProfilSekolahController extends BaseController
{
    public function __construct(
        private readonly ProfilSekolahService $service = new ProfilSekolahService(),
    ) {}

    /**
     * GET /admin/profil
     */
    public function edit(Request $request): void
    {
        $profil = $this->service->getProfile();

        $this->view('admin.profil.edit', [
            'title'  => 'Profil Sekolah — ' . env('APP_NAME'),
            'profil' => $profil,
        ], 'layouts/admin');
    }

    /**
     * POST /admin/profil
     */
    public function update(Request $request): void
    {
        $logoFile = $request->file('logo');
        $result   = $this->service->updateProfile($request->all(), $logoFile);

        if (!$result['success']) {
            $this->withErrors($result['errors'])
                 ->withInput($request->all())
                 ->redirect('/admin/profil');
        }

        $this->withSuccess('Profil sekolah berhasil diperbarui.')
             ->redirect('/admin/profil');
    }
}
