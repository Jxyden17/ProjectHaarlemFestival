<?php

namespace App\Service;

use App\Models\Media\MediaModuleConfig;
use App\Models\Media\MediaUploadRequest;
use App\Repository\Interfaces\IDanceRepository;
use App\Repository\Interfaces\IMediaRepository;
use App\Service\Interfaces\IMediaService;

class MediaService implements IMediaService
{
    private const TYPE_IMAGE = 'image';
    private const TYPE_AUDIO = 'audio';

    private IMediaRepository $mediaRepository;
    private IDanceRepository $danceRepository;

    public function __construct(IMediaRepository $mediaRepository, IDanceRepository $danceRepository)
    {
        $this->mediaRepository = $mediaRepository;
        $this->danceRepository = $danceRepository;
    }

    public function uploadReplace(array $server, array $post, array $files): array
    {
        return $this->handleUpload(self::TYPE_IMAGE, $server, $post, $files);
    }

    public function uploadAudio(array $server, array $post, array $files): array
    {
        return $this->handleUpload(self::TYPE_AUDIO, $server, $post, $files);
    }

    private function handleUpload(string $mediaType, array $server, array $post, array $files): array
    {
        try {
            $request = $this->buildUploadRequest($mediaType, $server, $post, $files);
            $resolved = $this->resolveTargetPaths(
                $mediaType,
                $request->moduleConfig,
                $request->currentPath,
                $request->extension,
                $request->sectionItemId
            );
            if ($resolved === null) {
                throw new \RuntimeException('Target path is not allowed for this module', 400);
            }

            $this->moveUploadedFileToTarget($request->tmpPath, $resolved['absolute_target']);
            $this->removeOldFileWhenExtensionChanged($resolved);
            $dbSyncStatus = $this->syncPathToDatabase($mediaType, $request, $resolved['public_path']);
            return $this->buildUploadSuccessResponse($request, $resolved['public_path'], $dbSyncStatus, $post);
        } catch (\RuntimeException $e) {
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
        } catch (\Throwable $e) {
            $message = ucfirst($mediaType) . ' upload failed';
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
    }

    private function buildUploadRequest(string $mediaType, array $server, array $post, array $files): MediaUploadRequest
    {
        if (($server['REQUEST_METHOD'] ?? '') !== 'POST') {
            throw new \RuntimeException('Method not allowed', 405);
        }

        $moduleConfig = $this->resolveRequestModuleConfig($post);
        $file = $this->extractUploadedFile($mediaType, $files);
        $currentPath = $this->resolveCurrentPath($post, $mediaType === self::TYPE_IMAGE);
        $uploadFileInfo = $this->validateAndExtractUploadFileInfo($mediaType, $file);
        $sectionItemId = $this->resolveSectionItemId(
            $moduleConfig,
            $post,
            $currentPath,
            $mediaType === self::TYPE_AUDIO
        );

        return new MediaUploadRequest(
            $moduleConfig,
            $currentPath,
            $uploadFileInfo['tmp_path'],
            $uploadFileInfo['extension'],
            $sectionItemId
        );
    }

    private function extractUploadedFile(string $mediaType, array $files): array
    {
        $fieldName = $mediaType === self::TYPE_AUDIO ? 'audio' : 'image';
        if (!isset($files[$fieldName]) || !is_array($files[$fieldName])) {
            throw new \RuntimeException('No ' . $fieldName . ' uploaded', 400);
        }

        return $files[$fieldName];
    }

    private function resolveRequestModuleConfig(array $post): MediaModuleConfig
    {
        $module = trim((string)($post['module'] ?? ''));
        $moduleConfig = $this->resolveModuleDefinition($module);
        if ($moduleConfig === null) {
            throw new \RuntimeException('Invalid media module', 400);
        }

        return $moduleConfig;
    }

    private function resolveSectionItemId(
        MediaModuleConfig $moduleConfig,
        array $post,
        string $currentPath,
        bool $resolveFromLinkUrl
    ): ?int
    {
        if (!$moduleConfig->supportsDatabaseSync()) {
            return null;
        }

        $sectionItemId = isset($post['section_item_id']) ? (int)$post['section_item_id'] : 0;
        if ($sectionItemId > 0) {
            return $sectionItemId;
        }

        if ($currentPath === '') {
            return null;
        }

        if ($resolveFromLinkUrl) {
            return $this->mediaRepository->findSectionItemIdByLinkUrl(
                $currentPath,
                (string)$moduleConfig->pageSlug,
                (string)$moduleConfig->sectionType,
                (string)$moduleConfig->itemCategory
            );
        }

        return $this->mediaRepository->findSectionItemIdByImagePath(
            $currentPath,
            (string)$moduleConfig->pageSlug,
            (string)$moduleConfig->sectionType,
            (string)$moduleConfig->itemCategory
        );
    }

    private function resolveCurrentPath(array $post, bool $required): string
    {
        $currentPath = trim((string)($post['current_path'] ?? ''));
        if ($required && $currentPath === '') {
            throw new \RuntimeException('No target image path provided', 400);
        }

        return $currentPath;
    }

    private function validateAndExtractUploadFileInfo(string $mediaType, array $file): array
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
        $maxSizeBytes = $this->maxUploadSizeByType($mediaType);
        if ($size <= 0 || $size > $maxSizeBytes) {
            throw new \RuntimeException(ucfirst($mediaType) . ' must be between 1 byte and ' . (int)($maxSizeBytes / 1024 / 1024) . 'MB', 400);
        }

        $extension = $this->detectUploadExtension($mediaType, $tmpPath);
        if ($extension === null) {
            throw new \RuntimeException($this->allowedExtensionMessage($mediaType), 400);
        }

        return [
            'tmp_path' => $tmpPath,
            'extension' => $extension,
        ];
    }

