<?php
$userRows = is_array($users ?? null) ? $users : [];
$sortOrder = ($order ?? 'asc') === 'asc' ? 'desc' : 'asc';
$searchValue = htmlspecialchars((string)($searchQuery ?? ''));
$searchQueryString = trim((string)($searchQuery ?? '')) !== '' ? '&search=' . urlencode((string)$searchQuery) : '';
?>
<div class="container-lg py-4 py-md-5 cms-users-page">
    <div class="vstack gap-3">
        <section class="cms-page-hero">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <p class="cms-page-hero__eyebrow">Access control</p>
                    <h1 class="cms-page-hero__title">User Management</h1>
                    <p class="cms-page-hero__description">Search, sort, and maintain CMS user accounts from one consistent administrative workflow.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms" class="btn btn-outline-secondary">Back to CMS</a>
                    <a href="/cms/users/create" class="btn btn-primary">Add User</a>
                </div>
            </div>
        </section>

        <section class="card border-0 shadow-sm">
            <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2"><?= count($userRows) ?> users</span>
                    <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2"><?= trim((string)($searchQuery ?? '')) !== '' ? 'Filtered result' : 'All records' ?></span>
                </div>
                <form method="GET" action="/cms/users" class="row g-2 align-items-center m-0">
                    <div class="col-12 col-md-auto">
                    <input type="text" name="search" value="<?= $searchValue ?>" class="form-control" placeholder="Search users...">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-outline-primary">Search</button>
                    </div>
                    <div class="col-auto">
                        <a href="/cms/users" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </form>
            </div>
        </section>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle cms-users-table">
                        <thead>
                            <tr>
                                <th class="cms-users-table__number">#</th>
                                <th>Name</th>
                                <th class="cms-users-table__nowrap">
                                    <a href="/cms/users?sort=email&order=<?= $sortOrder . $searchQueryString ?>" class="d-inline-flex align-items-center gap-2">
                                        <span>Email</span>
                                        <i data-lucide="arrow-up-down"></i>
                                    </a>
                                </th>
                                <th class="cms-users-table__nowrap">Phone number</th>
                                <th>Country</th>
                                <th>City</th>
                                <th>Address</th>
                                <th class="cms-users-table__nowrap">Postcode</th>
                                <th class="cms-users-table__nowrap">
                                    <a href="/cms/users?sort=role_id&order=<?= $sortOrder . $searchQueryString ?>" class="d-inline-flex align-items-center gap-2">
                                        <span>Role</span>
                                        <i data-lucide="arrow-up-down"></i>
                                    </a>
                                </th>
                                <th class="cms-users-table__nowrap">
                                    <a href="/cms/users?sort=created_at&order=<?= $sortOrder . $searchQueryString ?>" class="d-inline-flex align-items-center gap-2">
                                        <span>Created</span>
                                        <i data-lucide="arrow-up-down"></i>
                                    </a>
                                </th>
                                <th class="cms-users-table__nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($userRows === []): ?>
                                <tr>
                                    <td colspan="11" class="cms-empty-state">No users matched the current query.</td>
                                </tr>
                            <?php else: ?>
                                <?php $counter = 1; ?>
                                <?php foreach ($userRows as $user): ?>
                                    <tr>
                                        <td class="cms-users-table__number"><?= $counter++ ?></td>
                                        <td><?= htmlspecialchars($user->name) ?></td>
                                        <td class="cms-users-table__nowrap"><?= htmlspecialchars($user->email) ?></td>
                                        <td class="cms-users-table__nowrap"><?= htmlspecialchars($user->phoneNumber) ?></td>
                                        <td><?= htmlspecialchars($user->country) ?></td>
                                        <td><?= htmlspecialchars($user->city) ?></td>
                                        <td><?= htmlspecialchars($user->addres) ?></td>
                                        <td class="cms-users-table__nowrap"><?= htmlspecialchars($user->postcode) ?></td>
                                        <td class="cms-users-table__nowrap"><?= htmlspecialchars($user->userRole->label()) ?></td>
                                        <td class="cms-users-table__nowrap"><?= htmlspecialchars((string)$user->createdAt) ?></td>
                                        <td class="cms-users-table__nowrap">
                                            <div class="cms-table-actions">
                                                <a href="/cms/users/edit?id=<?= (int)$user->id ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <a href="/cms/users/delete?id=<?= (int)$user->id ?>" class="btn btn-sm btn-outline-danger">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
