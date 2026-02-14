<div class="container py-4">
    <h1 class="h3 mb-3">Edit User</h1>
    <div class="mb-3">
        <a href="/cms/users" class="btn btn-sm btn-outline-secondary">Back to Users</a>
    </div>

    <form action="/cms/users/edit" method="POST" class="card p-3">
        <input type="hidden" name="id" value="<?= (int)$user->id ?>">

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user->email) ?>" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Leave empty to keep current password">
        </div>

        <div class="mb-3">
            <label for="role_id" class="form-label">Role</label>
            <select id="role_id" name="role_id" class="form-select">
                <option value="1" <?= $user->userRole->value === 1 ? 'selected' : '' ?>>Administrator</option>
                <option value="2" <?= $user->userRole->value === 2 ? 'selected' : '' ?>>Customer</option>
                <option value="3" <?= $user->userRole->value === 3 ? 'selected' : '' ?>>Employee</option>
            </select>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="/cms/users" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
