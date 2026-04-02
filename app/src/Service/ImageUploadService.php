<?php

namespace App\Service;

use App\Models\Media\MediaModuleConfig;
use App\Repository\Interfaces\IMediaRepository;
use App\Service\Interfaces\IDanceService;
use App\Service\Interfaces\IImageUploadService;

class ImageUploadService extends MediaService implements IImageUploadService
{
    private const ALLOWED_IMAGE_MIME_MAP = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    private IDanceService $danceService;

    // Stores image upload dependencies so dance-aware module resolution can reuse the shared media helpers.
    public function __construct(IMediaRepository $mediaRepository, IDanceService $danceService)
    {
        parent::__construct($mediaRepository);
        $this->danceService = $danceService;
    }

    // Uploads one image file and syncs its public path so CMS image fields update storage and database together.
    public function uploadImage(array $post, array $files): array
    {
        try {
            $moduleConfig = $this->parseImageModuleConfig($post);
            $currentPath = $this->requireCurrentImagePath($post);
            $file = $this->getUploadedFile($files, 'image');
            $upload = $this->validateUploadedFile(
                $file,
                self::ALLOWED_IMAGE_MIME_MAP,
                'Only JPG, PNG, and WEBP are allowed',
                'image'
            );

            $sectionItemId = $this->findImageSectionItemId($moduleConfig, $post, $currentPath);
            $currentPath = $this->loadStoredImagePath($currentPath, $sectionItemId);
            $paths = $this->buildUploadTargetPaths($moduleConfig, $currentPath, $upload['extension'], false);
            if ($paths === null) {
                throw new \RuntimeException('Target path is not allowed for this module', 400);
            }

            $this->moveUploadedFileToTarget($upload['tmp_path'], $paths['absolute_target']);
            $this->removeOldFileWhenExtensionChanged($paths);

            $dbSyncStatus = $this->syncImagePathToDatabase($moduleConfig, $sectionItemId, $paths['public_path']);

            return $this->buildUploadSuccessResponse(
                $paths['public_path'],
                $this->buildUploadDebugContext($dbSyncStatus, $sectionItemId, $post, $currentPath, $moduleConfig)
            );
        } catch (\Throwable $e) {
            return $this->buildUploadErrorResponse($e, 'Image upload failed');
        }
    }

    // Parses the requested image module so later steps know which page, section, and category may be updated.
    private function parseImageModuleConfig(array $post): MediaModuleConfig
    {
        $module = trim((string)($post['module'] ?? ''));
        $moduleConfig = $this->matchImageModule($module);
        if ($moduleConfig === null) {
            throw new \RuntimeException('Invalid media module', 400);
        }

        return $moduleConfig;
    }

    // Resolves an image module name so dynamic and static upload targets share one entry point. Example: module 'dance_detail_hero:urban-echo' -> MediaModuleConfig.
    private function matchImageModule(string $module): ?MediaModuleConfig
    {
        $dynamicModule = $this->matchDynamicImageModule($module);
        if ($dynamicModule !== null) {
            return $dynamicModule;
        }

        return $this->matchStaticImageModule($module);
    }

    // Resolves dynamic image modules so slug-based dance and tour uploads point at the correct page scope.
    private function matchDynamicImageModule(string $module): ?MediaModuleConfig
    {
        if (preg_match('/^dance_detail_hero:([a-z0-9-]+)$/', $module, $matches) === 1) {
            return $this->buildDanceDetailImageModuleConfig($matches[1], 'dance_detail_hero', 'hero_image');
        }

        if (preg_match('/^dance_detail_track:([a-z0-9-]+)$/', $module, $matches) === 1) {
            return $this->buildDanceDetailImageModuleConfig($matches[1], 'dance_detail_tracks', 'track');
        }

        if (preg_match('/^tour_image:([a-z0-9-]+):([a-z_]+)$/', $module, $matches) === 1) {
            return new MediaModuleConfig(
                ['/img/historyIMG/'],
                $matches[1],
                $matches[2],
                null,
                MediaModuleConfig::MATCH_BY_SECTION
            );
        }

        return null;
    }

