<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Core\Mail;
use App\Repositories\UserRepository;
use App\Core\Database;

/**
 * Forgot Password Controller
 */
final class ForgotPasswordController extends BaseController
{
    private readonly UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    public function showLinkRequestForm(): void
    {
        $this->view('auth.forgot-password', [
            'title' => 'Lupa Kata Sandi — ' . env('APP_NAME')
        ], 'layouts/auth');
    }

    public function sendResetLinkEmail(Request $request): void
    {
        $email = $request->input('email');
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->withError('Format email tidak valid.')->redirect('/forgot-password');
        }

        $user = $this->userRepo->findByEmail($email);

        if (!$user) {
            // For security, don't reveal if email exists, but in internal apps usually we do.
            $this->withError('Email tidak ditemukan dalam sistem.')->redirect('/forgot-password');
        }

        // Generate token
        $token = bin2hex(random_bytes(32));
        
        // Save to database
        $db = Database::getInstance();
        $db->insert('password_resets', [
            'email' => $email,
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Send Email
        $resetUrl = url("/reset-password/{$token}");
        $subject = "Reset Kata Sandi - " . env('APP_NAME');
        $body = "
            <h2>Halo, {$user['name']}</h2>
            <p>Anda menerima email ini karena kami menerima permintaan reset kata sandi untuk akun Anda.</p>
            <p>Klik tombol di bawah ini untuk mengatur ulang kata sandi Anda:</p>
            <p><a href='{$resetUrl}' style='background: #6366f1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Reset Kata Sandi</a></p>
            <p>Link ini akan kadaluarsa dalam 60 menit.</p>
            <p>Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.</p>
            <br>
            <p>Salam,<br>" . env('APP_NAME') . "</p>
        ";

        if (Mail::send($email, $subject, $body)) {
            $this->withSuccess('Link reset kata sandi telah dikirim ke email Anda.')->redirect('/forgot-password');
        } else {
            $this->withError('Gagal mengirim email. Pastikan konfigurasi SMTP benar.')->redirect('/forgot-password');
        }
    }
}
