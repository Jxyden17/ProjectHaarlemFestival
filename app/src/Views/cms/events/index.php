<?php
use App\Models\Event\EventDetailPageModel;

$danceDetailPages = is_array($danceDetailPages ?? null) ? $danceDetailPages : [];
$storyDetailPages = is_array($storyDetailPages ?? null) ? $storyDetailPages : [];
$storiesSuccess = isset($storiesSuccess) && is_string($storiesSuccess) ? $storiesSuccess : '';
$storiesError = isset($storiesError) && is_string($storiesError) ? $storiesError : '';
$tourDetailPages = is_array($tourDetailPages ?? null) ? $tourDetailPages : [];
?>

<div class="container-lg py-4 py-md-5">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <p class="text-uppercase text-muted small mb-2">Content Management System</p>
                    <h1 class="h2 mb-2">Page Management</h1>
                    <p class="text-muted mb-0">Choose the festival page you want to edit and jump straight into its content.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms" class="btn btn-outline-secondary">Back to CMS</a>
                    <a href="/cms/users" class="btn btn-outline-secondary">Users</a>
                    <a href="/cms/tickets" class="btn btn-outline-secondary">Tickets</a>
                </div>
            </div>
        </div>
    </div>

    <?php if ($storiesSuccess !== ''): ?>
        <div class="alert alert-success" role="alert">
            <?= htmlspecialchars($storiesSuccess) ?>
        </div>
    <?php endif; ?>

    <?php if ($storiesError !== ''): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($storiesError) ?>
        </div>
    <?php endif; ?>

    
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

        <div class="col-12 col-xl-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Dance</h2>
                    <p class="text-muted mb-4">Manage the Dance landing page and jump into individual performer detail pages.</p>
                    <div class="d-grid gap-2 d-sm-flex flex-wrap mb-4">
                        <a href="/cms/events/dance-home" class="btn btn-primary">Edit Home Content</a>
                    </div>
                    <div class="border-top pt-3">
                        <p class="text-uppercase text-muted small fw-semibold mb-2">Sub Pages</p>
                        <div class="d-grid gap-2 d-sm-flex flex-wrap">
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
        </div>

        <div class="col-12 col-xl-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Tour</h2>
                    <p class="text-muted mb-4">Edit the Tour overview page and open each location or route detail page from one card.</p>
                    <div class="d-grid gap-2 d-sm-flex flex-wrap mb-4">
                        <a href="/cms/events/tour-home" class="btn btn-primary">Edit Tour Home</a>
                    </div>
                    <div class="border-top pt-3">
                        <p class="text-uppercase text-muted small fw-semibold mb-2">Sub Pages</p>
                        <div class="d-grid gap-2 d-sm-flex flex-wrap">
                            <?php foreach ($tourDetailPages as $detailPage): ?>
                                <?php if ((int)($detailPage['id'] ?? 0) <= 0) { continue; } ?>
                                <a href="/cms/events/tour-details?id=<?= htmlspecialchars($detailPage['id']) ?>" class="btn btn-outline-primary">
                                    Edit <?= htmlspecialchars((string)($detailPage['name'] ?? 'Tour detail')) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Stories</h2>
                        <div class="d-flex align-items-center gap-2">
                            <a href="/cms/events/stories/create" class="btn btn-sm btn-success">Add Story Event</a>
                            <span class="badge text-bg-success">Live</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <a href="/cms/events/stories/schedule" class="btn btn-outline-primary">Edit Schedule</a>
                        <a href="/cms/events/stories-home" class="btn btn-primary">Edit Stories Home</a>
                        <a href="/cms/events/stories/schedule" class="btn btn-outline-primary">Edit Schedule</a>
                    </div>
                    <div class="border-top pt-3">
                        <p class="text-uppercase text-muted small fw-semibold mb-2">Sub Pages</p>
                        <div class="d-grid gap-2 d-sm-flex flex-wrap mb-3">
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
</div>
