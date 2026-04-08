<?php

use App\Models\ViewModels\Cms\Stories\StoriesDetailEditViewModel;
use App\Models\ViewModels\Cms\Stories\StoriesItemRowViewModel;
use App\Models\ViewModels\Cms\Stories\StoriesSectionEditViewModel;

$contentViewModel = (isset($contentViewModel) && $contentViewModel instanceof StoriesDetailEditViewModel)
    ? $contentViewModel
    : new StoriesDetailEditViewModel(
        0,
        'Stories Detail Content',
        '',
        '',
        new StoriesSectionEditViewModel('', '', ''),
        [],
        [],
        new StoriesSectionEditViewModel('', '', ''),
        [],
        new StoriesSectionEditViewModel('', '', ''),
        [],
        new StoriesSectionEditViewModel('', '', ''),
        [],
        new StoriesSectionEditViewModel('', '', ''),
        [],
        [],
        [],
        [],
        [],
        []
    );

$hero = $contentViewModel->hero;
$heroImageItems = $contentViewModel->heroImageItems;
$heroTagItems = $contentViewModel->heroTagItems;
$about = $contentViewModel->about;
$aboutItems = $contentViewModel->aboutItems;
$gallery = $contentViewModel->gallery;
$galleryItems = $contentViewModel->galleryItems;
$featured = $contentViewModel->featured;
$featuredItems = $contentViewModel->featuredItems;
$booking = $contentViewModel->booking;
$bookingButtonItems = $contentViewModel->bookingButtonItems;
$bookingPriceItems = $contentViewModel->bookingPriceItems;
$bookingPriceLabelItems = $contentViewModel->bookingPriceLabelItems;
$bookingDateItems = $contentViewModel->bookingDateItems;
$bookingLocationItems = $contentViewModel->bookingLocationItems;
$bookingTagItems = $contentViewModel->bookingTagItems;
$pageSlug = trim($contentViewModel->pageSlug);
$pageId = $contentViewModel->pageId;

$renderHiddenItemFields = static function (string $prefix, StoriesItemRowViewModel $item, array $skip = []): void {
    $fields = [
        'id' => (string) $item->id,
        'item_category' => $item->category,
        'title' => $item->title,
        'item_subtitle' => $item->subTitle,
        'content' => $item->content,
        'image_path' => $item->image,
        'link_url' => $item->url,
        'duration' => $item->duration,
        'icon_class' => $item->icon,
    ];

    foreach ($skip as $fieldName) {
        unset($fields[$fieldName]);
    }

    foreach ($fields as $fieldName => $fieldValue) {
        echo '<input type="hidden" name="' . htmlspecialchars($prefix . '[' . $fieldName . ']') . '" value="' . htmlspecialchars($fieldValue) . '">';
    }
};
?>

