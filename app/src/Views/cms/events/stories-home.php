<?php

use App\Models\ViewModels\Cms\Stories\StoriesHomeEditViewModel;
use App\Models\ViewModels\Cms\Stories\StoriesItemRowViewModel;
use App\Models\ViewModels\Cms\Stories\StoriesSectionEditViewModel;

$contentViewModel = (isset($contentViewModel) && $contentViewModel instanceof StoriesHomeEditViewModel)
    ? $contentViewModel
    : new StoriesHomeEditViewModel(
        'Stories Home Content',
        'stories',
        '/stories',
        new StoriesSectionEditViewModel('', '', ''),
        [],
        new StoriesSectionEditViewModel('', '', ''),
        [],
        new StoriesSectionEditViewModel('', '', ''),
        [],
        new StoriesSectionEditViewModel('', '', ''),
        [],
        new StoriesSectionEditViewModel('', '', ''),
        [],
        new StoriesSectionEditViewModel('', '', ''),
        []
    );

$hero = $contentViewModel->hero;
$heroItems = $contentViewModel->heroItems;
$grid = $contentViewModel->grid;
$gridItems = $contentViewModel->gridItems;
$venues = $contentViewModel->venues;
$venueItems = $contentViewModel->venueItems;
$schedule = $contentViewModel->schedule;
$scheduleItems = $contentViewModel->scheduleItems;
$explore = $contentViewModel->explore;
$exploreItems = $contentViewModel->exploreItems;
$faq = $contentViewModel->faq;
$faqItems = $contentViewModel->faqItems;
$pageSlug = $contentViewModel->pageSlug;

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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-1"><?= htmlspecialchars($contentViewModel->editorTitle) ?></h1>
            <p class="text-muted mb-0">Public page: <a href="<?= htmlspecialchars($contentViewModel->publicPath) ?>" target="_blank" rel="noreferrer"><?= htmlspecialchars($contentViewModel->publicPath) ?></a></p>
        </div>
        <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
    </div>

    <?php
    $successMessage = 'Stories home content updated.';
    include __DIR__ . '/../../partialsViews/cms/form-feedback.php';
    ?>

    <form method="POST" action="/cms/events/stories-home" class="card border-0 shadow-sm" data-stories-page-slug="<?= htmlspecialchars($pageSlug) ?>">
        <div class="card-body p-4">
            <div class="accordion cms-editor-accordion" id="storiesHomeAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#stories-hero-panel" aria-expanded="true" aria-controls="stories-hero-panel">
                            Hero
                        </button>
                    </h2>
                    <div id="stories-hero-panel" class="accordion-collapse collapse show" data-bs-parent="#storiesHomeAccordion">
                        <div class="accordion-body">
                            <div class="border rounded-3 p-3 cms-editor-field-card mb-3">
                                <label for="hero_title" class="form-label">Title</label>
                                <input type="hidden" name="sections[hero][title]" value="<?= htmlspecialchars($hero?->title ?? '') ?>">
                                <div id="hero_title" class="cms-readonly-title">
                                    <?= htmlspecialchars($hero?->title ?? '') ?>
                                </div>
                                <input type="hidden" name="sections[hero][subtitle]" value="<?= htmlspecialchars($hero?->subTitle ?? '') ?>">
                                <input type="hidden" name="sections[hero][description]" value="<?= htmlspecialchars($hero?->description ?? '') ?>">
                            </div>

                            <div class="row g-3">
                                <?php foreach ($heroItems as $index => $item): ?>
                                    <div class="col-12">
                                        <div class="border rounded-3 p-3 cms-editor-item-card" data-stories-upload-row="1" data-stories-section-type="hero">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h3 class="h6 mb-0">Hero Image <?= (int)$index + 1 ?></h3>
                                                <span class="text-muted small">Image and copy</span>
                                            </div>

                                            <input type="hidden" name="items[hero][<?= (int)$index ?>][id]" class="stories-item-id" value="<?= (int)($item->id ?? 0) ?>">
                                            <input type="hidden" name="items[hero][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                                            <input type="hidden" name="items[hero][<?= (int)$index ?>][image_path]" class="stories-image-path" value="<?= htmlspecialchars($item->image ?? '') ?>">

                                            <div class="mb-3">
                                                <label class="form-label">Alt Title</label>
                                                <textarea name="items[hero][<?= (int)$index ?>][title]" class="form-control" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea name="items[hero][<?= (int)$index ?>][content]" class="form-control" rows="3"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                                            </div>

                                            <div class="cms-upload-row">
                                                <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                                                <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                                                <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                                            </div>

                                            <?php $renderHiddenItemFields('items[hero][' . (int)$index . ']', $item, ['id', 'item_category', 'image_path', 'title', 'content']); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stories-grid-panel" aria-expanded="false" aria-controls="stories-grid-panel">
                            Info Grid
                        </button>
                    </h2>
                    <div id="stories-grid-panel" class="accordion-collapse collapse" data-bs-parent="#storiesHomeAccordion">
                        <div class="accordion-body">
                            <div class="border rounded-3 p-3 cms-editor-field-card mb-3">
                                <label for="grid_title" class="form-label">Section Title</label>
                                <textarea id="grid_title" name="sections[grid][title]" class="form-control" rows="2"><?= htmlspecialchars($grid?->title ?? '') ?></textarea>
                                <input type="hidden" name="sections[grid][subtitle]" value="<?= htmlspecialchars($grid?->subTitle ?? '') ?>">
                                <input type="hidden" name="sections[grid][description]" value="<?= htmlspecialchars($grid?->description ?? '') ?>">
                            </div>
                            <div class="row g-3">
                                <?php foreach ($gridItems as $index => $item): ?>
                                    <div class="col-12 col-xl-6">
                                        <div class="border rounded-3 p-3 cms-editor-item-card" data-stories-upload-row="1" data-stories-section-type="grid">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h3 class="h6 mb-0">Card <?= (int)$index + 1 ?></h3>
                                                <span class="text-muted small">Story teaser</span>
                                            </div>
                                            <input type="hidden" name="items[grid][<?= (int)$index ?>][id]" class="stories-item-id" value="<?= (int)($item->id ?? 0) ?>">
                                            <input type="hidden" name="items[grid][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                                            <input type="hidden" name="items[grid][<?= (int)$index ?>][image_path]" class="stories-image-path" value="<?= htmlspecialchars($item->image ?? '') ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Card Title</label>
                                                <textarea name="items[grid][<?= (int)$index ?>][title]" class="form-control" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Card Content</label>
                                                <textarea name="items[grid][<?= (int)$index ?>][content]" class="form-control" rows="3"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Profile Link</label>
                                                <input type="text" name="items[grid][<?= (int)$index ?>][link_url]" class="form-control" value="<?= htmlspecialchars($item->url ?? '') ?>">
                                            </div>
                                            <div class="cms-upload-row">
                                                <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                                                <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                                                <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                                            </div>
                                            <?php $renderHiddenItemFields('items[grid][' . (int)$index . ']', $item, ['id', 'item_category', 'image_path', 'title', 'content', 'link_url']); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stories-venues-panel" aria-expanded="false" aria-controls="stories-venues-panel">
                            Venues
                        </button>
                    </h2>
                    <div id="stories-venues-panel" class="accordion-collapse collapse" data-bs-parent="#storiesHomeAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-12 col-xl-6">
                                    <div class="border rounded-3 p-3 cms-editor-field-card h-100">
                                        <label for="venues_title" class="form-label">Section Title</label>
                                    <textarea id="venues_title" name="sections[venues][title]" class="form-control" rows="2"><?= htmlspecialchars($venues?->title ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-6">
                                    <div class="border rounded-3 p-3 cms-editor-field-card h-100">
                                        <label for="venues_subtitle" class="form-label">Section Subtitle</label>
                                        <textarea id="venues_subtitle" name="sections[venues][subtitle]" class="form-control" rows="2"><?= htmlspecialchars($venues?->subTitle ?? '') ?></textarea>
                                        <input type="hidden" name="sections[venues][description]" value="<?= htmlspecialchars($venues?->description ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <?php foreach ($venueItems as $index => $item): ?>
                                    <div class="col-12 col-xl-6">
                                        <div class="border rounded-3 p-3 cms-editor-item-card">
                                            <h3 class="h6 mb-3">Venue <?= (int)$index + 1 ?></h3>
                                            <input type="hidden" name="items[venues][<?= (int)$index ?>][id]" value="<?= (int)($item->id ?? 0) ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Venue Name</label>
                                                <textarea name="items[venues][<?= (int)$index ?>][title]" class="form-control" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Address</label>
                                                <textarea name="items[venues][<?= (int)$index ?>][item_subtitle]" class="form-control" rows="2"><?= htmlspecialchars($item->subTitle ?? '') ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea name="items[venues][<?= (int)$index ?>][content]" class="form-control" rows="3"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                                            </div>
                                            <div>
                                                <label class="form-label">Tags (comma separated)</label>
                                                <input type="text" name="items[venues][<?= (int)$index ?>][item_category]" class="form-control" value="<?= htmlspecialchars($item->category ?? '') ?>">
                                            </div>
                                            <input type="hidden" name="items[venues][<?= (int)$index ?>][image_path]" value="<?= htmlspecialchars($item->image ?? '') ?>">
                                            <input type="hidden" name="items[venues][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                                            <input type="hidden" name="items[venues][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                                            <input type="hidden" name="items[venues][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stories-schedule-panel" aria-expanded="false" aria-controls="stories-schedule-panel">
                            Schedule
                        </button>
                    </h2>
                    <div id="stories-schedule-panel" class="accordion-collapse collapse" data-bs-parent="#storiesHomeAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-12 col-xl-6">
                                    <div class="border rounded-3 p-3 cms-editor-field-card h-100">
                                        <label for="schedule_title" class="form-label">Section Title</label>
                                        <textarea id="schedule_title" name="sections[schedule][title]" class="form-control" rows="2"><?= htmlspecialchars($schedule?->title ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-6">
                                    <div class="border rounded-3 p-3 cms-editor-field-card h-100">
                                        <label for="schedule_subtitle" class="form-label">Section Subtitle</label>
                                        <textarea id="schedule_subtitle" name="sections[schedule][subtitle]" class="form-control" rows="2"><?= htmlspecialchars($schedule?->subTitle ?? '') ?></textarea>
                                        <input type="hidden" name="sections[schedule][description]" value="<?= htmlspecialchars($schedule?->description ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <?php foreach ($scheduleItems as $index => $item): ?>
                                    <div class="col-12">
                                        <div class="border rounded-3 p-3 cms-editor-item-card" data-stories-upload-row="1" data-stories-section-type="schedule">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h3 class="h6 mb-0">Schedule Card <?= (int)$index + 1 ?></h3>
                                                <span class="text-muted small">Highlight item</span>
                                            </div>
                                            <input type="hidden" name="items[schedule][<?= (int)$index ?>][id]" class="stories-item-id" value="<?= (int)($item->id ?? 0) ?>">
                                            <input type="hidden" name="items[schedule][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                                            <input type="hidden" name="items[schedule][<?= (int)$index ?>][image_path]" class="stories-image-path" value="<?= htmlspecialchars($item->image ?? '') ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Event Title</label>
                                                <textarea name="items[schedule][<?= (int)$index ?>][title]" class="form-control" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Event Description</label>
                                                <textarea name="items[schedule][<?= (int)$index ?>][content]" class="form-control" rows="3"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                                            </div>
                                            <div class="cms-upload-row">
                                                <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                                                <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                                                <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                                            </div>
                                            <input type="hidden" name="items[schedule][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                                            <input type="hidden" name="items[schedule][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                                            <input type="hidden" name="items[schedule][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                                            <input type="hidden" name="items[schedule][<?= (int)$index ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stories-explore-panel" aria-expanded="false" aria-controls="stories-explore-panel">
                            Explore
                        </button>
                    </h2>
                    <div id="stories-explore-panel" class="accordion-collapse collapse" data-bs-parent="#storiesHomeAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-12 col-xl-4">
                                    <div class="border rounded-3 p-3 cms-editor-field-card h-100">
                                        <label for="explore_title" class="form-label">Section Title</label>
                                        <textarea id="explore_title" name="sections[explore][title]" class="form-control" rows="2"><?= htmlspecialchars($explore?->title ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-4">
                                    <div class="border rounded-3 p-3 cms-editor-field-card h-100">
                                        <label for="explore_subtitle" class="form-label">Section Subtitle</label>
                                        <textarea id="explore_subtitle" name="sections[explore][subtitle]" class="form-control" rows="2"><?= htmlspecialchars($explore?->subTitle ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-4">
                                    <div class="border rounded-3 p-3 cms-editor-field-card h-100">
                                        <label for="explore_description" class="form-label">Footer Description</label>
                                        <textarea id="explore_description" name="sections[explore][description]" class="form-control" rows="3"><?= htmlspecialchars($explore?->description ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <?php foreach ($exploreItems as $index => $item): ?>
                                    <div class="col-12 col-xl-6">
                                        <div class="border rounded-3 p-3 cms-editor-item-card" data-stories-upload-row="1" data-stories-section-type="explore">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h3 class="h6 mb-0">Explore Card <?= (int)$index + 1 ?></h3>
                                                <span class="text-muted small">CTA card</span>
                                            </div>
                                            <input type="hidden" name="items[explore][<?= (int)$index ?>][id]" class="stories-item-id" value="<?= (int)($item->id ?? 0) ?>">
                                            <input type="hidden" name="items[explore][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                                            <input type="hidden" name="items[explore][<?= (int)$index ?>][image_path]" class="stories-image-path" value="<?= htmlspecialchars($item->image ?? '') ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Card Title</label>
                                                <textarea name="items[explore][<?= (int)$index ?>][title]" class="form-control" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Card Subtitle</label>
                                                <textarea name="items[explore][<?= (int)$index ?>][item_subtitle]" class="form-control" rows="2"><?= htmlspecialchars($item->subTitle ?? '') ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Card Text</label>
                                                <textarea name="items[explore][<?= (int)$index ?>][content]" class="form-control" rows="3"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Button Link</label>
                                                <input type="text" name="items[explore][<?= (int)$index ?>][link_url]" class="form-control" value="<?= htmlspecialchars($item->url ?? '') ?>">
                                            </div>
                                            <div class="cms-upload-row">
                                                <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                                                <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                                                <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                                            </div>
                                            <input type="hidden" name="items[explore][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                                            <input type="hidden" name="items[explore][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stories-faq-panel" aria-expanded="false" aria-controls="stories-faq-panel">
                            FAQ
                        </button>
                    </h2>
                    <div id="stories-faq-panel" class="accordion-collapse collapse" data-bs-parent="#storiesHomeAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-12 col-xl-6">
                                    <div class="border rounded-3 p-3 cms-editor-field-card h-100">
                                        <label for="faq_title" class="form-label">Section Title</label>
                                        <textarea id="faq_title" name="sections[faq][title]" class="form-control" rows="2"><?= htmlspecialchars($faq?->title ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-6">
                                    <div class="border rounded-3 p-3 cms-editor-field-card h-100">
                                        <label for="faq_subtitle" class="form-label">Section Subtitle</label>
                                        <textarea id="faq_subtitle" name="sections[faq][subtitle]" class="form-control" rows="2"><?= htmlspecialchars($faq?->subTitle ?? '') ?></textarea>
                                        <input type="hidden" name="sections[faq][description]" value="<?= htmlspecialchars($faq?->description ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <?php foreach ($faqItems as $index => $item): ?>
                                    <div class="col-12 col-xl-6">
                                        <div class="border rounded-3 p-3 cms-editor-item-card">
                                            <h3 class="h6 mb-3">Question <?= (int)$index + 1 ?></h3>
                                            <input type="hidden" name="items[faq][<?= (int)$index ?>][id]" value="<?= (int)($item->id ?? 0) ?>">
                                            <input type="hidden" name="items[faq][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Question</label>
                                                <textarea name="items[faq][<?= (int)$index ?>][title]" class="form-control" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>
                                            </div>
                                            <div>
                                                <label class="form-label">Answer</label>
                                                <textarea name="items[faq][<?= (int)$index ?>][content]" class="form-control" rows="3"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                                            </div>
                                            <?php $renderHiddenItemFields('items[faq][' . (int)$index . ']', $item, ['id', 'item_category', 'title', 'content']); ?>
                                        </div>
                                    </div>
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

<script src="/js/cms/upload-feedback.js"></script>
<script src="/js/cms/media-upload.js"></script>
<?php $storiesHomeJsVersion = @filemtime(__DIR__ . '/../../../../public/js/cms/stories-home.js') ?: time(); ?>
<script src="/js/cms/stories-home.js?v=<?= (int)$storiesHomeJsVersion ?>"></script>
