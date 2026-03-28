<?php

namespace App\Service\Interfaces;

interface IImageUploadService
{
    public function uploadImage(array $post, array $files): array;
}
