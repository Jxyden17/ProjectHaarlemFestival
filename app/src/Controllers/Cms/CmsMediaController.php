<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;

class CmsMediaController extends BaseController
{
    public function uploadReplace(): void
    {
        $this->requireAdmin();

        header('Content-Type: application/json');

        try {
            $request = $this->parseUploadRequest();
            if ($request === null) {
                return;
            }

            $resolved = $this->resolveExistingTarget($request['module_config'], $request['current_path'], $request['extension']);
            if ($resolved === null) {
                $this->respondJsonError(400, 'Target path is not allowed for this module');
                return;
            }

            $absoluteTarget = $resolved['absolute_target'];
            $publicPath = $resolved['public_path'];
            $absoluteCurrent = $resolved['absolute_current'];
            $currentPublicPath = $resolved['current_public_path'];

            $this->storeUploadedFile($request['tmp_path'], $absoluteTarget);

            if ($publicPath !== $currentPublicPath && is_file($absoluteCurrent)) {
                @unlink($absoluteCurrent);
            }

            echo json_encode([
                'success' => true,
                'path' => $publicPath,
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            $message = 'Image upload failed';
            if ($this->isDebugEnabled()) {
                $message .= ': ' . $e->getMessage();
            }
            echo json_encode(['success' => false, 'message' => $message]);
        }
    }

    private function parseUploadRequest(): ?array
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJsonError(405, 'Method not allowed');
            return null;
        }

        if (!isset($_FILES['image']) || !is_array($_FILES['image'])) {
            $this->respondJsonError(400, 'No image uploaded');
            return null;
        }

        $slotIndex = isset($_POST['slot_index']) ? (int)$_POST['slot_index'] : -1;
        if ($slotIndex < 0) {
            $this->respondJsonError(400, 'Invalid slot index');
            return null;
        }

        $module = trim((string)($_POST['module'] ?? ''));
        $moduleConfig = $this->resolveModuleConfig($module);
        if ($moduleConfig === null) {
            $this->respondJsonError(400, 'Invalid media module');
            return null;
        }

        $currentPath = trim((string)($_POST['current_path'] ?? ''));
        if ($currentPath === '') {
            $this->respondJsonError(400, 'No target image path provided');
            return null;
        }

        $file = $_FILES['image'];
        $errorCode = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($errorCode !== UPLOAD_ERR_OK) {
            $message = $this->uploadErrorMessage($errorCode);
            if ($this->isDebugEnabled()) {
                $message .= ' (code: ' . $errorCode . ')';
            }
            $this->respondJsonError(400, $message);
            return null;
        }

        $tmpPath = (string)($file['tmp_name'] ?? '');
        if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
            $this->respondJsonError(400, 'Invalid upload payload');
            return null;
        }

        $size = (int)($file['size'] ?? 0);
        if ($size <= 0 || $size > 5 * 1024 * 1024) {
            $this->respondJsonError(400, 'Image must be between 1 byte and 5MB');
            return null;
        }

        $extension = $this->resolveUploadExtension($tmpPath);
        if ($extension === null) {
            $this->respondJsonError(400, 'Only JPG, PNG, and WEBP are allowed');
            return null;
        }

        return [
            'module_config' => $moduleConfig,
            'current_path' => $currentPath,
            'tmp_path' => $tmpPath,
            'extension' => $extension,
        ];
    }

    private function resolveUploadExtension(string $tmpPath): ?string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = (string)$finfo->file($tmpPath);
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        return $allowed[$mimeType] ?? null;
    }

    private function storeUploadedFile(string $tmpPath, string $absoluteTarget): void
    {
        if (!move_uploaded_file($tmpPath, $absoluteTarget)) {
            $debugContext = $this->isDebugEnabled()
                ? ' tmp=' . $tmpPath . ' target=' . $absoluteTarget
                : '';
            throw new \RuntimeException('Failed to save uploaded image.' . $debugContext);
        }
    }

    private function respondJsonError(int $statusCode, string $message): void
    {
        http_response_code($statusCode);
        echo json_encode(['success' => false, 'message' => $message]);
    }

    private function resolveModuleConfig(string $module): ?array
    {
        $map = [
            'dance_artist' => [
                'allowed_prefixes' => ['/img/danceIMG/'],
            ],
        ];

        return $map[$module] ?? null;
    }

    private function resolveExistingTarget(array $moduleConfig, string $publicPath, string $uploadExt): ?array
    {
        $allowedPrefixes = $moduleConfig['allowed_prefixes'] ?? [];
        $isAllowed = false;
        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($publicPath, (string)$prefix)) {
                $isAllowed = true;
                break;
            }
        }
        if (!$isAllowed) {
            return null;
        }

        if (str_contains($publicPath, '..')) {
            return null;
        }

        $pathInfo = pathinfo($publicPath);
        $baseName = (string)($pathInfo['filename'] ?? '');
        $directory = (string)($pathInfo['dirname'] ?? '');
        $targetExt = strtolower((string)($pathInfo['extension'] ?? ''));
        if ($targetExt === '' || $baseName === '' || $directory === '' || $directory === '.') {
            return null;
        }

        $normalizedTargetExt = $targetExt === 'jpeg' ? 'jpg' : $targetExt;
        $newPublicPath = $publicPath;
        if ($normalizedTargetExt !== $uploadExt) {
            $newPublicPath = rtrim($directory, '/') . '/' . $baseName . '.' . $uploadExt;
        }

        $absoluteCurrent = dirname(__DIR__, 3) . '/public' . $publicPath;
        $absoluteTarget = dirname(__DIR__, 3) . '/public' . $newPublicPath;
        $absoluteDir = dirname($absoluteTarget);
        if (!is_dir($absoluteDir)) {
            return null;
        }

        return [
            'absolute_target' => $absoluteTarget,
            'public_path' => $newPublicPath,
            'absolute_current' => $absoluteCurrent,
            'current_public_path' => $publicPath,
        ];
    }

    private function uploadErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE => 'Upload exceeds server upload_max_filesize.',
            UPLOAD_ERR_FORM_SIZE => 'Upload exceeds form MAX_FILE_SIZE.',
            UPLOAD_ERR_PARTIAL => 'Upload was only partially completed.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary upload directory.',
            UPLOAD_ERR_CANT_WRITE => 'Server failed to write uploaded file to disk.',
            UPLOAD_ERR_EXTENSION => 'Upload stopped by a PHP extension.',
            default => 'Upload failed.',
        };
    }

    private function isDebugEnabled(): bool
    {
        return filter_var($_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG') ?? false, FILTER_VALIDATE_BOOLEAN);
    }
}
