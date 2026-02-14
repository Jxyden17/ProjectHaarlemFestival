<?php

namespace App\Service;

use App\Models\MailConfig;
use App\Service\Interfaces\IMailService;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailService implements IMailService
{
    private MailConfig $config;

    public function __construct(MailConfig $config)
    {
        $this->config = $config;
    }

    public function sendMail(string $to, string $subject, string $body): bool
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
            $mail->Body = $body;
            $mail->isHTML(false);

            return $mail->send();
        } catch (Exception $e) {
            error_log('Mail send failed: ' . $e->getMessage());
            return false;
        }
    }
}
