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
        header('Content-Type: application/json');
        $result = $this->mediaService->uploadReplace($_SERVER, $_POST, $_FILES);
        http_response_code((int)($result['status_code'] ?? 500));
        echo json_encode($result['body'] ?? ['success' => false, 'message' => 'Image upload failed']);
    }
}
