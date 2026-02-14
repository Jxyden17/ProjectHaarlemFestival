<?php

namespace App\Models;

class DatabaseConfig
{
    public string $host;
    public int $port;
    public string $dbname;
    public string $user;
    public string $password;
    public string $charset;

    public function __construct(
        string $host,
        int $port,
        string $dbname,
        string $user,
        string $password,
        string $charset = 'utf8mb4'
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->dbname = $dbname;
        $this->user = $user;
        $this->password = $password;
        $this->charset = $charset;
    }

    public static function fromEnvironment(): self
    {
        return new self(
            (string)($_ENV['host'] ?? getenv('host')),
            (int)($_ENV['port'] ?? getenv('port')),
            (string)($_ENV['dbname'] ?? getenv('dbname')),
            (string)($_ENV['user'] ?? getenv('user')),
            (string)($_ENV['password'] ?? getenv('password')),
            (string)($_ENV['charset'] ?? getenv('charset'))
        );
    }
}
