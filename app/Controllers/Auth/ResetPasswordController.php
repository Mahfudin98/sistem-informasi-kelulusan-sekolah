<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Repositories\UserRepository;
use App\Core\Database;
use App\Services\AuditService;

/**
 * Reset Password Controller
 */
final class ResetPasswordController extends BaseController
{
    private readonly UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    public function showResetForm(Request $request, array $params): void
    {
        $token = $params['token'];
        
        $db = Database::getInstance();
        $reset = $db->fetchOne("SELECT * FROM password_resets WHERE token = :token LIMIT 1", ['token' => $token]);

        if (!$reset || (strtotime($reset['created_at']) < strtotime('-1 hour'))) {
            $this->withError('Token tidak valid atau sudah kadaluarsa.')->redirect('/forgot-password');
        }

        $this->view('auth.reset-password', [
            'title' => 'Reset Kata Sandi — ' . env('APP_NAME'),
            'token' => $token,
            'email' => $reset['email']
        ], 'layouts/auth');
    }

    public function reset(Request $request): void
    {
        $token    = $request->input('token');
        $email    = $request->input('email');
        $password = $request->input('password');
        
        if (strlen($password) < 6) {
            $this->withError('Password minimal 6 karakter.')->redirect("/reset-password/{$token}");
        }

        $db = Database::getInstance();
        $reset = $db->fetchOne("SELECT * FROM password_resets WHERE token = :token AND email = :email LIMIT 1", [
            'token' => $token,
            'email' => $email
        ]);

        if (!$reset || (strtotime($reset['created_at']) < strtotime('-1 hour'))) {
            $this->withError('Sesi tidak valid atau sudah kadaluarsa.')->redirect('/forgot-password');
        }

        $user = $this->userRepo->findByEmail($email);
        if (!$user) {
            $this->withError('User tidak ditemukan.')->redirect('/forgot-password');
        }

        // Update password
        $this->userRepo->update($user['id'], [
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        // Delete token
        $db->delete('password_resets', 'email = :email', ['email' => $email]);

        // Audit Log
        AuditService::log('update', 'user', (int)$user['id'], "User melakukan reset kata sandi melalui email.");

        $this->withSuccess('Kata sandi berhasil diperbarui. Silakan login.')->redirect('/login');
    }
}
