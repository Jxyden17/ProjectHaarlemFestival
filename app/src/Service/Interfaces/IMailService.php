<?php

namespace App\Service\Interfaces;

interface IMailService
{
    public function sendMail(string $to, string $subject, string $body): bool;
}
