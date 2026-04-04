<?php

namespace App\Service\Interfaces;

interface IAudioUploadService
{
    // Uploads one audio file and returns a response payload so controllers can send consistent JSON back to the client.
    public function uploadAudio(array $post, array $files): array;
}
