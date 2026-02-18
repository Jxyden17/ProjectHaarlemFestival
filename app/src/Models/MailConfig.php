<?php

namespace App\Models;

class MailConfig
{
    public string $host;
    public int $port;
    public string $fromAddress;
    public string $fromName;
    public string $username;
    public string $password;
    public string $encryption;

    public function __construct(
        string $host,
        int $port,
        string $fromAddress,
        string $fromName,
        string $username = '',
        string $password = '',
        string $encryption = ''
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
        $this->username = $username;
        $this->password = $password;
        $this->encryption = strtolower(trim($encryption));
    }

    public static function fromEnvironment(): self
    {
        return new self(
            (string)($_ENV['MAIL_HOST'] ?? getenv('MAIL_HOST')),
            (int)($_ENV['MAIL_PORT'] ?? getenv('MAIL_PORT')),
            (string)($_ENV['MAIL_FROM_ADDRESS'] ?? getenv('MAIL_FROM_ADDRESS')),
            (string)($_ENV['MAIL_FROM_NAME'] ?? getenv('MAIL_FROM_NAME')),
            (string)($_ENV['MAIL_USERNAME'] ?? getenv('MAIL_USERNAME')),
            (string)($_ENV['MAIL_PASSWORD'] ?? getenv('MAIL_PASSWORD')),
            (string)($_ENV['MAIL_ENCRYPTION'] ?? getenv('MAIL_ENCRYPTION'))
        );
    }
}
