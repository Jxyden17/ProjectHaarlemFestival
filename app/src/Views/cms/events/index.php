<?php
use App\Models\Event\EventDetailPageModel;

$danceDetailPages = is_array($danceDetailPages ?? null) ? $danceDetailPages : [];
$storyDetailPages = is_array($storyDetailPages ?? null) ? $storyDetailPages : [];
$tourDetailPages = is_array($tourDetailPages ?? null) ? $tourDetailPages : [];
$detailPageCount = count($danceDetailPages) + count($storyDetailPages) + count($tourDetailPages) + 2;
?>

<div class="container-lg py-4 py-md-5">
    <div class="vstack gap-3">
        <section class="cms-page-hero">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <p class="cms-page-hero__eyebrow">Content administration</p>
                    <h1 class="cms-page-hero__title">Page Management</h1>
                    <p class="cms-page-hero__description">Choose a public content area and move directly into its home page, detail pages, and supporting content editors.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms" class="btn btn-outline-secondary">Back to CMS</a>
                    <a href="/cms/users" class="btn btn-outline-secondary">Users</a>
                </div>
            </div>
        </section>

        <section class="card border-0 shadow-sm">
            <div class="card-body p-3 d-flex flex-wrap align-items-center gap-2">
                <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2">6 content modules</span>
                <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2"><?= $detailPageCount ?> detail pages</span>
            </div>
        </section>

        <div class="row g-3">
        <div class="col-12 col-xl-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-3">
                    <div class="mb-3">
                        <h2 class="h5 mb-2">Home</h2>
                        <p class="text-muted mb-0">Update the main homepage content and keep the festival landing experience current.</p>
                    </div>
                    <div class="d-grid gap-2 d-sm-flex flex-wrap">
                        <a href="/cms/events/home" class="btn btn-primary">Edit Homepage</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-3">
                    <div class="mb-3">
                        <h2 class="h5 mb-2">Dance</h2>
                        <p class="text-muted mb-0">Manage the Dance landing page and jump into individual performer detail pages.</p>
                    </div>
                    <div class="d-grid gap-2 d-sm-flex flex-wrap mb-3">
                        <a href="/cms/events/dance-home" class="btn btn-primary btn-sm">Edit Home Content</a>
                    </div>
                    <div class="border-top pt-3">
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($danceDetailPages as $detailPage): ?>
                                <?php if (!$detailPage instanceof EventDetailPageModel || trim((string)$detailPage->pageSlug) === '') { continue; } ?>
                                <a href="/cms/events/dance-detail/<?= rawurlencode($detailPage->pageSlug) ?>" class="btn btn-outline-primary btn-sm">
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
                    <div class="card-body p-3">
                    <div class="mb-3">
                        <h2 class="h5 mb-2">Jazz</h2>
                        <p class="text-muted mb-0">Manage the Jazz home page content and keep the section up to date.</p>
                    </div>
                    <div class="d-grid gap-2 d-sm-flex flex-wrap mb-3">
                        <a href="/cms/events/jazz-home" class="btn btn-primary btn-sm">Edit Home Content</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-3">
                    <div class="mb-3">
                        <h2 class="h5 mb-2">Tour</h2>
                        <p class="text-muted mb-0">Edit the Tour overview page and open each location or route detail page from one card.</p>
                    </div>
                    <div class="d-grid gap-2 d-sm-flex flex-wrap mb-3">
                        <a href="/cms/events/tour-home" class="btn btn-primary btn-sm">Edit Tour Home</a>
                    </div>
                    <div class="border-top pt-3">
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($tourDetailPages as $detailPage): ?>
                                <?php if ((int)($detailPage['id'] ?? 0) <= 0) { continue; } ?>
                                <a href="/cms/events/tour-details?id=<?= htmlspecialchars($detailPage['id']) ?>" class="btn btn-outline-primary btn-sm">
                                    Edit <?= htmlspecialchars((string)($detailPage['name'] ?? 'Tour detail')) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-3">
                    <div class="mb-3">
                        <h2 class="h5 mb-2">Stories</h2>
                        <p class="text-muted mb-0">Edit Stories home content, schedule content, and each story detail page from the same section.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <a href="/cms/events/stories-home" class="btn btn-primary btn-sm">Edit Stories Home</a>
                        <a href="/cms/events/stories/schedule" class="btn btn-outline-primary btn-sm">Edit Schedule</a>
                    </div>
                    <div class="border-top pt-3">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <?php foreach ($storyDetailPages as $storyDetailPage): ?>
                                <?php if ((int)($storyDetailPage['id'] ?? 0) <= 0) { continue; } ?>
                                <a href="/cms/events/stories-details?id=<?= (int)$storyDetailPage['id'] ?>" class="btn btn-outline-primary btn-sm">
                                    Edit <?= htmlspecialchars((string)($storyDetailPage['page_name'] ?? $storyDetailPage['slug'] ?? 'Story detail')) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-3">
                    <div class="mb-3">
                        <h2 class="h5 mb-2">Yummy</h2>
                        <p class="text-muted mb-0">Manage the Yummy homepage and linked restaurant pages from one content module.</p>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <a href="/cms/events/yummy-home" class="btn btn-primary btn-sm">Edit Yummy HomePage</a>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="/cms/events/yummy-details/ratatouille"
                            class="btn btn-outline-primary btn-sm">
                            Edit Ratatouille
                        </a>

                        <a href="/cms/events/yummy-details/cafe-de-roemer"
                            class="btn btn-outline-primary btn-sm">
                            Edit Cafe De Roemer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
