<?php

namespace App\Service\Interfaces;

interface IMailService
{
    public function sendMail(string $to, string $subject, string $body): bool;

    public function sendTicketMail(
        string $to,
        string $subject,
        string $textBody,
        string $htmlBody,
        array $inlineImages = [],
        array $attachments = []
    ): bool;
}
