<?php
declare(strict_types=1);

$host    = '127.0.0.1';
$port    = '3306';
$name    = 'app_kelulusan';
$user    = 'root_ls';
$pass    = 'password';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$name", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Modify the column
    $pdo->exec("ALTER TABLE siswa MODIFY COLUMN jurusan VARCHAR(100) NULL DEFAULT NULL");
    
    echo "Column jurusan is now nullable.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
