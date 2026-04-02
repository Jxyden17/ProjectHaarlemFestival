<?php

namespace App\Service\Interfaces;

interface IImageUploadService
{
    // Uploads one image file and returns a response payload so controllers can send consistent JSON back to the client.
    public function uploadImage(array $post, array $files): array;
}
