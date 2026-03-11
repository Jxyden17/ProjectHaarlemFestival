<?php

namespace App\Service\Interfaces;

interface IMediaService
{
    public function uploadReplace(array $server, array $post, array $files): array;
    public function uploadAudio(array $server, array $post, array $files): array;
}
