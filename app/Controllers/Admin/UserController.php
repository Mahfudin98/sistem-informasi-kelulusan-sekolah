<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Services\UserService;

/**
 * User Controller — Manage admin accounts.
 */
final class UserController extends BaseController
{
    public function __construct(
        private readonly UserService $service = new UserService(),
    ) {}

    public function index(Request $request): void
    {
        $currentRole = auth()->user()['role'] ?? 'admin';
        $this->view('admin.user.index', [
            'title' => 'Manajemen Admin — ' . env('APP_NAME'),
            'users' => $this->service->getAll($currentRole),
        ], 'layouts/admin');
    }

    public function create(Request $request): void
    {
        $this->view('admin.user.create', [
            'title' => 'Tambah Admin — ' . env('APP_NAME'),
            'currentRole' => auth()->user()['role'] ?? 'admin',
        ], 'layouts/admin');
    }

    public function store(Request $request): void
    {
        $data = $request->all();
        $currentRole = auth()->user()['role'] ?? 'admin';

        // Force role to 'admin' if creator is not superadmin
        if ($currentRole !== 'superadmin') {
            $data['role'] = 'admin';
        }

        $result = $this->service->create($data);

        if (!$result['success']) {
            $this->withErrors($result['errors'])
                 ->withInput($request->all())
                 ->redirect('/admin/users/create');
        }

        $this->withSuccess('Admin berhasil ditambahkan.')
             ->redirect('/admin/users');
    }

    public function edit(Request $request, array $params): void
    {
        $user = $this->service->findById((int) $params['id']);
        if (!$user) {
            $this->abort(404);
        }

        $currentRole = auth()->user()['role'] ?? 'admin';
        if ($currentRole !== 'superadmin' && $user['role'] === 'superadmin') {
            $this->withError('Anda tidak memiliki izin untuk mengedit Superadmin.')
                 ->redirect('/admin/users');
        }

        $this->view('admin.user.edit', [
            'title' => 'Edit Admin — ' . env('APP_NAME'),
            'user'  => $user,
            'currentRole' => $currentRole,
        ], 'layouts/admin');
    }

    public function update(Request $request, array $params): void
    {
        $id = (int) $params['id'];
        $user = $this->service->findById($id);
        if (!$user) $this->abort(404);

        $currentRole = auth()->user()['role'] ?? 'admin';
        if ($currentRole !== 'superadmin' && $user['role'] === 'superadmin') {
            $this->withError('Izin ditolak.')
                 ->redirect('/admin/users');
        }

        $data = $request->all();
        // Prevent non-superadmin from elevating role or changing superadmin role
        if ($currentRole !== 'superadmin') {
            $data['role'] = 'admin';
        }

        $isSelf = $id === (int) auth()->id();
        
        $result = $this->service->update($id, $data, isProfile: $isSelf);

        if (!$result['success']) {
            $this->withErrors($result['errors'])
                 ->withInput($request->all())
                 ->redirect("/admin/users/{$id}/edit");
        }

        if ($isSelf && $result['sensitive_changed']) {
            (new \App\Services\AuthService())->logout();
            $this->withSuccess('Keamanan akun diperbarui. Silakan login kembali.')
                 ->redirect('/login');
        }

        $this->withSuccess('Admin berhasil diperbarui.')
             ->redirect('/admin/users');
    }

    public function destroy(Request $request, array $params): void
    {
        $id = (int) $params['id'];
        $user = $this->service->findById($id);
        if (!$user) $this->abort(404);

        $currentRole = auth()->user()['role'] ?? 'admin';
        if ($currentRole !== 'superadmin' && $user['role'] === 'superadmin') {
            $this->withError('Izin ditolak.')
                 ->redirect('/admin/users');
        }
        
        // Prevent deleting self
        if ($id === (int) auth()->id()) {
            $this->withError('Anda tidak dapat menghapus akun Anda sendiri.')
                 ->redirect('/admin/users');
        }

        $this->service->delete($id);

        $this->withSuccess('Admin berhasil dihapus.')
             ->redirect('/admin/users');
    }
}
