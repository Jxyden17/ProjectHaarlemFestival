<?php
$storiesError = isset($storiesError) && is_string($storiesError) ? $storiesError : '';
?>

<div class="container-lg py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-1">Create Stories Event</h1>
            <p class="text-muted mb-0">Add a new Stories detail page and start editing it right away.</p>
        </div>
        <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
    </div>

    <?php if ($storiesError !== ''): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($storiesError) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/cms/events/stories/create" class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="mb-3">
                <label for="stories-page-name" class="form-label">Event title</label>
                <input
                    type="text"
                    id="stories-page-name"
                    name="page_name"
                    class="form-control"
                    placeholder="For example: New Podcast Night"
                    required
                >
            </div>

        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="/cms/events" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-success">Create Event Page</button>
        </div>
    </form>
</div>
