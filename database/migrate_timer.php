<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$name", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM profil_sekolah LIKE 'tgl_pengumuman'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE profil_sekolah ADD COLUMN tgl_pengumuman DATETIME NULL DEFAULT NULL AFTER telepon");
        echo "Column tgl_pengumuman added.\n";
    } else {
        echo "Column tgl_pengumuman already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
