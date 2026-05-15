<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;

/**
 * Backup Service — Handles database export.
 */
final class BackupService
{
    /**
     * Export selected tables with optional filters.
     */
    public static function export(array $selectedTables = [], array $filters = []): string
    {
        $db = Database::getInstance();
        $pdo = $db->getPdo();
        
        if (empty($selectedTables)) {
            $result = $pdo->query("SHOW TABLES");
            while ($row = $result->fetch(PDO::FETCH_NUM)) {
                $selectedTables[] = $row[0];
            }
        }

        $output = "-- Database Backup\n";
        $output .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";
        $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($selectedTables as $table) {
            // Drop table
            $output .= "DROP TABLE IF EXISTS `{$table}`;\n";
            
            // Create table
            $res = $pdo->query("SHOW CREATE TABLE `{$table}`");
            $row = $res->fetch(PDO::FETCH_NUM);
            $output .= $row[1] . ";\n\n";

            // Data
            $query = "SELECT * FROM `{$table}`";
            $bindings = [];

            // Apply filters if table is 'siswa'
            if ($table === 'siswa' && !empty($filters['tahun_lulus'])) {
                $query .= " WHERE tahun_lulus = :tahun";
                $bindings['tahun'] = $filters['tahun_lulus'];
            }

            $stmt = $pdo->prepare($query);
            $stmt->execute($bindings);
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $columns = array_keys($row);
                $values = array_values($row);
                
                $escapedValues = array_map(function ($val) use ($pdo) {
                    if ($val === null) return 'NULL';
                    return $pdo->quote((string)$val);
                }, $values);

                $output .= "INSERT INTO `{$table}` (`" . implode("`, `", $columns) . "`) VALUES (" . implode(", ", $escapedValues) . ");\n";
            }
            $output .= "\n";
        }

        $output .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return $output;
    }
}
