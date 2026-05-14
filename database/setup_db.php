<?php

declare(strict_types=1);

// Database configuration. Make sure this matches your .env
$host    = '127.0.0.1';
$port    = '3306';
$name    = 'app_kelulusan'; // Replace with your DB name
$user    = 'root_ls';
$pass    = 'password';

try {
    $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$name`");
    
    echo "Database `$name` created or already exists.\n";

    // Create Users Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `email` VARCHAR(100) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `role` ENUM('admin', 'superadmin') DEFAULT 'admin',
            `is_active` TINYINT(1) DEFAULT 1,
            `last_login_at` DATETIME NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Table `users` created.\n";

    // Create Siswa Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `siswa` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `nisn` VARCHAR(20) NOT NULL UNIQUE,
            `nama` VARCHAR(150) NOT NULL,
            `tempat_lahir` VARCHAR(100) NOT NULL,
            `tanggal_lahir` DATE NOT NULL,
            `jenis_kelamin` ENUM('L', 'P') NOT NULL,
            `jurusan` VARCHAR(100) NULL,
            `tahun_lulus` INT NOT NULL,
            `status_kelulusan` ENUM('lulus', 'tidak_lulus') NOT NULL,
            `nilai_rata_rata` DECIMAL(5,2) NULL,
            `keterangan` TEXT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Table `siswa` created.\n";

    // Create Profil Sekolah Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `profil_sekolah` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `nama_sekolah` VARCHAR(150) NOT NULL,
            `logo` VARCHAR(255) NULL,
            `alamat` TEXT NULL,
            `kepala_sekolah` VARCHAR(100) NULL,
            `nip_kepala_sekolah` VARCHAR(50) NULL,
            `website` VARCHAR(100) NULL,
            `email` VARCHAR(100) NULL,
            `telepon` VARCHAR(50) NULL,
            `tgl_pengumuman` DATETIME NULL DEFAULT NULL,
            `template_surat` LONGTEXT NULL DEFAULT NULL,
            `template_header` LONGTEXT NULL DEFAULT NULL,
            `template_footer` LONGTEXT NULL DEFAULT NULL,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Table `profil_sekolah` created.\n";

    // Insert Default Profil
    $defaultTemplate = '<p style="text-indent: 30px; text-align: justify;">Kepala [nama_sekolah] selaku Ketua Penyelenggara Ujian Sekolah Tahun Pelajaran [tahun_pelajaran] berdasarkan Keputusan Kepala Sekolah dan Rapat Dewan Guru, menerangkan bahwa:</p><table style="margin-left: 30px; width: calc(100% - 30px); margin-bottom: 20px;"><tbody><tr><td style="width: 250px; padding: 5px 0;">Nama Lengkap</td><td style="width: 20px; padding: 5px 0;">:</td><td style="padding: 5px 0;"><strong>[nama_siswa]</strong></td></tr><tr><td style="padding: 5px 0;">Tempat, Tanggal Lahir</td><td style="padding: 5px 0;">:</td><td style="padding: 5px 0;">[tempat_lahir], [tanggal_lahir]</td></tr><tr><td style="padding: 5px 0;">Nomor Induk Siswa Nasional (NISN)</td><td style="padding: 5px 0;">:</td><td style="padding: 5px 0;">[nisn]</td></tr><tr><td style="padding: 5px 0;">Jenis Kelamin</td><td style="padding: 5px 0;">:</td><td style="padding: 5px 0;">[jenis_kelamin]</td></tr><tr><td style="padding: 5px 0;">Jurusan / Program Studi</td><td style="padding: 5px 0;">:</td><td style="padding: 5px 0;">[jurusan]</td></tr></tbody></table><p style="text-indent: 30px; text-align: justify;">Berdasarkan hasil nilai ujian dan kriteria kelulusan yang telah ditetapkan, siswa tersebut di atas dinyatakan:</p><div style="text-align: center; font-size: 24pt; font-weight: bold; margin: 30px 0; border: 2px solid #000; padding: 10px; letter-spacing: 5px;">[status_kelulusan]</div><p style="text-indent: 30px; text-align: justify;">Telah menyelesaikan seluruh program pembelajaran dan memperoleh nilai rata-rata: <strong>[nilai_rata_rata]</strong>.</p><p style="text-indent: 30px; text-align: justify;">Demikian Surat Keterangan Lulus ini dibuat dengan sebenarnya agar dapat dipergunakan sebagaimana mestinya.</p>';
    $headerTemplate = '<div style="text-align: center; border-bottom: 4px solid #000; padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;"><div style="width: 100px;"><img src="[logo_sekolah]" alt="Logo" style="width: 100%; height: auto;"></div><div style="flex: 1; text-align: center; padding: 0 15px;"><p style="font-size: 16pt; font-weight: bold; margin: 0; text-transform: uppercase;">DINAS PENDIDIKAN</p><h1 style="font-size: 20pt; font-weight: bold; margin: 5px 0; text-transform: uppercase;">[nama_sekolah]</h1><p style="font-size: 11pt; margin: 0;">[alamat]<br>Website: [website] | Email: [email] | Telp: [telepon]</p></div><div style="width: 100px;"></div></div>';
    $footerTemplate = '<div style="margin-top: 50px; display: flex; justify-content: flex-end;"><div style="text-align: center; width: 300px;"><div style="margin-bottom: 5px; text-align: left; padding-left: 30px;">Ditetapkan di: ____________<br>Pada tanggal: [tanggal_surat]</div><div style="text-align: left; padding-left: 30px; margin-bottom: 80px;">Kepala Sekolah,</div><div style="font-weight: bold; text-decoration: underline; text-align: left; padding-left: 30px;">[kepala_sekolah]</div><div style="margin-top: 5px; text-align: left; padding-left: 30px;">NIP. [nip_kepala_sekolah]</div></div></div>';

    $stmt = $pdo->prepare("SELECT id FROM profil_sekolah WHERE id = 1");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $stmt = $pdo->prepare("INSERT INTO profil_sekolah (id, nama_sekolah, template_surat, template_header, template_footer) VALUES (1, 'SMA Negeri 1 Contoh', ?, ?, ?)");
        $stmt->execute([$defaultTemplate, $headerTemplate, $footerTemplate]);
        echo "Default profil sekolah created.\n";
    }

    // Insert Default Admin
    // Password is: admin123
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $stmt = $pdo->prepare("INSERT INTO users (name, username, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Administrator', 'admin', 'admin@example.com', $adminPassword, 'superadmin']);
        echo "Default admin user created. (Username: admin, Password: admin123)\n";
    } else {
        echo "Default admin user already exists.\n";
    }

    echo "\nDatabase setup complete.\n";

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage() . "\n");
}
