<div class="container py-4">
    <h1 class="h3 mb-3 text-danger">Delete User</h1>
    <div class="mb-3">
        <a href="/cms/users" class="btn btn-sm btn-outline-secondary">Back to Users</a>
    </div>

    <div class="alert alert-warning">
        Are you sure you want to delete <strong><?= htmlspecialchars($user->email) ?></strong>?
        This action cannot be undone.
    </div>

    <form action="/cms/users/delete" method="POST">
        <input type="hidden" name="id" value="<?= (int)$user->id ?>">
        <button type="submit" class="btn btn-danger">Delete User</button>
        <a href="/cms/users" class="btn btn-outline-secondary">Cancel</a>
    </form>
</div>
