<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;
use App\Validation\Validator;

/**
 * User Service
 *
 * Handles admin account management and profile updates.
 */
final class UserService
{
    public function __construct(
        private readonly UserRepository $repo = new UserRepository(),
    ) {}

    /**
     * Get all users, filtered by current user role permissions.
     */
    public function getAll(string $currentRole): array
    {
        $users = $this->repo->all();

        if ($currentRole !== 'superadmin') {
            // Non-superadmins cannot see superadmins
            return array_values(array_filter($users, fn($u) => $u['role'] !== 'superadmin'));
        }

        return $users;
    }

    /**
     * Find user by ID.
     */
    public function findById(int $id): ?array
    {
        return $this->repo->findById($id);
    }

    /**
     * Create a new admin user.
     */
    public function create(array $data): array
    {
        $validator = $this->validateUser($data);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()];
        }

        // Check uniqueness
        if ($this->repo->findByUsername($data['username'])) {
            return ['success' => false, 'errors' => ['username' => ['Username sudah digunakan.']]];
        }
        if ($this->repo->findByEmail($data['email'])) {
            return ['success' => false, 'errors' => ['email' => ['Email sudah digunakan.']]];
        }

        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $sanitized = $this->sanitize($data);
        $id = $this->repo->create($sanitized);

        AuditService::log('create', 'user', (int)$id, "Menambahkan admin baru: {$sanitized['username']} ({$sanitized['role']})");

        return ['success' => true, 'id' => $id];
    }

    /**
     * Update an admin user.
     */
    public function update(int $id, array $data, bool $isProfile = false): array
    {
        $user = $this->repo->findById($id);
        if (!$user) {
            return ['success' => false, 'errors' => ['general' => ['User tidak ditemukan.']]];
        }

        $rules = [
            'name'     => 'required|min:3|max:100',
            'username' => 'required|min:3|max:50',
            'email'    => 'required|email|max:100',
        ];

        if (!empty($data['password'])) {
            $rules['password'] = 'min:6';
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()];
        }

        // Check uniqueness (excluding current user)
        $existingUsername = $this->repo->findByUsername($data['username']);
        if ($existingUsername && (int)$existingUsername['id'] !== $id) {
            return ['success' => false, 'errors' => ['username' => ['Username sudah digunakan.']]];
        }

        $existingEmail = $this->repo->findByEmail($data['email']);
        if ($existingEmail && (int)$existingEmail['id'] !== $id) {
            return ['success' => false, 'errors' => ['email' => ['Email sudah digunakan.']]];
        }

        $updateData = [
            'name'     => trim($data['name']),
            'username' => trim($data['username']),
            'email'    => trim($data['email']),
        ];

        if (!$isProfile && isset($data['role'])) {
            $updateData['role'] = $data['role'];
        }

        if (!empty($data['password'])) {
            $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $this->repo->update($id, $updateData);

        AuditService::log('update', 'user', $id, "Memperbarui data " . ($isProfile ? "profil sendiri" : "admin") . ": {$user['username']}", $user, $updateData);

        // Check if sensitive data changed to trigger logout
        $sensitiveChanged = (
            $user['username'] !== $updateData['username'] ||
            $user['email']    !== $updateData['email'] ||
            !empty($data['password'])
        );

        return [
            'success'           => true,
            'sensitive_changed' => $sensitiveChanged
        ];
    }

    /**
     * Delete a user.
     */
    public function delete(int $id): bool
    {
        $user = $this->repo->findById($id);
        $deleted = $this->repo->delete($id) > 0;
        
        if ($deleted && $user) {
            AuditService::log('delete', 'user', $id, "Menghapus admin: {$user['username']}", $user);
        }

        return $deleted;
    }

    private function validateUser(array $data): Validator
    {
        return Validator::make($data, [
            'name'     => 'required|min:3|max:100',
            'username' => 'required|min:3|max:50',
            'email'    => 'required|email|max:100',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,superadmin',
        ]);
    }

    private function sanitize(array $data): array
    {
        return [
            'name'     => trim($data['name']),
            'username' => trim($data['username']),
            'email'    => trim($data['email']),
            'password' => $data['password'],
            'role'     => $data['role'] ?? 'admin',
        ];
    }
}
