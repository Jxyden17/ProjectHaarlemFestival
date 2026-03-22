<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Service\Interfaces\IAudioUploadService;
use App\Service\Interfaces\IImageUploadService;

class CmsMediaController extends BaseController
{
    private IImageUploadService $imageUploadService;
    private IAudioUploadService $audioUploadService;

    public function __construct(IImageUploadService $imageUploadService, IAudioUploadService $audioUploadService)
    {
        $this->imageUploadService = $imageUploadService;
        $this->audioUploadService = $audioUploadService;
    }

    public function uploadImage(): void
    {
        $this->requireAdmin();
        $result = $this->imageUploadService->uploadImage($_POST, $_FILES);
        $this->json(
            $result['body'] ?? ['success' => false, 'message' => 'Image upload failed'],
            (int)($result['status_code'] ?? 500)
        );
    }

    public function uploadAudio(): void
    {
        $this->requireAdmin();
        $result = $this->audioUploadService->uploadAudio($_POST, $_FILES);
        $this->json(
            $result['body'] ?? ['success' => false, 'message' => 'Audio upload failed'],
            (int)($result['status_code'] ?? 500)
        );
    }
}
