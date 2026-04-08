<?php 
use App\Models\ViewModels\Cms\Dance\DanceHomeEditViewModel;
$contentViewModel = (isset($contentViewModel) && $contentViewModel instanceof DanceHomeEditViewModel)
    ? $contentViewModel
    : new DanceHomeEditViewModel('', '', '', '', '', '', '', '', '', '', '', '', '');
?>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<div class="container-lg py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-1">Dance Home Content</h1>
            <p class="text-muted mb-0">Update the public dance landing page content and rich-text sections.</p>
        </div>
        <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
    </div>

    <form method="POST" action="/cms/events/dance-home" class="card border-0 shadow-sm" data-quill-form="1" data-save-api="/cms/events/dance-homeAPI">
        <div class="card-body p-4">
            <div class="accordion" id="danceHomeAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="dance-home-page-heading">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#dance-home-page-panel" aria-expanded="true" aria-controls="dance-home-page-panel">
                            Page
                        </button>
                    </h2>
                    <div id="dance-home-page-panel" class="accordion-collapse collapse show" aria-labelledby="dance-home-page-heading" data-bs-parent="#danceHomeAccordion">
                        <div class="accordion-body p-4">
                            <div class="mb-0">
                                <label for="page_title" class="form-label">Browser Tab Title</label>
                                <input
                                    type="text"
                                    id="page_title"
                                    name="page_title"
                                    class="form-control"
                                    value="<?= htmlspecialchars($contentViewModel->pageTitle) ?>"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="dance-home-banner-heading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dance-home-banner-panel" aria-expanded="false" aria-controls="dance-home-banner-panel">
                            Banner
                        </button>
                    </h2>
                    <div id="dance-home-banner-panel" class="accordion-collapse collapse" aria-labelledby="dance-home-banner-heading" data-bs-parent="#danceHomeAccordion">
                        <div class="accordion-body p-4">
                            <div class="mb-3">
                                <label for="banner_badge" class="form-label">Banner Badge</label>
                                <input
                                    type="text"
                                    id="banner_badge"
                                    name="banner_badge"
                                    class="form-control"
                                    value="<?= htmlspecialchars($contentViewModel->bannerBadge) ?>"
                                >
                            </div>

                            <div class="mb-3">
                                <label for="banner_title" class="form-label">Banner Title</label>
                                <input
                                    type="text"
                                    id="banner_title"
                                    name="banner_title"
                                    class="form-control"
                                    value="<?= htmlspecialchars($contentViewModel->bannerTitle) ?>"
                                    required
                                >
                            </div>

                            <div class="mb-0">
                                <label for="banner_description" class="form-label">Banner Description</label>
                                <textarea
                                    id="banner_description"
                                    name="banner_description"
                                    class="form-control"
                                    data-quill="1"
                                    rows="4"
                                    required
                                ><?= htmlspecialchars($contentViewModel->bannerDescription) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="dance-home-featured-heading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dance-home-featured-panel" aria-expanded="false" aria-controls="dance-home-featured-panel">
                            Featured Artists &amp; Schedule
                        </button>
                    </h2>
                    <div id="dance-home-featured-panel" class="accordion-collapse collapse" aria-labelledby="dance-home-featured-heading" data-bs-parent="#danceHomeAccordion">
                        <div class="accordion-body p-4">
                            <div class="alert alert-info" role="alert">
                                This section controls the featured artists title and the schedule title. Performer names, types, descriptions, schedule sessions, and artist images are managed in Event Management.
                            </div>
                            <div class="mb-3">
                                <label for="featured_artists_title" class="form-label">Featured Artists Title</label>
                                <input
                                    type="text"
                                    id="featured_artists_title"
                                    name="featured_artists_title"
                                    class="form-control"
                                    value="<?= htmlspecialchars($contentViewModel->featuredArtistsTitle) ?>"
                                    required
                                >
                            </div>
                            <div class="mb-0">
                                <label for="schedule_title" class="form-label">Schedule Title</label>
                                <input
                                    type="text"
                                    id="schedule_title"
                                    name="schedule_title"
                                    class="form-control"
                                    value="<?= htmlspecialchars($contentViewModel->scheduleTitle) ?>"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="dance-home-info-heading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dance-home-info-panel" aria-expanded="false" aria-controls="dance-home-info-panel">
                            Important Information
                        </button>
                    </h2>
                    <div id="dance-home-info-panel" class="accordion-collapse collapse" aria-labelledby="dance-home-info-heading" data-bs-parent="#danceHomeAccordion">
                        <div class="accordion-body p-4">
                            <div class="mb-3">
                                <label for="important_information_title" class="form-label">Title</label>
                                <input
                                    type="text"
                                    id="important_information_title"
                                    name="important_information_title"
                                    class="form-control"
                                    value="<?= htmlspecialchars($contentViewModel->importantInformationTitle) ?>"
                                    required
                                >
                            </div>
                            <div class="mb-0">
                                <label for="important_information_html" class="form-label">Content (HTML/Text)</label>
                                <textarea
                                    id="important_information_html"
                                    name="important_information_html"
                                    class="form-control"
                                    data-quill="1"
                                    rows="6"
                                    required
                                ><?= htmlspecialchars($contentViewModel->importantInformationHtml) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="dance-home-passes-heading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dance-home-passes-panel" aria-expanded="false" aria-controls="dance-home-passes-panel">
                            All-Access Passes
                        </button>
                    </h2>
                    <div id="dance-home-passes-panel" class="accordion-collapse collapse" aria-labelledby="dance-home-passes-heading" data-bs-parent="#danceHomeAccordion">
                        <div class="accordion-body p-4">
                            <div class="mb-3">
                                <label for="passes_title" class="form-label">Passes Section Title</label>
                                <input
                                    type="text"
                                    id="passes_title"
                                    name="passes_title"
                                    class="form-control"
                                    value="<?= htmlspecialchars($contentViewModel->passesTitle) ?>"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="dance-home-capacity-heading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dance-home-capacity-panel" aria-expanded="false" aria-controls="dance-home-capacity-panel">
                            Capacity &amp; Entry
                        </button>
                    </h2>
                    <div id="dance-home-capacity-panel" class="accordion-collapse collapse" aria-labelledby="dance-home-capacity-heading" data-bs-parent="#danceHomeAccordion">
                        <div class="accordion-body p-4">
                            <div class="mb-3">
                                <label for="capacity_title" class="form-label">Title</label>
                                <input
                                    type="text"
                                    id="capacity_title"
                                    name="capacity_title"
                                    class="form-control"
                                    value="<?= htmlspecialchars($contentViewModel->capacityTitle) ?>"
                                    required
                                >
                            </div>
                            <div class="mb-0">
                                <label for="capacity_html" class="form-label">Content (HTML/Text)</label>
                                <textarea
                                    id="capacity_html"
                                    name="capacity_html"
                                    class="form-control"
                                    data-quill="1"
                                    rows="6"
                                    required
                                ><?= htmlspecialchars($contentViewModel->capacityHtml) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="dance-home-special-heading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dance-home-special-panel" aria-expanded="false" aria-controls="dance-home-special-panel">
                            Special Session
                        </button>
                    </h2>
                    <div id="dance-home-special-panel" class="accordion-collapse collapse" aria-labelledby="dance-home-special-heading" data-bs-parent="#danceHomeAccordion">
                        <div class="accordion-body p-4">
                            <div class="mb-3">
                                <label for="special_title" class="form-label">Title</label>
                                <input
                                    type="text"
                                    id="special_title"
                                    name="special_title"
                                    class="form-control"
                                    value="<?= htmlspecialchars($contentViewModel->specialTitle) ?>"
                                    required
                                >
                            </div>
                            <div class="mb-0">
                                <label for="special_html" class="form-label">Content (HTML/Text)</label>
                                <textarea
                                    id="special_html"
                                    name="special_html"
                                    class="form-control"
                                    data-quill="1"
                                    rows="6"
                                    required
                                ><?= htmlspecialchars($contentViewModel->specialHtml) ?></textarea>
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
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script src="/js/cms/page-editor.js"></script>
<script src="/js/cms/form-save-api.js"></script>
<script src="/js/cms/dance-home.js"></script>
