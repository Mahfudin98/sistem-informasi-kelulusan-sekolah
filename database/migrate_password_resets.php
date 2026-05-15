<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$name", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `password_resets` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `email` VARCHAR(100) NOT NULL,
            `token` VARCHAR(100) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (`email`),
            INDEX (`token`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    echo "Table `password_resets` created successfully.\n";

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage() . "\n");
}
