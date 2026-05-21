<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;
use App\Core\Session;

/**
 * Auth Service
 *
 * Handles login, logout and session management for admin users.
 */
final class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepo = new UserRepository(),
    ) {}

    /**
     * Attempt to log in with username/email + password.
     *
     * @return array{success: bool, message: string}
     */
    public function attempt(string $identifier, string $password): array
    {
        // Try email first, then username
        $user = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? $this->userRepo->findByEmail($identifier)
            : $this->userRepo->findByUsername($identifier);

        if ($user === null) {
            return ['success' => false, 'message' => 'Akun tidak ditemukan.'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Password salah.'];
        }

        if (isset($user['is_active']) && !(bool) $user['is_active']) {
            return ['success' => false, 'message' => 'Akun Anda telah dinonaktifkan.'];
        }

        // Regenerate session ID to prevent session fixation
        Session::regenerate();

        Session::set('user', [
            'id'       => $user['id'],
            'name'     => $user['name'],
            'username' => $user['username'],
            'email'    => $user['email'],
            'role'     => $user['role'] ?? 'admin',
        ]);

        $this->userRepo->updateLastLogin((int) $user['id']);

        // Log Audit
        AuditService::log('login', 'user', (int) $user['id'], "User logged in: {$user['username']}");

        return ['success' => true, 'message' => 'Login berhasil.'];
    }

    /**
     * Log out the current user.
     */
    public function logout(): void
    {
        $user = Session::get('user');
        if ($user) {
            AuditService::log('logout', 'user', (int) $user['id'], "User logged out: {$user['username']}");
        }
        Session::destroy();
    }

    /**
     * Return the current authenticated user, or null.
     */
    public function user(): ?array
    {
        return Session::get('user');
    }

    /**
     * Check whether any user is authenticated.
     */
    public function check(): bool
    {
        return Session::has('user');
    }

    /**
     * Check whether the user is a guest (not authenticated).
     */
    public function guest(): bool
    {
        return !$this->check();
    }

    /**
     * Get current user ID.
     */
    public function id(): ?int
    {
        return Session::get('user')['id'] ?? null;
    }

    /**
     * Refresh the session data from DB.
     */
    public function refreshSession(): void
    {
        $id = $this->id();
        if (!$id) return;

        $user = $this->userRepo->findById($id);
        if ($user) {
            Session::set('user', [
                'id'       => $user['id'],
                'name'     => $user['name'],
                'username' => $user['username'],
                'email'    => $user['email'],
                'role'     => $user['role'] ?? 'admin',
            ]);
        }
    }
}
