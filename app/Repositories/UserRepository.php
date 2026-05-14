<?php

declare(strict_types=1);

namespace App\Repositories;

/**
 * User Repository — for admin authentication.
 */
final class UserRepository extends BaseRepository
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1",
            ['email' => $email],
        );
    }

    public function findByUsername(string $username): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1",
            ['username' => $username],
        );
    }

    public function updateLastLogin(int $id): void
    {
        $this->db->update(
            $this->table,
            ['last_login_at' => date('Y-m-d H:i:s')],
            'id = :id',
            ['id' => $id],
        );
    }
}
