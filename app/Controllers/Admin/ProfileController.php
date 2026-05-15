<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Services\UserService;
use App\Services\AuthService;

/**
 * Profile Controller — Edit current logged-in user profile.
 */
final class ProfileController extends BaseController
{
    public function __construct(
        private readonly UserService $userService = new UserService(),
        private readonly AuthService $authService = new AuthService(),
    ) {}

    public function edit(Request $request): void
    {
        $user = $this->authService->user();

        $this->view('admin.profile.edit', [
            'title' => 'Edit Profil Saya — ' . env('APP_NAME'),
            'user'  => $user,
        ], 'layouts/admin');
    }

    public function update(Request $request): void
    {
        $id = (int) $this->authService->id();
        $result = $this->userService->update($id, $request->all(), isProfile: true);

        if (!$result['success']) {
            $this->withErrors($result['errors'])
                 ->withInput($request->all())
                 ->redirect('/admin/profile');
        }

        if ($result['sensitive_changed']) {
            $this->authService->logout();
            $this->withSuccess('Profil/Keamanan diperbarui. Silakan login kembali dengan data baru.')
                 ->redirect('/login');
        }

        $this->authService->refreshSession();

        $this->withSuccess('Profil berhasil diperbarui.')
             ->redirect('/admin/profile');
    }
}