    private function maxUploadSizeByType(string $mediaType): int
    {
        return 10 * 1024 * 1024;
    }

    private function allowedExtensionMessage(string $mediaType): string
    {
        if ($mediaType === self::TYPE_AUDIO) {
            return 'Only MP3, WAV, OGG, AAC, and M4A are allowed';
        }

        return 'Only JPG, PNG, and WEBP are allowed';
    }

    private function detectUploadExtension(string $mediaType, string $tmpPath): ?string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = (string)$finfo->file($tmpPath);
        $allowed = $this->allowedMimeMapByType($mediaType);

        return $allowed[$mimeType] ?? null;
    }

    private function allowedMimeMapByType(string $mediaType): array
    {
        if ($mediaType === self::TYPE_AUDIO) {
            return [
                'audio/mpeg' => 'mp3',
                'audio/wav' => 'wav',
                'audio/x-wav' => 'wav',
                'audio/wave' => 'wav',
                'audio/ogg' => 'ogg',
                'audio/mp4' => 'm4a',
                'video/mp4' => 'm4a',
                'audio/x-m4a' => 'm4a',
                'audio/aac' => 'aac',
            ];
        }

        return [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
    }

    private function resolveModuleDefinition(string $module): ?MediaModuleConfig
    {
        if (preg_match('/^dance_detail_hero:([a-z0-9-]+)$/', $module, $matches) === 1) {
            return $this->buildDanceDetailModuleConfig($matches[1], 'dance_detail_hero', 'hero_image');
        }

        if (preg_match('/^dance_detail_track:([a-z0-9-]+)$/', $module, $matches) === 1) {
            return $this->buildDanceDetailModuleConfig($matches[1], 'dance_detail_tracks', 'track');
        }

        if (preg_match('/^dance_detail_track_audio:([a-z0-9-]+)$/', $module, $matches) === 1) {
            return $this->buildDanceDetailModuleConfig($matches[1], 'dance_detail_tracks', 'track', ['/audio/dance/']);
        }

        $map = [
            'dance_artist' => new MediaModuleConfig(
                ['/img/danceIMG/'],
                'dance-home',
                'dance_artists',
                'artist'
            ),
        ];

        return $map[$module] ?? null;
    }

    private function buildDanceDetailModuleConfig(
        string $cmsSlug,
        string $sectionType,
        string $itemCategory,
        array $allowedPrefixes = ['/img/danceIMG/']
    ): ?MediaModuleConfig
    {
        $detailPage = $this->danceRepository->findDetailPageByCmsSlug($cmsSlug);
        if ($detailPage === null) {
            return null;
        }

        return new MediaModuleConfig(
            $allowedPrefixes,
            $detailPage->pageSlug,
            $sectionType,
            $itemCategory
        );
    }

    private function syncPathToDatabase(string $mediaType, MediaUploadRequest $request, string $publicPath): string
    {
        if (!$request->moduleConfig->supportsDatabaseSync()) {
            return 'disabled_for_module';
        }

        if ($request->sectionItemId === null || $request->sectionItemId <= 0) {
            return 'skipped_missing_section_item_id';
        }

        if ($mediaType === self::TYPE_AUDIO) {
            $updated = $this->mediaRepository->updateSectionItemLinkUrl(
                $request->sectionItemId,
                $publicPath,
                (string)$request->moduleConfig->pageSlug,
                (string)$request->moduleConfig->sectionType,
                (string)$request->moduleConfig->itemCategory
            );
            $errorMessage = 'Could not update audio link in database for this item';
        } else {
            $updated = $this->mediaRepository->updateSectionItemImagePath(
                $request->sectionItemId,
                $publicPath,
                (string)$request->moduleConfig->pageSlug,
                (string)$request->moduleConfig->sectionType,
                (string)$request->moduleConfig->itemCategory
            );
            $errorMessage = 'Could not update image path in database for this item';
        }

        if (!$updated) {
            $message = $errorMessage;
            if ($this->isDebugMode()) {
                $message .= ' [section_item_id=' . $request->sectionItemId
                    . ', page_slug=' . (string)$request->moduleConfig->pageSlug
                    . ', section_type=' . (string)$request->moduleConfig->sectionType
                    . ', item_category=' . (string)$request->moduleConfig->itemCategory
                    . ', path=' . $publicPath . ']';
            }

            throw new \RuntimeException($message, 404);
        }

        return 'updated';
    }

    private function buildUploadSuccessResponse(MediaUploadRequest $request, string $publicPath, string $dbSyncStatus, array $post): array
    {
        $body = [
            'success' => true,
            'path' => $publicPath,
        ];

        if ($this->isDebugMode()) {
            $body['debug'] = [
                'db_sync' => $dbSyncStatus,
                'section_item_id' => $request->sectionItemId,
                'posted_section_item_id' => isset($post['section_item_id']) ? (string)$post['section_item_id'] : null,
                'current_path' => $request->currentPath,
                'page_slug' => $request->moduleConfig->pageSlug,
                'section_type' => $request->moduleConfig->sectionType,
                'item_category' => $request->moduleConfig->itemCategory,
            ];
        }

        return [
            'status_code' => 200,
            'body' => $body,
        ];
    }

    private function resolveTargetPaths(
        string $mediaType,
        MediaModuleConfig $moduleConfig,
        string $currentPublicPath,
        string $uploadExt,
        ?int $sectionItemId
    ): ?array
    {
        if ($mediaType === self::TYPE_AUDIO) {
            return $this->resolveAudioUploadTargetPaths($moduleConfig, $currentPublicPath, $uploadExt, $sectionItemId);
        }

        return $this->resolveUploadTargetPaths($moduleConfig, $currentPublicPath, $uploadExt, false);
    }

    private function resolveUploadTargetPaths(
        MediaModuleConfig $moduleConfig,
        string $publicPath,
        string $uploadExt,
        bool $autoCreateDir
    ): ?array
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

    private function resolveAudioUploadTargetPaths(
        MediaModuleConfig $moduleConfig,
        string $currentPublicPath,
        string $uploadExt,
        ?int $sectionItemId
    ): ?array {
        $publicPath = $currentPublicPath;
        if ($publicPath === '') {
            $primaryPrefix = $moduleConfig->allowedPrefixes[0] ?? '';
            if ($primaryPrefix === '' || $sectionItemId === null || $sectionItemId <= 0) {
                return null;
            }

            $publicPath = rtrim((string)$primaryPrefix, '/') . '/track-' . $sectionItemId . '.' . $uploadExt;
        }

        return $this->resolveUploadTargetPaths($moduleConfig, $publicPath, $uploadExt, true);
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

    private function moveUploadedFileToTarget(string $tmpPath, string $absoluteTarget): void
    {
        if (!move_uploaded_file($tmpPath, $absoluteTarget)) {
            $debugContext = $this->isDebugMode()
                ? ' tmp=' . $tmpPath . ' target=' . $absoluteTarget
                : '';
            throw new \RuntimeException('Failed to save uploaded file.' . $debugContext, 500);
        }
    }

    private function removeOldFileWhenExtensionChanged(array $resolved): void
    {
        if ($resolved['public_path'] !== $resolved['current_public_path'] && is_file($resolved['absolute_current'])) {
            @unlink($resolved['absolute_current']);
        }
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

    private function isDebugMode(): bool
    {
        return filter_var($_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG') ?? false, FILTER_VALIDATE_BOOLEAN);
    }
}
