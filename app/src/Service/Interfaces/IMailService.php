<?php

namespace App\Service\Interfaces;

interface IMailService
{
    // Sends one plain-text email so callers can trigger outbound mail without knowing the SMTP implementation details.
    public function sendMail(string $to, string $subject, string $body): bool;
}
