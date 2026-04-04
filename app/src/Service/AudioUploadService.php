<?php

namespace App\Service;

use App\Models\Media\MediaModuleConfig;
use App\Repository\Interfaces\IMediaRepository;
use App\Service\Interfaces\IAudioUploadService;
use App\Service\Interfaces\IDanceService;

class AudioUploadService extends MediaService implements IAudioUploadService
{
    private const ALLOWED_AUDIO_MIME_MAP = [
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
    private IDanceService $danceService;

    // Stores audio upload dependencies so dance-aware module resolution can reuse the shared media helpers.
    public function __construct(IMediaRepository $mediaRepository, IDanceService $danceService)
    {
        parent::__construct($mediaRepository);
        $this->danceService = $danceService;
    }

    // Uploads one audio file and syncs its public path so dance track editors can attach audio without manual filesystem work.
    public function uploadAudio(array $post, array $files): array
    {
        try {
            $moduleConfig = $this->parseAudioModuleConfig($post);
            $currentPath = trim((string)($post['current_path'] ?? ''));
            $file = $this->getUploadedFile($files, 'audio');
            $upload = $this->validateUploadedFile(
                $file,
                self::ALLOWED_AUDIO_MIME_MAP,
                'Only MP3, WAV, OGG, AAC, and M4A are allowed',
                'audio'
            );

            $sectionItemId = $this->findAudioSectionItemId($moduleConfig, $post, $currentPath);
            $paths = $this->buildAudioUploadTargetPaths(
                $moduleConfig,
                $currentPath,
                $upload['extension'],
                $sectionItemId
            );
            if ($paths === null) {
                throw new \RuntimeException('Target path is not allowed for this module', 400);
            }

            $this->moveUploadedFileToTarget($upload['tmp_path'], $paths['absolute_target']);
            $this->removeOldFileWhenExtensionChanged($paths);

            $dbSyncStatus = $this->syncAudioPathToDatabase($moduleConfig, $sectionItemId, $paths['public_path']);

            return $this->buildUploadSuccessResponse(
                $paths['public_path'],
                $this->buildUploadDebugContext($dbSyncStatus, $sectionItemId, $post, $currentPath, $moduleConfig)
            );
        } catch (\Throwable $e) {
            return $this->buildUploadErrorResponse($e, 'Audio upload failed');
        }
    }

    // Parses the requested audio module so later steps know which page, section, and category may be updated.
    private function parseAudioModuleConfig(array $post): MediaModuleConfig
    {
        $module = trim((string)($post['module'] ?? ''));
        $moduleConfig = $this->matchAudioModule($module);
        if ($moduleConfig === null) {
            throw new \RuntimeException('Invalid media module', 400);
        }

        return $moduleConfig;
    }

    // Resolves an audio module name so dynamic and static upload targets share one entry point. Example: module 'dance_detail_track_audio:urban-echo' -> MediaModuleConfig.
    private function matchAudioModule(string $module): ?MediaModuleConfig
    {
        $dynamicModule = $this->matchDynamicAudioModule($module);
        if ($dynamicModule !== null) {
            return $dynamicModule;
        }

        return $this->matchStaticAudioModule($module);
    }

    // Resolves dynamic audio modules so detail-page track uploads can target the correct dance slug.
    private function matchDynamicAudioModule(string $module): ?MediaModuleConfig
    {
        if (preg_match('/^dance_detail_track_audio:([a-z0-9-]+)$/', $module, $matches) === 1) {
            return $this->buildDanceDetailAudioModuleConfig($matches[1]);
        }

        if (preg_match('/^stories_detail_audio:([a-z0-9-]+)$/', $module, $matches) === 1) {
            return new MediaModuleConfig(
                ['/audio/stories/'],
                $matches[1],
                'featured',
                '',
                MediaModuleConfig::MATCH_BY_SECTION_AND_CATEGORY
            );
        }

        return null;
    }

    // Resolves static audio modules so future fixed audio targets can be added without changing the upload flow.
    private function matchStaticAudioModule(string $module): ?MediaModuleConfig
    {
        return match ($module) {
            default => null,
        };
    }

    // Builds the dance detail audio config so track uploads only write inside the selected detail page scope.
    private function buildDanceDetailAudioModuleConfig(string $pageSlug): ?MediaModuleConfig
    {
        $detailPage = $this->danceService->getDanceDetailPageBySlug($pageSlug);
        if ($detailPage === null) {
            return null;
        }

        return new MediaModuleConfig(
            ['/audio/dance/'],
            $detailPage->pageSlug,
            'dance_detail_tracks',
            'track',
            MediaModuleConfig::MATCH_BY_SECTION_AND_CATEGORY
        );
    }

    // Finds the section item to update so audio uploads can target either the posted item id or the current stored path.
    private function findAudioSectionItemId(MediaModuleConfig $moduleConfig, array $post, string $currentPath): ?int
    {
        $sectionItemId = $this->getPostedSectionItemId($post);
        if ($sectionItemId !== null) {
            return $sectionItemId;
        }

        if ($currentPath === '') {
            return null;
        }

        return $this->mediaRepository->findSectionItemIdByLinkUrl(
            $currentPath,
            $moduleConfig->pageSlug,
            $moduleConfig->sectionType,
            (string)$moduleConfig->itemCategory
        );
    }

    // Persists the new audio path so the uploaded file is reachable from the matching track item in the database.
    private function syncAudioPathToDatabase(MediaModuleConfig $moduleConfig, ?int $sectionItemId, string $publicPath): string
    {
        if ($sectionItemId === null || $sectionItemId <= 0) {
            return 'skipped_missing_section_item_id';
        }

        $updated = $this->mediaRepository->updateSectionItemLinkUrl(
            $sectionItemId,
            $publicPath,
            $moduleConfig->pageSlug,
            $moduleConfig->sectionType,
            (string)$moduleConfig->itemCategory
        );

        if (!$updated) {
            $message = 'Could not update audio link in database for this item';
            if ($this->isDebugMode()) {
                $message .= ' [section_item_id=' . $sectionItemId
                    . ', page_slug=' . $moduleConfig->pageSlug
                    . ', section_type=' . $moduleConfig->sectionType
                    . ', item_category=' . (string)($moduleConfig->itemCategory ?? '')
                    . ', path=' . $publicPath . ']';
            }

            throw new \RuntimeException($message, 404);
        }

        return 'updated';
    }
}
