<?php

namespace App\Service;

use App\Models\MailConfig;
use App\Service\Interfaces\IMailService;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailService implements IMailService
{
    private MailConfig $config;

    // Stores the mail configuration so each send can reuse the same SMTP host, auth, and sender settings.
    public function __construct(MailConfig $config)
    {
        $this->config = $config;
    }

    // Sends one plain-text email over SMTP so auth and password-reset flows can trigger outbound mail. Example: to 'user@example.com' -> true.
    public function sendMail(string $to, string $subject, string $body): bool
    {
        return $this->sendMessage($to, $subject, $body, '', [], []);
    }

    public function sendTicketMail(
        string $to,
        string $subject,
        string $textBody,
        string $htmlBody,
        array $inlineImages = [],
        array $attachments = []
    ): bool
    {
        return $this->sendMessage($to, $subject, $textBody, $htmlBody, $inlineImages, $attachments);
    }

    private function sendMessage(
        string $to,
        string $subject,
        string $textBody,
        string $htmlBody = '',
        array $inlineImages = [],
        array $attachments = []
    ): bool
    {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $this->config->host;
            $mail->Port = $this->config->port;
            $mail->SMTPAuth = $this->config->username !== '';

            if ($mail->SMTPAuth) {
                $mail->Username = $this->config->username;
                $mail->Password = $this->config->password;
            }

            if ($this->config->encryption === 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } elseif ($this->config->encryption === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = '';
                $mail->SMTPAutoTLS = false;
            }

            $mail->setFrom($this->config->fromAddress, $this->config->fromName);
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody !== '' ? $htmlBody : $textBody;
            $mail->AltBody = $textBody;
            $mail->isHTML($htmlBody !== '');

            foreach ($inlineImages as $image) {
                $cid = (string) ($image['cid'] ?? '');
                $data = $image['data'] ?? null;
                $name = (string) ($image['name'] ?? 'ticket-qr.png');
                $mimeType = (string) ($image['mimeType'] ?? 'image/png');

                if ($cid === '' || !is_string($data) || $data === '') {
                    continue;
                }

                $mail->addStringEmbeddedImage(
                    $data,
                    $cid,
                    $name,
                    PHPMailer::ENCODING_BASE64,
                    $mimeType
                );
            }

            foreach ($attachments as $attachment) {
                $data = $attachment['data'] ?? null;
                $name = (string) ($attachment['name'] ?? 'attachment.bin');
                $mimeType = (string) ($attachment['mimeType'] ?? 'application/octet-stream');

                if (!is_string($data) || $data === '') {
                    continue;
                }

                $mail->addStringAttachment(
                    $data,
                    $name,
                    PHPMailer::ENCODING_BASE64,
                    $mimeType
                );
            }

            return $mail->send();
        } catch (Exception $e) {
            error_log('Mail send failed: ' . $e->getMessage());
            return false;
        }
    }
}
