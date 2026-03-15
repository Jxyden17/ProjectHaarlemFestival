<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Service\Interfaces\IMediaService;

class CmsMediaController extends BaseController
{
    private IMediaService $mediaService;

    public function __construct(IMediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function uploadReplace(): void
    {
        $this->requireAdmin();
        $result = $this->mediaService->uploadReplace($_SERVER, $_POST, $_FILES);
        $this->json(
            $result['body'] ?? ['success' => false, 'message' => 'Image upload failed'],
            (int)($result['status_code'] ?? 500)
        );
    }

    public function uploadAudio(): void
    {
        $this->requireAdmin();
        $result = $this->mediaService->uploadAudio($_SERVER, $_POST, $_FILES);
        $this->json(
            $result['body'] ?? ['success' => false, 'message' => 'Audio upload failed'],
            (int)($result['status_code'] ?? 500)
        );
    }
}
