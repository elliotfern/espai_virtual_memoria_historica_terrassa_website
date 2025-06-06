<?php

namespace App\Config;

use PDO;
use PDOException;

class DatabaseConnection
{
    private static ?PDO $conn = null;

    public static function getConnection(): ?PDO
    {
        if (self::$conn === null) {
            $dbHost = $_ENV['DB_HOST'] ?? null;
            $dbUser = $_ENV['DB_USER'] ?? null;
            $dbPass = $_ENV['DB_PASS'] ?? null;
            $dbName = $_ENV['DB_DBNAME'] ?? null;

            if (!$dbHost || !$dbUser || !$dbName) {
                error_log("Variables de entorno de BD no definidas correctamente");
                return null;
            }

            try {
                self::$conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
                self::$conn->exec("SET NAMES utf8");
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                error_log("Error de conexión: " . $e->getMessage());
                return null;
            }
        }

        return self::$conn;
    }
}
