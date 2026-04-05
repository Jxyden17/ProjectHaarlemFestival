<div class="container-lg py-4 py-md-5">
    <div class="vstack gap-3">
        <section class="cms-page-hero">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <p class="cms-page-hero__eyebrow">Access control</p>
                    <h1 class="cms-page-hero__title">Delete User</h1>
                    <p class="cms-page-hero__description">Review the account before permanently removing it from the CMS.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms/users" class="btn btn-outline-secondary">Back to Users</a>
                </div>
            </div>
        </section>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="alert alert-warning mb-4">
                    Are you sure you want to delete <strong><?= htmlspecialchars($user->email) ?></strong>?
                    This action cannot be undone.
                </div>

                <form action="/cms/users/delete" method="POST" class="d-flex flex-wrap gap-2">
                    <input type="hidden" name="id" value="<?= (int)$user->id ?>">
                    <button type="submit" class="btn btn-danger">Delete User</button>
                    <a href="/cms/users" class="btn btn-outline-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
