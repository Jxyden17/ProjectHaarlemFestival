<?php

namespace App\Models;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    // Returns a singleton PDO instance for the database connection.
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            // Load configuration
            $config = require __DIR__ . '/../Config/database.php';

            $host = $config['host'] ?? 'localhost';
            $db   = $config['dbname'] ?? 'blackjack';
            $user = $config['user'] ?? 'root';
            $pass = $config['password'] ?? '';
            $charset = $config['charset'] ?? 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                throw new \Exception('Database connection failed: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
