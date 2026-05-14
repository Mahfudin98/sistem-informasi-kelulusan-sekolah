<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ProfilSekolahRepository;
use App\Validation\Validator;

final class ProfilSekolahService
{
    public function __construct(
        private readonly ProfilSekolahRepository $repo = new ProfilSekolahRepository(),
    ) {}

    public function getProfile(): array
    {
        return $this->repo->getProfile();
    }

    /**
     * @return array{success: bool, errors: array}
     */
    public function updateProfile(array $data, ?array $logoFile = null): array
    {
        $validator = Validator::make($data, [
            'nama_sekolah'       => 'required|min:3|max:150',
            'kepala_sekolah'     => 'max:100',
            'nip_kepala_sekolah' => 'max:50',
            'website'            => 'max:100',
            'email'              => 'max:100',
            'telepon'            => 'max:50',
            'warna_dasar'        => 'max:10',
            'tgl_pengumuman'     => '',
            'template_header'    => '',
            'template_surat'     => '',
            'template_footer'    => '',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()];
        }

        $updateData = [
            'nama_sekolah'       => trim($data['nama_sekolah'] ?? ''),
            'alamat'             => trim($data['alamat'] ?? ''),
            'kepala_sekolah'     => trim($data['kepala_sekolah'] ?? ''),
            'nip_kepala_sekolah' => trim($data['nip_kepala_sekolah'] ?? ''),
            'website'            => trim($data['website'] ?? ''),
            'email'              => trim($data['email'] ?? ''),
            'telepon'            => trim($data['telepon'] ?? ''),
            'tgl_pengumuman'     => !empty($data['tgl_pengumuman']) ? date('Y-m-d H:i:s', strtotime($data['tgl_pengumuman'])) : null,
            'template_header'    => htmlspecialchars_decode(trim($data['template_header'] ?? ''), ENT_QUOTES),
            'template_surat'     => htmlspecialchars_decode(trim($data['template_surat'] ?? ''), ENT_QUOTES),
            'template_footer'    => htmlspecialchars_decode(trim($data['template_footer'] ?? ''), ENT_QUOTES),
            'warna_dasar'        => trim($data['warna_dasar'] ?? '#6366f1'),
        ];

        // Handle file upload
        if ($logoFile && $logoFile['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($logoFile['name'], PATHINFO_EXTENSION));
            
            // Validate extension
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'svg', 'webp'])) {
                return ['success' => false, 'errors' => ['logo' => ['Format logo harus berupa gambar (jpg, png, svg, webp).']]];
            }

            // Create upload dir if not exists
            $uploadDir = PUBLIC_PATH . '/uploads/logo';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $filename = 'logo_' . time() . '.' . $ext;
            $destination = $uploadDir . '/' . $filename;

            if (move_uploaded_file($logoFile['tmp_name'], $destination)) {
                $updateData['logo'] = 'uploads/logo/' . $filename;

                // Delete old logo if exists
                $oldProfile = $this->getProfile();
                if (!empty($oldProfile['logo'])) {
                    $oldPath = PUBLIC_PATH . '/' . $oldProfile['logo'];
                    if (file_exists($oldPath) && is_file($oldPath)) {
                        unlink($oldPath);
                    }
                }
            }
        }

        $this->repo->updateProfile($updateData);

        return ['success' => true, 'errors' => []];
    }
}
