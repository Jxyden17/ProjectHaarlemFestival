<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;

class CmsMediaController extends BaseController
{
    public function uploadReplace(): void
    {
        $this->requireAdmin();
        $this->sendJsonHeader();

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

            $this->storeUploadedFile($request['tmp_path'], $resolved['absolute_target']);
            $this->cleanupOldFileIfRenamed($resolved);
            $this->respondUploadSuccess($resolved['public_path']);
        } catch (\Throwable $e) {
            $this->respondUploadException($e);
        }
    }

    private function parseUploadRequest(): ?array
    {
        if (!$this->isPostRequest()) {
            $this->respondJsonError(405, 'Method not allowed');
            return null;
        }

        $file = $this->extractUploadedImageFile();
        if ($file === null) {
            return null;
        }

        if (!$this->hasValidSlotIndex()) {
            return null;
        }

        $moduleConfig = $this->parseModuleConfigFromRequest();
        if ($moduleConfig === null) {
            return null;
        }

        $currentPath = $this->parseCurrentPathFromRequest();
        if ($currentPath === null) {
            return null;
        }

        $uploadFileInfo = $this->parseUploadedFileInfo($file);
        if ($uploadFileInfo === null) {
            return null;
        }

        return [
            'module_config' => $moduleConfig,
            'current_path' => $currentPath,
            'tmp_path' => $uploadFileInfo['tmp_path'],
            'extension' => $uploadFileInfo['extension'],
        ];
    }

    private function parseUploadedFileInfo(array $file): ?array
    {
        if (!$this->validateUploadErrorCode((int)($file['error'] ?? UPLOAD_ERR_NO_FILE))) {
            return null;
        }

        $tmpPath = $this->extractValidTmpPath($file);
        if ($tmpPath === null) {
            return null;
        }

        if (!$this->validateUploadSize((int)($file['size'] ?? 0))) {
            return null;
        }

        $extension = $this->resolveUploadExtension($tmpPath);
        if ($extension === null) {
            $this->respondJsonError(400, 'Only JPG, PNG, and WEBP are allowed');
            return null;
        }

        return [
            'tmp_path' => $tmpPath,
            'extension' => $extension,
        ];
    }

    private function isPostRequest(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
    }

    private function extractUploadedImageFile(): ?array
    {
        if (!isset($_FILES['image']) || !is_array($_FILES['image'])) {
            $this->respondJsonError(400, 'No image uploaded');
            return null;
        }

        return $_FILES['image'];
    }

    private function hasValidSlotIndex(): bool
    {
        $slotIndex = isset($_POST['slot_index']) ? (int)$_POST['slot_index'] : -1;
        if ($slotIndex < 0) {
            $this->respondJsonError(400, 'Invalid slot index');
            return false;
        }

        return true;
    }

    private function parseModuleConfigFromRequest(): ?array
    {
        $module = trim((string)($_POST['module'] ?? ''));
        $moduleConfig = $this->resolveModuleConfig($module);
        if ($moduleConfig === null) {
            $this->respondJsonError(400, 'Invalid media module');
            return null;
        }

        return $moduleConfig;
    }

    private function parseCurrentPathFromRequest(): ?string
    {
        $currentPath = trim((string)($_POST['current_path'] ?? ''));
        if ($currentPath === '') {
            $this->respondJsonError(400, 'No target image path provided');
            return null;
        }

        return $currentPath;
    }

    private function validateUploadErrorCode(int $errorCode): bool
    {
        if ($errorCode === UPLOAD_ERR_OK) {
            return true;
        }

        $message = $this->uploadErrorMessage($errorCode);
        if ($this->isDebugEnabled()) {
            $message .= ' (code: ' . $errorCode . ')';
        }
        $this->respondJsonError(400, $message);

        return false;
    }

    private function extractValidTmpPath(array $file): ?string
    {
        $tmpPath = (string)($file['tmp_name'] ?? '');
        if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
            $this->respondJsonError(400, 'Invalid upload payload');
            return null;
        }

        return $tmpPath;
    }

    private function validateUploadSize(int $size): bool
    {
        if ($size > 0 && $size <= 10 * 1024 * 1024) {
            return true;
        }

        $this->respondJsonError(400, 'Image must be between 1 byte and 10MB');

        return false;
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
        if (!$this->isAllowedModulePath($moduleConfig, $publicPath) || str_contains($publicPath, '..')) {
            return null;
        }

        $pathParts = $this->extractTargetPathParts($publicPath);
        if ($pathParts === null) {
            return null;
        }

        $newPublicPath = $this->buildPublicPathForUpload($publicPath, $pathParts, $uploadExt);

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

    private function isAllowedModulePath(array $moduleConfig, string $publicPath): bool
    {
        $allowedPrefixes = $moduleConfig['allowed_prefixes'] ?? [];
        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($publicPath, (string)$prefix)) {
                return true;
            }
        }

        return false;
    }

    private function extractTargetPathParts(string $publicPath): ?array
    {
        $pathInfo = pathinfo($publicPath);
        $baseName = (string)($pathInfo['filename'] ?? '');
        $directory = (string)($pathInfo['dirname'] ?? '');
        $targetExt = strtolower((string)($pathInfo['extension'] ?? ''));

        if ($targetExt === '' || $baseName === '' || $directory === '' || $directory === '.') {
            return null;
        }

        return [
            'base_name' => $baseName,
            'directory' => $directory,
            'extension' => $targetExt,
        ];
    }

    private function buildPublicPathForUpload(string $currentPublicPath, array $pathParts, string $uploadExt): string
    {
        $normalizedTargetExt = $pathParts['extension'] === 'jpeg' ? 'jpg' : $pathParts['extension'];
        if ($normalizedTargetExt === $uploadExt) {
            return $currentPublicPath;
        }

        return rtrim($pathParts['directory'], '/') . '/' . $pathParts['base_name'] . '.' . $uploadExt;
    }

    private function sendJsonHeader(): void
    {
        header('Content-Type: application/json');
    }

    private function cleanupOldFileIfRenamed(array $resolved): void
    {
        if ($resolved['public_path'] !== $resolved['current_public_path'] && is_file($resolved['absolute_current'])) {
            @unlink($resolved['absolute_current']);
        }
    }

    private function respondUploadSuccess(string $publicPath): void
    {
        echo json_encode([
            'success' => true,
            'path' => $publicPath,
        ]);
    }

    private function respondUploadException(\Throwable $e): void
    {
        http_response_code(500);
        $message = 'Image upload failed';
        if ($this->isDebugEnabled()) {
            $message .= ': ' . $e->getMessage();
        }

        echo json_encode(['success' => false, 'message' => $message]);
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
