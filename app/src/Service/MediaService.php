<?php

namespace App\Service;

use App\Models\Media\MediaModuleConfig;
use App\Repository\Interfaces\IMediaRepository;

abstract class MediaService
{
    private const MAX_UPLOAD_SIZE_BYTES = 10 * 1024 * 1024;

    protected IMediaRepository $mediaRepository;

    public function __construct(IMediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    protected function getUploadedFile(array $files, string $fieldName): array
    {
        if (!isset($files[$fieldName]) || !is_array($files[$fieldName])) {
            throw new \RuntimeException('No ' . $fieldName . ' uploaded', 400);
        }

        return $files[$fieldName];
    }

    protected function validateUploadedFile(array $file, array $allowedMimeMap, string $allowedExtensionMessage, string $mediaType): array
    {
        $errorCode = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($errorCode !== UPLOAD_ERR_OK) {
            $message = $this->mapUploadErrorMessage($errorCode);
            if ($this->isDebugMode()) {
                $message .= ' (code: ' . $errorCode . ')';
            }

            throw new \RuntimeException($message, 400);
        }

        $tmpPath = (string)($file['tmp_name'] ?? '');
        if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
            throw new \RuntimeException('Invalid upload payload', 400);
        }

        $size = (int)($file['size'] ?? 0);
        if ($size <= 0 || $size > self::MAX_UPLOAD_SIZE_BYTES) {
            throw new \RuntimeException(
                ucfirst($mediaType) . ' must be between 1 byte and ' . (int)(self::MAX_UPLOAD_SIZE_BYTES / 1024 / 1024) . 'MB',
                400
            );
        }

        $extension = $this->detectUploadExtension($tmpPath, $allowedMimeMap);
        if ($extension === null) {
            throw new \RuntimeException($allowedExtensionMessage, 400);
        }

        return [
            'tmp_path' => $tmpPath,
            'extension' => $extension,
        ];
    }

    protected function getPostedSectionItemId(array $post): ?int
    {
        $sectionItemId = isset($post['section_item_id']) ? (int)$post['section_item_id'] : 0;

        return $sectionItemId > 0 ? $sectionItemId : null;
    }

    protected function buildUploadTargetPaths(MediaModuleConfig $moduleConfig, string $publicPath, string $uploadExt, bool $autoCreateDir): ?array
    {
        if (!$this->isPathAllowedForModule($moduleConfig, $publicPath) || str_contains($publicPath, '..')) {
            return null;
        }

        $pathParts = $this->parseTargetPathParts($publicPath);
        if ($pathParts === null) {
            return null;
        }

        $newPublicPath = $this->resolveFinalPublicPath($publicPath, $pathParts, $uploadExt);
        $absoluteCurrent = dirname(__DIR__, 2) . '/public' . $publicPath;
        $absoluteTarget = dirname(__DIR__, 2) . '/public' . $newPublicPath;
        $absoluteDir = dirname($absoluteTarget);

        if (!is_dir($absoluteDir) && !($autoCreateDir && @mkdir($absoluteDir, 0775, true)) && !is_dir($absoluteDir)) {
            return null;
        }

        return [
            'absolute_target' => $absoluteTarget,
            'public_path' => $newPublicPath,
            'absolute_current' => $absoluteCurrent,
            'current_public_path' => $publicPath,
        ];
    }

    protected function buildAudioUploadTargetPaths(MediaModuleConfig $moduleConfig, string $currentPublicPath, string $uploadExt, ?int $sectionItemId): ?array
    {
        $publicPath = $currentPublicPath;
        if ($publicPath === '') {
            $primaryPrefix = $moduleConfig->allowedPrefixes[0] ?? '';
            if ($primaryPrefix === '' || $sectionItemId === null || $sectionItemId <= 0) {
                return null;
            }

            $publicPath = rtrim((string)$primaryPrefix, '/') . '/track-' . $sectionItemId . '.' . $uploadExt;
        }

        return $this->buildUploadTargetPaths($moduleConfig, $publicPath, $uploadExt, true);
    }

    protected function moveUploadedFileToTarget(string $tmpPath, string $absoluteTarget): void
    {
        if (!move_uploaded_file($tmpPath, $absoluteTarget)) {
            $debugContext = $this->isDebugMode()
                ? ' tmp=' . $tmpPath . ' target=' . $absoluteTarget
                : '';

            throw new \RuntimeException('Failed to save uploaded file.' . $debugContext, 500);
        }
    }

    protected function removeOldFileWhenExtensionChanged(array $resolved): void
    {
        if ($resolved['public_path'] !== $resolved['current_public_path'] && is_file($resolved['absolute_current'])) {
            @unlink($resolved['absolute_current']);
        }
    }

    protected function buildUploadSuccessResponse(string $publicPath, array $debugContext = []): array
    {
        $body = [
            'success' => true,
            'path' => $publicPath,
        ];

        if ($this->isDebugMode()) {
            $body['debug'] = $debugContext;
        }

        return [
            'status_code' => 200,
            'body' => $body,
        ];
    }

    protected function buildUploadErrorResponse(\Throwable $e, string $fallbackMessage): array
    {
        if ($e instanceof \RuntimeException) {
            $statusCode = (int)$e->getCode();
            if ($statusCode < 400 || $statusCode > 599) {
                $statusCode = 400;
            }

            return [
                'status_code' => $statusCode,
                'body' => [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
            ];
        }

        $message = $fallbackMessage;
        if ($this->isDebugMode()) {
            $message .= ': ' . $e->getMessage();
        }

        return [
            'status_code' => 500,
            'body' => [
                'success' => false,
                'message' => $message,
            ],
        ];
    }

    protected function buildUploadDebugContext(string $dbSyncStatus, ?int $sectionItemId, array $post, string $currentPath, MediaModuleConfig $moduleConfig): array
    {
        return [
            'db_sync' => $dbSyncStatus,
            'section_item_id' => $sectionItemId,
            'posted_section_item_id' => isset($post['section_item_id']) ? (string)$post['section_item_id'] : null,
            'current_path' => $currentPath,
            'page_slug' => $moduleConfig->pageSlug,
            'section_type' => $moduleConfig->sectionType,
            'item_category' => $moduleConfig->itemCategory,
        ];
    }

    protected function isDebugMode(): bool
    {
        return filter_var($_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG') ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    private function detectUploadExtension(string $tmpPath, array $allowedMimeMap): ?string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = (string)$finfo->file($tmpPath);

        return $allowedMimeMap[$mimeType] ?? null;
    }

    private function isPathAllowedForModule(MediaModuleConfig $moduleConfig, string $publicPath): bool
    {
        foreach ($moduleConfig->allowedPrefixes as $prefix) {
            if (str_starts_with($publicPath, (string)$prefix)) {
                return true;
            }
        }

        return false;
    }

    private function parseTargetPathParts(string $publicPath): ?array
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

    private function resolveFinalPublicPath(string $currentPublicPath, array $pathParts, string $uploadExt): string
    {
        $normalizedTargetExt = $pathParts['extension'] === 'jpeg' ? 'jpg' : $pathParts['extension'];
        if ($normalizedTargetExt === $uploadExt) {
            return $currentPublicPath;
        }

        return rtrim($pathParts['directory'], '/') . '/' . $pathParts['base_name'] . '.' . $uploadExt;
    }

    private function mapUploadErrorMessage(int $errorCode): string
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
}