    // Resolves static image modules so fixed CMS image targets can reuse the same upload flow.
    private function matchStaticImageModule(string $module): ?MediaModuleConfig
    {
        return match ($module) {
            'dance_artist' => new MediaModuleConfig(
                ['/img/danceIMG/'],
                'dance-home',
                'dance_artists',
                'artist',
                MediaModuleConfig::MATCH_BY_SECTION_AND_CATEGORY
            ),
            'tour' => new MediaModuleConfig(
                ['/img/historyIMG/'],
                'tour-home',
                'tour_items',
                'tour',
                MediaModuleConfig::MATCH_BY_SECTION_AND_CATEGORY
            ),
            default => null,
        };
    }

    // Requires a current image path so image uploads know which existing asset location they are replacing.
    private function requireCurrentImagePath(array $post): string
    {
        $currentPath = trim((string)($post['current_path'] ?? ''));
        if ($currentPath === '') {
            throw new \RuntimeException('No target image path provided', 400);
        }

        return $currentPath;
    }

    // Builds the dance detail image config so hero and track image uploads stay inside the selected detail page scope.
    private function buildDanceDetailImageModuleConfig(string $pageSlug, string $sectionType, string $itemCategory): ?MediaModuleConfig
    {
        $detailPage = $this->danceService->getDanceDetailPageBySlug($pageSlug);
        if ($detailPage === null) {
            return null;
        }

        return new MediaModuleConfig(
            ['/img/danceIMG/'],
            $detailPage->pageSlug,
            $sectionType,
            $itemCategory,
            MediaModuleConfig::MATCH_BY_SECTION_AND_CATEGORY
        );
    }

    // Finds the section item to update so image uploads can target either a posted item id or the current stored path.
    private function findImageSectionItemId(MediaModuleConfig $moduleConfig, array $post, string $currentPath): ?int
    {
        $sectionItemId = $this->getPostedSectionItemId($post);
        if ($sectionItemId !== null) {
            return $sectionItemId;
        }

        if ($currentPath === '') {
            return null;
        }

        if ($moduleConfig->matchesBySection()) {
            return $sectionItemId;
        }

        return $this->mediaRepository->findSectionItemIdByImagePath(
            $currentPath,
            $moduleConfig->pageSlug,
            $moduleConfig->sectionType,
            (string)$moduleConfig->itemCategory
        );
    }

    // Reloads the stored image path so extension swaps use the real current file instead of stale client input.
    private function loadStoredImagePath(string $currentPath, ?int $sectionItemId): string
    {
        if ($sectionItemId === null || $sectionItemId <= 0) {
            return $currentPath;
        }

        $storedCurrentPath = $this->mediaRepository->findSectionItemImagePathById($sectionItemId);
        if ($storedCurrentPath === null || $storedCurrentPath === '') {
            return $currentPath;
        }

        return $storedCurrentPath;
    }

    // Persists the new image path so the uploaded file is reachable from the matching item or section in the database.
    private function syncImagePathToDatabase(MediaModuleConfig $moduleConfig, ?int $sectionItemId, string $publicPath): string
    {
        if ($sectionItemId === null || $sectionItemId <= 0) {
            return 'skipped_missing_section_item_id';
        }

        if ($moduleConfig->matchesBySection()) {
            $updated = $this->mediaRepository->updateSectionItemImagePathBySection(
                $sectionItemId,
                $publicPath,
                $moduleConfig->pageSlug,
                $moduleConfig->sectionType
            );
        } elseif ($moduleConfig->matchesBySectionAndCategory()) {
            $updated = $this->mediaRepository->updateSectionItemImagePath(
                $sectionItemId,
                $publicPath,
                $moduleConfig->pageSlug,
                $moduleConfig->sectionType,
                (string)$moduleConfig->itemCategory
            );
        } else {
            throw new \RuntimeException('Unsupported media database target', 500);
        }

        if (!$updated) {
            $message = 'Could not update image path in database for this item';
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
