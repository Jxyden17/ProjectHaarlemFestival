<?php
$heroItems = $hero?->items ?? [];
$gridItems = $grid?->items ?? [];
$venueItems = $venues?->items ?? [];
$scheduleItems = $schedule?->items ?? [];
$exploreItems = $explore?->items ?? [];
$faqItems = $faq?->items ?? [];
?>

<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Stories Home Content</h1>
        <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
    </div>

    <?php
    $successMessage = 'Stories home content updated.';
    include __DIR__ . '/../../partialsViews/cms/form-feedback.php';
    ?>

    <form method="POST" action="/cms/events/stories-home" class="card" data-quill-form="1">
        <div class="card-body">
            <h2 class="h4">Hero</h2>
            <div class="mb-3">
                <label for="hero_title" class="form-label">Title</label>
                <textarea id="hero_title" name="sections[hero][title]" class="form-control" data-quill="1" rows="2" required><?= htmlspecialchars($hero?->title ?? '') ?></textarea>
                <input type="hidden" name="sections[hero][subtitle]" value="<?= htmlspecialchars($hero?->subTitle ?? '') ?>">
                <input type="hidden" name="sections[hero][description]" value="<?= htmlspecialchars($hero?->description ?? '') ?>">
            </div>

            <?php foreach ($heroItems as $index => $item): ?>
                <div class="mb-3 border rounded p-3">
                    <input type="hidden" name="items[hero][<?= (int)$index ?>][id]" value="<?= (int)($item->id ?? 0) ?>">
                    <input type="hidden" name="items[hero][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">

                    <label class="form-label">Hero Image <?= (int)$index + 1 ?> Alt Title</label>
                    <textarea name="items[hero][<?= (int)$index ?>][title]" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>

                    <label class="form-label">Hero Image <?= (int)$index + 1 ?> Description</label>
                    <textarea name="items[hero][<?= (int)$index ?>][content]" class="form-control mb-2" data-quill="1" rows="3"><?= htmlspecialchars($item->content ?? '') ?></textarea>

                    <label class="form-label">Hero Image <?= (int)$index + 1 ?> Path</label>
                    <input type="text" name="items[hero][<?= (int)$index ?>][image_path]" class="form-control mb-2" value="<?= htmlspecialchars($item->image ?? '') ?>">

                    <?php if (!empty($item->image)): ?>
                        <a href="<?= htmlspecialchars($item->image) ?>" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noreferrer">Open current image</a>
                    <?php endif; ?>

                    <input type="hidden" name="items[hero][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                    <input type="hidden" name="items[hero][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                    <input type="hidden" name="items[hero][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                    <input type="hidden" name="items[hero][<?= (int)$index ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                </div>
            <?php endforeach; ?>

            <h2 class="h4 mt-4">Info Grid</h2>
            <div class="mb-3">
                <label for="grid_title" class="form-label">Section Title</label>
                <textarea id="grid_title" name="sections[grid][title]" class="form-control" data-quill="1" rows="2"><?= htmlspecialchars($grid?->title ?? '') ?></textarea>
                <input type="hidden" name="sections[grid][subtitle]" value="<?= htmlspecialchars($grid?->subTitle ?? '') ?>">
                <input type="hidden" name="sections[grid][description]" value="<?= htmlspecialchars($grid?->description ?? '') ?>">
            </div>

            <?php foreach ($gridItems as $index => $item): ?>
                <div class="mb-3 border rounded p-3">
                    <input type="hidden" name="items[grid][<?= (int)$index ?>][id]" value="<?= (int)($item->id ?? 0) ?>">
                    <input type="hidden" name="items[grid][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">

                    <label class="form-label">Card Title</label>
                    <textarea name="items[grid][<?= (int)$index ?>][title]" class="form-control mb-2" data-quill="1" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>

                    <label class="form-label">Card Content</label>
                    <textarea name="items[grid][<?= (int)$index ?>][content]" class="form-control mb-2" data-quill="1" rows="3"><?= htmlspecialchars($item->content ?? '') ?></textarea>

                    <label class="form-label">Profile Link</label>
                    <input type="text" name="items[grid][<?= (int)$index ?>][link_url]" class="form-control mb-2" value="<?= htmlspecialchars($item->url ?? '') ?>">

                    <label class="form-label">Image Path</label>
                    <input type="text" name="items[grid][<?= (int)$index ?>][image_path]" class="form-control" value="<?= htmlspecialchars($item->image ?? '') ?>">

                    <input type="hidden" name="items[grid][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                    <input type="hidden" name="items[grid][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                    <input type="hidden" name="items[grid][<?= (int)$index ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                </div>
            <?php endforeach; ?>

            <h2 class="h4 mt-4">Venues</h2>
            <div class="mb-3">
                <label for="venues_title" class="form-label">Section Title</label>
                <textarea id="venues_title" name="sections[venues][title]" class="form-control" data-quill="1" rows="2"><?= htmlspecialchars($venues?->title ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="venues_subtitle" class="form-label">Section Subtitle</label>
                <textarea id="venues_subtitle" name="sections[venues][subtitle]" class="form-control" data-quill="1" rows="2"><?= htmlspecialchars($venues?->subTitle ?? '') ?></textarea>
                <input type="hidden" name="sections[venues][description]" value="<?= htmlspecialchars($venues?->description ?? '') ?>">
            </div>

            <?php foreach ($venueItems as $index => $item): ?>
                <div class="mb-3 border rounded p-3">
                    <input type="hidden" name="items[venues][<?= (int)$index ?>][id]" value="<?= (int)($item->id ?? 0) ?>">

                    <label class="form-label">Venue Name</label>
                    <textarea name="items[venues][<?= (int)$index ?>][title]" class="form-control mb-2" data-quill="1" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>

                    <label class="form-label">Address</label>
                    <textarea name="items[venues][<?= (int)$index ?>][item_subtitle]" class="form-control mb-2" data-quill="1" rows="2"><?= htmlspecialchars($item->subTitle ?? '') ?></textarea>

                    <label class="form-label">Description</label>
                    <textarea name="items[venues][<?= (int)$index ?>][content]" class="form-control mb-2" data-quill="1" rows="3"><?= htmlspecialchars($item->content ?? '') ?></textarea>

                    <label class="form-label">Tags (comma separated)</label>
                    <input type="text" name="items[venues][<?= (int)$index ?>][item_category]" class="form-control mb-2" value="<?= htmlspecialchars($item->category ?? '') ?>">

                    <input type="hidden" name="items[venues][<?= (int)$index ?>][image_path]" value="<?= htmlspecialchars($item->image ?? '') ?>">
                    <input type="hidden" name="items[venues][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                    <input type="hidden" name="items[venues][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                    <input type="hidden" name="items[venues][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                </div>
            <?php endforeach; ?>

            <h2 class="h4 mt-4">Schedule</h2>
            <div class="mb-3">
                <label for="schedule_title" class="form-label">Section Title</label>
                <textarea id="schedule_title" name="sections[schedule][title]" class="form-control" data-quill="1" rows="2"><?= htmlspecialchars($schedule?->title ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="schedule_subtitle" class="form-label">Section Subtitle</label>
                <textarea id="schedule_subtitle" name="sections[schedule][subtitle]" class="form-control" data-quill="1" rows="2"><?= htmlspecialchars($schedule?->subTitle ?? '') ?></textarea>
                <input type="hidden" name="sections[schedule][description]" value="<?= htmlspecialchars($schedule?->description ?? '') ?>">
            </div>

            <?php foreach ($scheduleItems as $index => $item): ?>
                <div class="mb-3 border rounded p-3">
                    <input type="hidden" name="items[schedule][<?= (int)$index ?>][id]" value="<?= (int)($item->id ?? 0) ?>">
                    <input type="hidden" name="items[schedule][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">

                    <label class="form-label">Event Title</label>
                    <textarea name="items[schedule][<?= (int)$index ?>][title]" class="form-control mb-2" data-quill="1" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>

                    <label class="form-label">Event Description</label>
                    <textarea name="items[schedule][<?= (int)$index ?>][content]" class="form-control mb-2" data-quill="1" rows="3"><?= htmlspecialchars($item->content ?? '') ?></textarea>

                    <label class="form-label">Thumbnail Path</label>
                    <input type="text" name="items[schedule][<?= (int)$index ?>][image_path]" class="form-control mb-2" value="<?= htmlspecialchars($item->image ?? '') ?>">

                    <input type="hidden" name="items[schedule][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                    <input type="hidden" name="items[schedule][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                    <input type="hidden" name="items[schedule][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                    <input type="hidden" name="items[schedule][<?= (int)$index ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                </div>
            <?php endforeach; ?>

            <h2 class="h4 mt-4">Explore</h2>
            <div class="mb-3">
                <label for="explore_title" class="form-label">Section Title</label>
                <textarea id="explore_title" name="sections[explore][title]" class="form-control" data-quill="1" rows="2"><?= htmlspecialchars($explore?->title ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="explore_subtitle" class="form-label">Section Subtitle</label>
                <textarea id="explore_subtitle" name="sections[explore][subtitle]" class="form-control" data-quill="1" rows="2"><?= htmlspecialchars($explore?->subTitle ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="explore_description" class="form-label">Footer Description</label>
                <textarea id="explore_description" name="sections[explore][description]" class="form-control" data-quill="1" rows="3"><?= htmlspecialchars($explore?->description ?? '') ?></textarea>
            </div>

            <?php foreach ($exploreItems as $index => $item): ?>
                <div class="mb-3 border rounded p-3">
                    <input type="hidden" name="items[explore][<?= (int)$index ?>][id]" value="<?= (int)($item->id ?? 0) ?>">
                    <input type="hidden" name="items[explore][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">

                    <label class="form-label">Card Title</label>
                    <textarea name="items[explore][<?= (int)$index ?>][title]" class="form-control mb-2" data-quill="1" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>

                    <label class="form-label">Card Subtitle</label>
                    <textarea name="items[explore][<?= (int)$index ?>][item_subtitle]" class="form-control mb-2" data-quill="1" rows="2"><?= htmlspecialchars($item->subTitle ?? '') ?></textarea>

                    <label class="form-label">Card Text</label>
                    <textarea name="items[explore][<?= (int)$index ?>][content]" class="form-control mb-2" data-quill="1" rows="3"><?= htmlspecialchars($item->content ?? '') ?></textarea>

                    <label class="form-label">Button Link</label>
                    <input type="text" name="items[explore][<?= (int)$index ?>][link_url]" class="form-control mb-2" value="<?= htmlspecialchars($item->url ?? '') ?>">

                    <label class="form-label">Image Path</label>
                    <input type="text" name="items[explore][<?= (int)$index ?>][image_path]" class="form-control mb-2" value="<?= htmlspecialchars($item->image ?? '') ?>">

                    <input type="hidden" name="items[explore][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                    <input type="hidden" name="items[explore][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                </div>
            <?php endforeach; ?>

            <h2 class="h4 mt-4">FAQ</h2>
            <div class="mb-3">
                <label for="faq_title" class="form-label">Section Title</label>
                <textarea id="faq_title" name="sections[faq][title]" class="form-control" data-quill="1" rows="2"><?= htmlspecialchars($faq?->title ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="faq_subtitle" class="form-label">Section Subtitle</label>
                <textarea id="faq_subtitle" name="sections[faq][subtitle]" class="form-control" data-quill="1" rows="2"><?= htmlspecialchars($faq?->subTitle ?? '') ?></textarea>
                <input type="hidden" name="sections[faq][description]" value="<?= htmlspecialchars($faq?->description ?? '') ?>">
            </div>

            <?php foreach ($faqItems as $index => $item): ?>
                <div class="mb-3 border rounded p-3">
                    <input type="hidden" name="items[faq][<?= (int)$index ?>][id]" value="<?= (int)($item->id ?? 0) ?>">
                    <input type="hidden" name="items[faq][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">

                    <label class="form-label">Question</label>
                    <textarea name="items[faq][<?= (int)$index ?>][title]" class="form-control mb-2" data-quill="1" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>

                    <label class="form-label">Answer</label>
                    <textarea name="items[faq][<?= (int)$index ?>][content]" class="form-control mb-2" data-quill="1" rows="3"><?= htmlspecialchars($item->content ?? '') ?></textarea>

                    <input type="hidden" name="items[faq][<?= (int)$index ?>][image_path]" value="<?= htmlspecialchars($item->image ?? '') ?>">
                    <input type="hidden" name="items[faq][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                    <input type="hidden" name="items[faq][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                    <input type="hidden" name="items[faq][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                    <input type="hidden" name="items[faq][<?= (int)$index ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<?php $pageEditorJsVersion = @filemtime(__DIR__ . '/../../../../public/js/cms/page-editor.js') ?: time(); ?>
<script src="/js/cms/page-editor.js?v=<?= (int)$pageEditorJsVersion ?>"></script>
