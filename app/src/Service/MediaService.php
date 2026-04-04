<?php

namespace App\Service;

use App\Models\Media\MediaModuleConfig;
use App\Repository\Interfaces\IMediaRepository;

abstract class MediaService
{
    private const MAX_UPLOAD_SIZE_BYTES = 10 * 1024 * 1024;

    protected IMediaRepository $mediaRepository;

    // Stores the media repository so shared upload helpers can resolve and sync section-item targets.
    public function __construct(IMediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    // Pulls one uploaded file payload from the request so later validation always receives the expected array shape.
    protected function getUploadedFile(array $files, string $fieldName): array
    {
        if (!isset($files[$fieldName]) || !is_array($files[$fieldName])) {
            throw new \RuntimeException('No ' . $fieldName . ' uploaded', 400);
        }

        return $files[$fieldName];
    }

    // Validates one uploaded file so only safe size and MIME combinations continue to storage. Example: MIME 'image/png' -> extension 'png'.
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

    // Extracts a posted section item id so uploads can target an existing row when the client sends one.
    protected function getPostedSectionItemId(array $post): ?int
    {
        $sectionItemId = isset($post['section_item_id']) ? (int)$post['section_item_id'] : 0;

        return $sectionItemId > 0 ? $sectionItemId : null;
    }

    // Resolves current and target filesystem paths so uploads stay inside approved public prefixes.
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

    // Resolves an audio upload target and can generate a new track filename so audio uploads work before a path exists.
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

    protected function buildImageUploadTargetPaths(MediaModuleConfig $moduleConfig, string $currentPublicPath, string $uploadExt, ?int $sectionItemId): ?array
    {
        $publicPath = $currentPublicPath;
        if ($publicPath === '') {
            $primaryPrefix = $moduleConfig->allowedPrefixes[0] ?? '';
            if ($primaryPrefix === '' || $sectionItemId === null || $sectionItemId <= 0) {
                return null;
            }

            $publicPath = rtrim((string) $primaryPrefix, '/') . '/item-' . $sectionItemId . '.' . $uploadExt;
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

    // Removes the old file when the extension changes so stale variants do not remain on disk.
    protected function removeOldFileWhenExtensionChanged(array $resolved): void
    {
        if ($resolved['public_path'] !== $resolved['current_public_path'] && is_file($resolved['absolute_current'])) {
            @unlink($resolved['absolute_current']);
        }
    }

    // Builds a consistent upload success payload so callers can return the same JSON shape for every media module.
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

    // Builds a consistent upload error payload so callers can surface runtime and unexpected failures the same way.
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

    // Builds optional debug details so upload responses explain which page, section, and item were targeted in debug mode.
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

    // Checks debug mode so upload responses can include extra diagnostics without exposing them in production.
    protected function isDebugMode(): bool
    {
        return filter_var($_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG') ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    // Detects the upload extension from MIME type so file trust comes from server inspection instead of client filenames.
    private function detectUploadExtension(string $tmpPath, array $allowedMimeMap): ?string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = (string)$finfo->file($tmpPath);

        return $allowedMimeMap[$mimeType] ?? null;
    }

    // Verifies a target path matches the module prefixes so uploads cannot escape their allowed folder.
    private function isPathAllowedForModule(MediaModuleConfig $moduleConfig, string $publicPath): bool
    {
        foreach ($moduleConfig->allowedPrefixes as $prefix) {
            if (str_starts_with($publicPath, (string)$prefix)) {
                return true;
            }
        }

        return false;
    }

    // Splits a public path into directory, base name, and extension so upload targets can be rewritten safely.
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

    // Reuses the current path when the extension matches or rewrites it when the uploaded file changes type. Example: '/img/a.jpeg' + 'webp' -> '/img/a.webp'.
    private function resolveFinalPublicPath(string $currentPublicPath, array $pathParts, string $uploadExt): string
    {
        $normalizedTargetExt = $pathParts['extension'] === 'jpeg' ? 'jpg' : $pathParts['extension'];
        if ($normalizedTargetExt === $uploadExt) {
            return $currentPublicPath;
        }

        return rtrim($pathParts['directory'], '/') . '/' . $pathParts['base_name'] . '.' . $uploadExt;
    }

    // Maps raw PHP upload error codes to readable messages so clients get useful failure reasons.
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
