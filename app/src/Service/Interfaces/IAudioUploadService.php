<?php

namespace App\Service\Interfaces;

interface IAudioUploadService
{
    public function uploadAudio(array $post, array $files): array;
}
