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
     * Export the entire database as a SQL string.
     */
    public static function export(): string
    {
        $db = Database::getInstance();
        $pdo = $db->getPdo();
        $tables = [];
        
        $result = $pdo->query("SHOW TABLES");
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $output = "-- Database Backup\n";
        $output .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";
        $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            // Drop table
            $output .= "DROP TABLE IF EXISTS `{$table}`;\n";
            
            // Create table
            $res = $pdo->query("SHOW CREATE TABLE `{$table}`");
            $row = $res->fetch(PDO::FETCH_NUM);
            $output .= $row[1] . ";\n\n";

            // Data
            $res = $pdo->query("SELECT * FROM `{$table}`");
            while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
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