<div class="container-lg py-4 py-md-5">
    <section class="cms-page-hero mb-3">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
            <div>
                <h1 class="cms-page-hero__title"><?= htmlspecialchars($contentViewModel->editorTitle) ?></h1>
            </div>
            <div class="d-flex align-items-center gap-2">
            <form method="POST" action="/cms/events/stories/delete" onsubmit="return confirm('Are you sure you want to delete this Stories subpage? This action cannot be undone.');" class="m-0">
                <input type="hidden" name="page_id" value="<?= (int)$pageId ?>">
                <button type="submit" class="btn btn-outline-danger">Delete Subpage</button>
            </form>
            <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
            </div>
        </div>
    </section>

    <?php
    $successMessage = 'Stories detail content updated.';
    include __DIR__ . '/../../partialsViews/cms/form-feedback.php';
    ?>

    <form method="POST" action="/cms/events/stories-details" class="card border-0 shadow-sm cms-editor-form cms-stories-editor" data-stories-page-slug="<?= htmlspecialchars($pageSlug) ?>" data-save-api="/cms/events/stories-details/updateAPI">
        <div class="card-body p-4">
            <input type="hidden" name="page_id" value="<?= (int)$pageId ?>">
            <div class="mb-4">
                <label for="stories_page_title" class="form-label">Page Title</label>
                <input id="stories_page_title" type="text" name="sections[page_title][title]" class="form-control" value="<?= htmlspecialchars($contentViewModel->editorTitle) ?>">
            </div>

            <div class="accordion cms-editor-accordion" id="storiesDetailAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#stories-detail-hero-panel" aria-expanded="true" aria-controls="stories-detail-hero-panel">
                            Hero
                        </button>
                    </h2>
                    <div id="stories-detail-hero-panel" class="accordion-collapse collapse show" data-bs-parent="#storiesDetailAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-12 col-xl-6">
                                    <div class="h-100">
                                        <label class="form-label">Subtitle</label>
                                        <textarea name="sections[hero][subtitle]" class="form-control" rows="3"><?= htmlspecialchars($hero?->subTitle ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-6">
                                    <div class="h-100">
                                        <label class="form-label">Description</label>
                                        <textarea name="sections[hero][description]" class="form-control" rows="4"><?= htmlspecialchars($hero?->description ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mt-1">
                                <?php foreach ($heroImageItems as $index => $item): ?>
                                    <div class="col-12">
                                        <div class="border rounded-3 p-3 cms-editor-item-card" data-stories-upload-row="1" data-stories-section-type="hero">
                                            <h3 class="h6 mb-3">Hero Image <?= (int)$index + 1 ?></h3>
                                            <input type="hidden" name="items[hero][<?= (int)$index ?>][id]" class="stories-item-id" value="<?= (int)$item->id ?>">
                                            <input type="hidden" name="items[hero][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                                            <input type="hidden" name="items[hero][<?= (int)$index ?>][image_path]" class="stories-image-path" value="<?= htmlspecialchars($item->image ?? '') ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Image Title</label>
                                                <input type="text" name="items[hero][<?= (int)$index ?>][title]" class="form-control" value="<?= htmlspecialchars($item->title ?? '') ?>">
                                            </div>
                                            <div class="cms-upload-row">
                                                <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                                                <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                                                <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                                            </div>
                                            <?php $renderHiddenItemFields('items[hero][' . (int)$index . ']', $item, ['id', 'item_category', 'image_path', 'title']); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <?php foreach ($heroTagItems as $index => $item): ?>
                                    <?php $rowIndex = $index + 100; ?>
                                    <div class="col-12 col-xl-6">
                                        <div class="border rounded-3 p-3 cms-editor-item-card">
                                            <h3 class="h6 mb-3">Hero Tag <?= (int)$index + 1 ?></h3>
                                            <input type="hidden" name="items[hero][<?= (int)$rowIndex ?>][id]" value="<?= (int)$item->id ?>">
                                            <input type="hidden" name="items[hero][<?= (int)$rowIndex ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                                            <input type="text" name="items[hero][<?= (int)$rowIndex ?>][title]" class="form-control" value="<?= htmlspecialchars($item->title ?? '') ?>">
                                            <?php $renderHiddenItemFields('items[hero][' . (int)$rowIndex . ']', $item, ['id', 'item_category', 'title']); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stories-detail-about-panel" aria-expanded="false" aria-controls="stories-detail-about-panel">
                            About
                        </button>
                    </h2>
                    <div id="stories-detail-about-panel" class="accordion-collapse collapse" data-bs-parent="#storiesDetailAccordion">
                        <div class="accordion-body">
                            <div class="mb-3">
                                <label class="form-label">Section Title</label>
                                <textarea name="sections[about][title]" class="form-control" rows="2"><?= htmlspecialchars($about?->title ?? '') ?></textarea>
                                <input type="hidden" name="sections[about][subtitle]" value="<?= htmlspecialchars($about?->subTitle ?? '') ?>">
                                <input type="hidden" name="sections[about][description]" value="<?= htmlspecialchars($about?->description ?? '') ?>">
                            </div>

                            <div class="row g-3">
                                <?php foreach ($aboutItems as $index => $item): ?>
                                    <div class="col-12">
                                        <div class="border rounded-3 p-3 cms-editor-item-card">
                                            <h3 class="h6 mb-3">Paragraph <?= (int)$index + 1 ?></h3>
                                            <input type="hidden" name="items[about][<?= (int)$index ?>][id]" value="<?= (int)$item->id ?>">
                                            <input type="hidden" name="items[about][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                                            <textarea name="items[about][<?= (int)$index ?>][content]" class="form-control" rows="5"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                                            <?php $renderHiddenItemFields('items[about][' . (int)$index . ']', $item, ['id', 'item_category', 'content']); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stories-detail-gallery-panel" aria-expanded="false" aria-controls="stories-detail-gallery-panel">
                            Gallery
                        </button>
                    </h2>
                    <div id="stories-detail-gallery-panel" class="accordion-collapse collapse" data-bs-parent="#storiesDetailAccordion">
                        <div class="accordion-body">
                            <div class="mb-3">
                                <label class="form-label">Section Title</label>
                                <textarea name="sections[gallery][title]" class="form-control" rows="2"><?= htmlspecialchars($gallery?->title ?? '') ?></textarea>
                                <input type="hidden" name="sections[gallery][subtitle]" value="<?= htmlspecialchars($gallery?->subTitle ?? '') ?>">
                                <input type="hidden" name="sections[gallery][description]" value="<?= htmlspecialchars($gallery?->description ?? '') ?>">
                            </div>

                            <div class="row g-3">
                                <?php foreach ($galleryItems as $index => $item): ?>
                                    <div class="col-12 col-xl-6">
                                        <div class="border rounded-3 p-3 cms-editor-item-card" data-stories-upload-row="1" data-stories-section-type="gallery">
                                            <h3 class="h6 mb-3">Gallery Image <?= (int)$index + 1 ?></h3>
                                            <input type="hidden" name="items[gallery][<?= (int)$index ?>][id]" class="stories-item-id" value="<?= (int)$item->id ?>">
                                            <input type="hidden" name="items[gallery][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                                            <input type="hidden" name="items[gallery][<?= (int)$index ?>][image_path]" class="stories-image-path" value="<?= htmlspecialchars($item->image ?? '') ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Image Label</label>
                                                <input type="text" name="items[gallery][<?= (int)$index ?>][title]" class="form-control" value="<?= htmlspecialchars($item->title ?? '') ?>">
                                            </div>
                                            <div class="cms-upload-row">
                                                <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                                                <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                                                <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                                            </div>
                                            <input type="hidden" name="items[gallery][<?= (int)$index ?>][content]" value="<?= htmlspecialchars($item->content ?? '') ?>">
                                            <input type="hidden" name="items[gallery][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                                            <input type="hidden" name="items[gallery][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                                            <input type="hidden" name="items[gallery][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                                            <input type="hidden" name="items[gallery][<?= (int)$index ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stories-detail-featured-panel" aria-expanded="false" aria-controls="stories-detail-featured-panel">
                            Featured
                        </button>
                    </h2>
                    <div id="stories-detail-featured-panel" class="accordion-collapse collapse" data-bs-parent="#storiesDetailAccordion">
                        <div class="accordion-body">
                            <div class="mb-3">
                                <label class="form-label">Section Title</label>
                                <textarea name="sections[featured][title]" class="form-control" rows="2"><?= htmlspecialchars($featured?->title ?? '') ?></textarea>
                                <input type="hidden" name="sections[featured][subtitle]" value="<?= htmlspecialchars($featured?->subTitle ?? '') ?>">
                                <input type="hidden" name="sections[featured][description]" value="<?= htmlspecialchars($featured?->description ?? '') ?>">
                            </div>

                            <div class="row g-3">
                                <?php foreach ($featuredItems as $index => $item): ?>
                                    <div class="col-12 col-xl-6">
                                        <div class="border rounded-3 p-3 cms-editor-item-card" data-stories-audio-upload-row="1" data-stories-audio-section-type="featured">
                                            <h3 class="h6 mb-3">Feature <?= (int)$index + 1 ?></h3>
                                            <input type="hidden" name="items[featured][<?= (int)$index ?>][id]" class="stories-item-id" value="<?= (int)$item->id ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Card Title</label>
                                                <input type="text" name="items[featured][<?= (int)$index ?>][title]" class="form-control" value="<?= htmlspecialchars($item->title ?? '') ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea name="items[featured][<?= (int)$index ?>][content]" class="form-control" rows="4"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Button Label</label>
                                                <input type="text" name="items[featured][<?= (int)$index ?>][item_category]" class="form-control" value="<?= htmlspecialchars($item->category ?? '') ?>" placeholder="Listen">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Audio Link</label>
                                                <input type="text" name="items[featured][<?= (int)$index ?>][link_url]" class="form-control stories-audio-path" value="<?= htmlspecialchars($item->url ?? '') ?>">
                                            </div>
                                            <div class="cms-upload-row">
                                                <input type="file" class="form-control form-control-sm performer-upload-audio-input" accept="audio/mpeg,audio/wav,audio/ogg,audio/mp4,audio/x-m4a,audio/aac">
                                                <button type="button" class="btn btn-sm btn-outline-primary upload-performer-audio">Upload</button>
                                                <a href="<?= htmlspecialchars($item->url ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-audio-download-link<?= ($item->url ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                                            </div>
                                            <input type="hidden" name="items[featured][<?= (int)$index ?>][image_path]" value="<?= htmlspecialchars($item->image ?? '') ?>">
                                            <input type="hidden" name="items[featured][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                                            <input type="hidden" name="items[featured][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                                            <input type="hidden" name="items[featured][<?= (int)$index ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stories-detail-booking-panel" aria-expanded="false" aria-controls="stories-detail-booking-panel">
                            Booking
                        </button>
                    </h2>
                    <div id="stories-detail-booking-panel" class="accordion-collapse collapse" data-bs-parent="#storiesDetailAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-12 col-xl-4">
                                    <div class="h-100">
                                        <label class="form-label">Section Title</label>
                                        <textarea name="sections[booking][title]" class="form-control" rows="2"><?= htmlspecialchars($booking?->title ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-4">
                                    <div class="h-100">
                                        <label class="form-label">Section Subtitle</label>
                                        <textarea name="sections[booking][subtitle]" class="form-control" rows="2"><?= htmlspecialchars($booking?->subTitle ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-4">
                                    <div class="h-100">
                                        <label class="form-label">Section Description</label>
                                        <textarea name="sections[booking][description]" class="form-control" rows="3"><?= htmlspecialchars($booking?->description ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mt-1">
                                <?php foreach ([$bookingPriceItems, $bookingPriceLabelItems, $bookingDateItems, $bookingLocationItems, $bookingButtonItems, $bookingTagItems] as $group): ?>
                                    <?php foreach ($group as $item): ?>
                                        <div class="col-12 col-xl-6">
                                            <div class="border rounded-3 p-3 cms-editor-item-card">
                                                <input type="hidden" name="items[booking][<?= (int)$item->id ?>][id]" value="<?= (int)$item->id ?>">
                                                <input type="hidden" name="items[booking][<?= (int)$item->id ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                                                <h3 class="h6 mb-3"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $item->category ?? 'item'))) ?></h3>
                                                <div class="mb-3">
                                                    <label class="form-label">Title</label>
                                                    <input type="text" name="items[booking][<?= (int)$item->id ?>][title]" class="form-control" value="<?= htmlspecialchars($item->title ?? '') ?>">
                                                </div>
                                                <div>
                                                    <label class="form-label">Link / Extra Value</label>
                                                    <input type="text" name="items[booking][<?= (int)$item->id ?>][link_url]" class="form-control" value="<?= htmlspecialchars($item->url ?? '') ?>">
                                                </div>
                                                <input type="hidden" name="items[booking][<?= (int)$item->id ?>][content]" value="<?= htmlspecialchars($item->content ?? '') ?>">
                                                <input type="hidden" name="items[booking][<?= (int)$item->id ?>][image_path]" value="<?= htmlspecialchars($item->image ?? '') ?>">
                                                <input type="hidden" name="items[booking][<?= (int)$item->id ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                                                <input type="hidden" name="items[booking][<?= (int)$item->id ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                                                <input type="hidden" name="items[booking][<?= (int)$item->id ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-primary save-btn">Save Content</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../partialsViews/cms/upload-feedback-modal.php'; ?>

<?php $storiesDetailJsVersion = @filemtime(__DIR__ . '/../../../../public/js/cms/stories-detail.js') ?: time(); ?>
<script src="/js/cms/upload-feedback.js"></script>
<script src="/js/cms/media-upload.js"></script>
<script src="/js/cms/form-save-api.js"></script>
<script src="/js/cms/stories-detail.js?v=<?= (int)$storiesDetailJsVersion ?>"></script>
