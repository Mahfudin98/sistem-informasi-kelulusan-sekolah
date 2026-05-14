<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$defaultTemplate = <<<HTML
<p style="text-indent: 30px; text-align: justify;">
    Kepala [nama_sekolah] selaku Ketua Penyelenggara Ujian Sekolah Tahun Pelajaran [tahun_pelajaran] berdasarkan Keputusan Kepala Sekolah dan Rapat Dewan Guru, menerangkan bahwa:
</p>
<table style="margin-left: 30px; width: calc(100% - 30px); margin-bottom: 20px;">
    <tbody>
        <tr>
            <td style="width: 250px; padding: 5px 0;">Nama Lengkap</td>
            <td style="width: 20px; padding: 5px 0;">:</td>
            <td style="padding: 5px 0;"><strong>[nama_siswa]</strong></td>
        </tr>
        <tr>
            <td style="padding: 5px 0;">Tempat, Tanggal Lahir</td>
            <td style="padding: 5px 0;">:</td>
            <td style="padding: 5px 0;">[tempat_lahir], [tanggal_lahir]</td>
        </tr>
        <tr>
            <td style="padding: 5px 0;">Nomor Induk Siswa Nasional (NISN)</td>
            <td style="padding: 5px 0;">:</td>
            <td style="padding: 5px 0;">[nisn]</td>
        </tr>
        <tr>
            <td style="padding: 5px 0;">Jenis Kelamin</td>
            <td style="padding: 5px 0;">:</td>
            <td style="padding: 5px 0;">[jenis_kelamin]</td>
        </tr>
        <tr>
            <td style="padding: 5px 0;">Jurusan / Program Studi</td>
            <td style="padding: 5px 0;">:</td>
            <td style="padding: 5px 0;">[jurusan]</td>
        </tr>
    </tbody>
</table>
<p style="text-indent: 30px; text-align: justify;">
    Berdasarkan hasil nilai ujian dan kriteria kelulusan yang telah ditetapkan, siswa tersebut di atas dinyatakan:
</p>
<div style="text-align: center; font-size: 24pt; font-weight: bold; margin: 30px 0; border: 2px solid #000; padding: 10px; letter-spacing: 5px;">
    [status_kelulusan]
</div>
<p style="text-indent: 30px; text-align: justify;">
    Telah menyelesaikan seluruh program pembelajaran dan memperoleh nilai rata-rata: <strong>[nilai_rata_rata]</strong>.
</p>
<p style="text-indent: 30px; text-align: justify;">
    Demikian Surat Keterangan Lulus ini dibuat dengan sebenarnya agar dapat dipergunakan sebagaimana mestinya.
</p>
HTML;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$name", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM profil_sekolah LIKE 'template_surat'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE profil_sekolah ADD COLUMN template_surat LONGTEXT NULL DEFAULT NULL AFTER tgl_pengumuman");
        
        // Update existing row with default template
        $stmt = $pdo->prepare("UPDATE profil_sekolah SET template_surat = ? WHERE id = 1");
        $stmt->execute([$defaultTemplate]);
        
        echo "Column template_surat added and populated.\n";
    } else {
        echo "Column template_surat already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
