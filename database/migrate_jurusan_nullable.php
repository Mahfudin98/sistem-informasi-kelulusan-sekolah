<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$name", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Modify the column
    $pdo->exec("ALTER TABLE siswa MODIFY COLUMN jurusan VARCHAR(100) NULL DEFAULT NULL");
    
    echo "Column jurusan is now nullable.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
