<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Mock some constants if needed
if (!defined('ROOT_PATH')) define('ROOT_PATH', dirname(__DIR__));

// Load .env
$dotenv = \Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->safeLoad();

use App\Core\Database;

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();

    $header = '<table style="width: 100%; border-bottom: 4px solid #000; padding-bottom: 10px; margin-bottom: 20px; border-collapse: collapse;"><tr><td style="width: 80px; text-align: left; vertical-align: middle;"><img src="[logo_sekolah]" alt="Logo" style="width: 80px; height: auto;"></td><td style="text-align: center; vertical-align: middle; padding: 0 10px;"><p style="font-size: 14pt; font-weight: bold; margin: 0; text-transform: uppercase;">DINAS PENDIDIKAN</p><h1 style="font-size: 18pt; font-weight: bold; margin: 2px 0; text-transform: uppercase;">[nama_sekolah]</h1><p style="font-size: 10pt; margin: 0;">[alamat]<br>Website: [website] | Email: [email] | Telp: [telepon]</p></td><td style="width: 80px;"></td></tr></table>';
    $footer = '<table style="width: 100%; margin-top: 30px; border-collapse: collapse;"><tr><td style="width: 60%;"></td><td style="width: 40%; text-align: left; padding-left: 30px;"><div style="margin-bottom: 5px;">Ditetapkan di: ____________<br>Pada tanggal: [tanggal_surat]</div><div style="margin-bottom: 60px;">Kepala Sekolah,</div><div style="font-weight: bold; text-decoration: underline;">[kepala_sekolah]</div><div style="margin-top: 5px;">NIP. [nip_kepala_sekolah]</div></td></tr></table>';

    $stmt = $pdo->prepare('UPDATE profil_sekolah SET template_header = ?, template_footer = ? WHERE id = 1');
    $stmt->execute([$header, $footer]);

    echo "Templates updated successfully.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
