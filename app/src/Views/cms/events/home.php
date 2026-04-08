
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Home Page Content</h1>
        <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
    </div>

    <?php
    $successMessage = 'Home page content updated.';
    include __DIR__ . '/../../partialsViews/cms/form-feedback.php';
    ?>

    <form method="POST" action="/cms/events/home" class="card">
        <div class="card-body">
            <div class="accordion cms-accordion" id="homeAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="homeHeading1">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#homeCollapse1" aria-expanded="true" aria-controls="homeCollapse1">
                            Hero Section
                        </button>
                    </h2>
                    <div id="homeCollapse1" class="accordion-collapse collapse show" aria-labelledby="homeHeading1" data-bs-parent="#homeAccordion">
                        <div class="accordion-body">
                            <input type="hidden" name="sections[hero][title]" value="<?= htmlspecialchars($hero?->title ?? '') ?>">
                            <input type="hidden" name="sections[hero][subtitle]" value="<?= htmlspecialchars($hero?->subTitle ?? '') ?>">
                            <input type="hidden" name="sections[hero][description]" value="<?= htmlspecialchars($hero?->description ?? '') ?>">
                            <?php foreach ($hero?->items ?? [] as $index => $item): ?>
                                <div class="mb-3" data-home-upload-row="1" data-home-section-type="hero">
                                    <input type="hidden" name="items[hero][<?= (int)$index ?>][id]" class="home-item-id" value="<?= (int)$item->id ?>">
                                    <input type="hidden" name="items[hero][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                                    <input type="hidden" name="items[hero][<?= (int)$index ?>][image_path]" class="home-image-path" value="<?= htmlspecialchars($item->image ?? '') ?>">
                                    <label class="form-label">Foto</label>
                                    <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                                    <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                                    <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                                    <label class="form-label">Title</label>
                                    <input type="text" name="items[hero][<?= (int)$index ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title ?? '') ?>">
                                    <label class="form-label">Date</label>
                                    <input type="text" name="items[hero][<?= (int)$index ?>][content]" class="form-control mb-2" value="<?= htmlspecialchars($item->content ?? '') ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="homeHeading2">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#homeCollapse2" aria-expanded="false" aria-controls="homeCollapse2">
                            About Section
                        </button>
                    </h2>
                    <div id="homeCollapse2" class="accordion-collapse collapse" aria-labelledby="homeHeading2" data-bs-parent="#homeAccordion">
                        <div class="accordion-body">
                            <label class="form-label">Title</label>
                            <input type="text" name="sections[about][title]" class="form-control mb-2" value="<?= htmlspecialchars($about?->title ?? '') ?>">
                            <label class="form-label">SubTitle</label>
                            <input type="text" name="sections[about][subtitle]" class="form-control mb-2" value="<?= htmlspecialchars($about?->subTitle ?? '') ?>">
                            <label class="form-label">Description</label>
                            <input type="text" name="sections[about][description]" class="form-control mb-2" value="<?= htmlspecialchars($about?->description ?? '') ?>">
                            <div class="mb-3">
                                <label class="form-label">Labels</label>
                                <?php foreach ($about?->getItemsByCategorie('label') ?? [] as $index => $label): ?>
                                    <div class="d-flex gap-2 mb-2">
                                        <input type="hidden" name="items[about][<?= (int)$index ?>][id]" value="<?= (int)$label->id ?>">
                                        <input type="text" name="items[about][<?= (int)$index ?>][icon_class]" class="form-control" placeholder="Icon" value="<?= htmlspecialchars($label->icon ??  $label->subTitle ?? '') ?>">
                                        <input type="text" name="items[about][<?= (int)$index ?>][title]" class="form-control" placeholder="Title" value="<?= htmlspecialchars($label->title ?? '') ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="homeHeading3">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#homeCollapse3" aria-expanded="false" aria-controls="homeCollapse3">
                            Discover Events
                        </button>
                    </h2>
                    <div id="homeCollapse3" class="accordion-collapse collapse" aria-labelledby="homeHeading3" data-bs-parent="#homeAccordion">
                        <div class="accordion-body">
                            <label class="form-label">Section Title</label>
                            <input type="text" name="sections[discover_events][title]" class="form-control mb-2" value="<?= htmlspecialchars($discover?->title) ?>">
                            <?php foreach ($discover?->items ?? [] as $index => $item): ?>
                                <div class="mb-3" data-home-upload-row="1" data-home-section-type="discover_events">
                                    <input type="hidden" name="items[discover_events][<?= (int)$index ?>][id]" class="home-item-id" value="<?= (int)$item->id ?>">
                                    <input type="hidden" name="items[discover_events][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category) ?>">
                                    <input type="hidden" name="items[discover_events][<?= (int)$index ?>][image_path]" class="home-image-path" value="<?= htmlspecialchars($item->image) ?>">
                                    <input type="hidden" name="items[discover_events][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url) ?>">
                                    <label class="form-label">Foto</label>
                                    <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                                    <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                                    <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                                    <label class="form-label">Title</label>
                                    <input type="text" name="items[discover_events][<?= (int)$index ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title) ?>">
                                    <label class="form-label">Content</label>
                                    <textarea name="items[discover_events][<?= (int)$index ?>][content]" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->content) ?></textarea>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="homeHeading4">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#homeCollapse4" aria-expanded="false" aria-controls="homeCollapse4">
                            Map Section
                        </button>
                    </h2>
                    <div id="homeCollapse4" class="accordion-collapse collapse" aria-labelledby="homeHeading4" data-bs-parent="#homeAccordion">
                        <div class="accordion-body">
                            <label class="form-label">Section Title</label>
                            <input type="text" name="sections[map_section][title]" class="form-control mb-2" value="<?= htmlspecialchars($map?->title) ?>">
                            <?php foreach ($map?->items ?? [] as $index => $item): ?>
                                <div class="mb-3">
                                    <input type="hidden" name="items[map_section][<?= (int)$index ?>][id]" value="<?= (int)$item->id ?>">
                                    <label class="form-label">Location Title</label>
                                    <input type="text" name="items[map_section][<?= (int)$index ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title) ?>">
                                    <label class="form-label">Location Content</label>
                                    <input type="text" name="items[map_section][<?= (int)$index ?>][content]" class="form-control mb-2" value="<?= htmlspecialchars($item->content) ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="homeHeading5">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#homeCollapse5" aria-expanded="false" aria-controls="homeCollapse5">
                            FAQ Section
                        </button>
                    </h2>
                    <div id="homeCollapse5" class="accordion-collapse collapse" aria-labelledby="homeHeading5" data-bs-parent="#homeAccordion">
                        <div class="accordion-body">
                            <label class="form-label">Section Title</label>
                            <input type="text" name="sections[faq][title]" class="form-control mb-2" value="<?= htmlspecialchars($faq?->title) ?>">
                            <?php foreach ($faq?->items ?? [] as $index => $item): ?>
                                <div class="mb-3">
                                    <input type="hidden" name="items[faq][<?= (int)$index ?>][id]" value="<?= (int)$item->id ?>">
                                    <input type="hidden" name="items[faq][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? 'faq') ?>">
                                    <label class="form-label">Question</label>
                                    <input type="text" name="items[faq][<?= (int)$index ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title ?? '') ?>">
                                    <label class="form-label">Answer</label>
                                    <textarea name="items[faq][<?= (int)$index ?>][content]" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary save-btn">Save Changes</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../partialsViews/cms/upload-feedback-modal.php'; ?>

<script src="/js/cms/upload-feedback.js"></script>
<script src="/js/cms/media-upload.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<?php $tourDetailsJsVersion = @filemtime(__DIR__ . '/../../../../public/js/cms/tour-details.js') ?: time(); ?>
<script src="/js/cms/tour-details.js?v=<?= (int)$tourDetailsJsVersion ?>"></script>
<?php $homeJsVersion = @filemtime(__DIR__ . '/../../../../public/js/cms/home.js') ?: time(); ?>
<script src="/js/cms/home.js?v=<?= (int)$homeJsVersion ?>"></script>
