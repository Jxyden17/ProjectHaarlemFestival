<?php
use App\Models\Event\EventDetailPageModel;

$danceDetailPages = is_array($danceDetailPages ?? null) ? $danceDetailPages : [];
$storyDetailPages = is_array($storyDetailPages ?? null) ? $storyDetailPages : [];
$tourDetailPages = is_array($tourDetailPages ?? null) ? $tourDetailPages : [];
?>


<div class="container py-4">
    <div class="mb-3">
        <h1 class="h3 mb-1">Page Management</h1>
        <p class="text mb-0">Choose what you want to edit.</p>
    </div>

    <div class="mb-3">
        <a href="/cms" class="btn btn-sm btn-outline-secondary">Back to CMS</a>
        <a href="/cms/users" class="btn btn-sm btn-outline-secondary">Users</a>
        <a href="/cms/tickets" class="btn btn-sm btn-outline-secondary">Tickets</a>
    </div>

    
    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Home</h2>
                        <span class="badge text-bg-success">Live</span>
                    </div>
                    <div class="d-grid gap-2 d-md-flex flex-wrap">
                        <a href="/cms/events/home" class="btn btn-outline-primary">Edit HomePage</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Dance</h2>
                        <span class="badge text-bg-success">Live</span>
                    </div>
                    <div class="mb-3">
                        <a href="/cms/events/dance-home" class="btn btn-primary">Edit Home Content</a>
                    </div>
                    <p class="mb-1">Sub Pages:</p>
                    <div class="d-grid gap-2 d-md-flex flex-wrap">
                        <?php foreach ($danceDetailPages as $detailPage): ?>
                            <?php if (!$detailPage instanceof EventDetailPageModel || trim((string)$detailPage->pageSlug) === '') { continue; } ?>
                            <a href="/cms/events/dance-detail/<?= rawurlencode($detailPage->pageSlug) ?>" class="btn btn-outline-primary">
                                Edit <?= htmlspecialchars(trim((string)($detailPage->performerName ?? '')) !== '' ? (string)$detailPage->performerName : $detailPage->pageSlug) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h5 mb-2">Roadmap</h2>
                    <p class="text-muted mb-3">Next steps for event administration.</p>
                    <ul class="mb-0">
                        <li>Edit festival events</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h5 mb-0">Tour</h2>
                        <span class="badge text-bg-success">Live</span>
                    </div>
                    <div class="mb-3">
                        <div class="mb-3">
                            <a href="/cms/events/tour-home" class="btn btn-primary">Edit Tour Home</a>
                        </div>
                        <p class="mb-1">Sub Pages:</p>
                        <div class="mb-3">
                            <?php foreach (array_chunk($tourDetailPages, 3) as $chunk): ?>
                                <div class="d-flex mb-2">
                                    <?php foreach ($chunk as $detailPage): ?>
                                        <a href="/cms/events/tour-details?id=<?= htmlspecialchars($detailPage['id']) ?>" class="btn btn-primary me-2">
                                            Edit <?= htmlspecialchars($detailPage['name']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h5 mb-0">Stories</h2>
                        <span class="badge text-bg-success">Live</span>
                    </div>
                    <div class="mb-3">
                        <a href="/cms/events/stories/schedule" class="btn btn-outline-primary">Edit Schedule</a>
                        <a href="/cms/events/stories-home" class="btn btn-primary">Edit Stories Home</a>
                    </div>
                    <p class="mb-1">Sub Pages:</p>
                    <div class="d-grid gap-2 d-md-flex flex-wrap mb-3">
                        <?php foreach ($storyDetailPages as $storyDetailPage): ?>
                            <?php if ((int)($storyDetailPage['id'] ?? 0) <= 0) { continue; } ?>
                            <a href="/cms/events/stories-details?id=<?= (int)$storyDetailPage['id'] ?>" class="btn btn-outline-primary">
                                Edit <?= htmlspecialchars((string)($storyDetailPage['page_name'] ?? $storyDetailPage['slug'] ?? 'Story detail')) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <p class="mb-0 text-muted">Edit hero, grid, venues, schedule, FAQ, and each Stories detail page.</p>
                </div>
            </div>
        </div>
    </div>
</div>
