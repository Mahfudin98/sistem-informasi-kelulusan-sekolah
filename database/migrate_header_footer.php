<?php
declare(strict_types=1);

$host    = '127.0.0.1';
$port    = '3306';
$name    = 'app_kelulusan';
$user    = 'root_ls';
$pass    = 'password';

$headerTemplate = <<<HTML
<div style="text-align: center; border-bottom: 4px solid #000; padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
    <div style="width: 100px;">
        <img src="[logo_sekolah]" alt="Logo" style="width: 100%; height: auto;">
    </div>
    <div style="flex: 1; text-align: center; padding: 0 15px;">
        <p style="font-size: 16pt; font-weight: bold; margin: 0; text-transform: uppercase;">DINAS PENDIDIKAN</p>
        <h1 style="font-size: 20pt; font-weight: bold; margin: 5px 0; text-transform: uppercase;">[nama_sekolah]</h1>
        <p style="font-size: 11pt; margin: 0;">
            [alamat]<br>
            Website: [website] | Email: [email] | Telp: [telepon]
        </p>
    </div>
    <div style="width: 100px;"></div>
</div>
HTML;

$footerTemplate = <<<HTML
<div style="margin-top: 50px; display: flex; justify-content: flex-end;">
    <div style="text-align: center; width: 300px;">
        <div style="margin-bottom: 5px; text-align: left; padding-left: 30px;">Ditetapkan di: ____________<br>Pada tanggal: [tanggal_surat]</div>
        <div style="text-align: left; padding-left: 30px; margin-bottom: 80px;">Kepala Sekolah,</div>
        <div style="font-weight: bold; text-decoration: underline; text-align: left; padding-left: 30px;">[kepala_sekolah]</div>
        <div style="margin-top: 5px; text-align: left; padding-left: 30px;">NIP. [nip_kepala_sekolah]</div>
    </div>
</div>
HTML;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$name", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM profil_sekolah LIKE 'template_header'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE profil_sekolah ADD COLUMN template_header LONGTEXT NULL DEFAULT NULL AFTER template_surat");
        $pdo->exec("ALTER TABLE profil_sekolah ADD COLUMN template_footer LONGTEXT NULL DEFAULT NULL AFTER template_header");
        
        // Update existing row with default template
        $stmt = $pdo->prepare("UPDATE profil_sekolah SET template_header = ?, template_footer = ? WHERE id = 1");
        $stmt->execute([$headerTemplate, $footerTemplate]);
        
        echo "Columns template_header and template_footer added and populated.\n";
    } else {
        echo "Columns already exist.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
