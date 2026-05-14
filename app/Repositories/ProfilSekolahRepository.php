<?php

declare(strict_types=1);

namespace App\Repositories;

final class ProfilSekolahRepository extends BaseRepository
{
    protected string $table = 'profil_sekolah';

    public function getProfile(): array
    {
        return $this->db->fetchOne("SELECT * FROM {$this->table} WHERE id = 1 LIMIT 1") ?: [];
    }

    public function updateProfile(array $data): int
    {
        return $this->db->update($this->table, $data, 'id = 1');
    }
}
