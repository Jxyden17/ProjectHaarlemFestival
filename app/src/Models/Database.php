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
            try {
                self::$instance = self::createConnection();
            } catch (PDOException $e) {
                throw new \Exception('Database connection failed: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }

    private static function createConnection(): PDO
    {
        $config = DatabaseConfig::fromEnvironment();

        $dsn = "mysql:host={$config->host};port={$config->port};dbname={$config->dbname};charset={$config->charset}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        return new PDO($dsn, $config->user, $config->password, $options);
    }

}
