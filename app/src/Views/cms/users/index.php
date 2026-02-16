<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">User Management</h1>
        <a href="/cms/users/create" class="btn btn-primary">Add User</a>
    </div>

    <div class="mb-3">
        <a href="/cms" class="btn btn-sm btn-outline-secondary">Back to CMS</a>
        <a href="/cms/events" class="btn btn-sm btn-outline-secondary">Events</a>
        <a href="/cms/tickets" class="btn btn-sm btn-outline-secondary">Tickets</a>
    </div>

    <form method="GET" action="/cms/users" class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="text" name="search" value="<?= htmlspecialchars((string)($searchQuery ?? '')) ?>" class="form-control" placeholder="Search users...">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-primary">Search</button>
        </div>
        <div class="col-auto">
            <a href="/cms/users" class="btn btn-outline-secondary">Clear</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th><a href="/cms/users?sort=email&order=<?= ($order ?? 'asc') === 'asc' ? 'desc' : 'asc' ?>">Email</a></th>
                    <th><a href="/cms/users?sort=role_id&order=<?= ($order ?? 'asc') === 'asc' ? 'desc' : 'asc' ?>">Role</a></th>
                    <th><a href="/cms/users?sort=created_at&order=<?= ($order ?? 'asc') === 'asc' ? 'desc' : 'asc' ?>">Created</a></th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $counter = 1; ?>
                <?php foreach (($users ?? []) as $user): ?>
                    <tr>
                        <td><?= $counter++ ?></td>
                        <td><?= htmlspecialchars($user->email) ?></td>
                        <td><?= htmlspecialchars($user->userRole->label()) ?></td>
                        <td><?= htmlspecialchars((string)$user->createdAt) ?></td>
                        <td>
                            <a href="/cms/users/edit?id=<?= (int)$user->id ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <a href="/cms/users/delete?id=<?= (int)$user->id ?>" class="btn btn-sm btn-outline-danger">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
