<?php

namespace App\Service\Cms;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

class TicketQrCodeService
{
    public function renderPng(string $qrCode): string
    {
        $result = new Builder(
            writer: new PngWriter(),
            data: $qrCode,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 280,
            margin: 12
        );

        return $result->build()->getString();
    }
}
