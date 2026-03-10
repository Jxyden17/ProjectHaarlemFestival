<div class="container py-4">
    <h1 class="h3 mb-3">your profile</h1>
    <a href="/" class="btn btn-sm btn-outline-secondary">Back to home</a>
    <div class="mb-3">
        <label >Email</label>
        <p><?= htmlspecialchars($user->email) ?></p>
    </div>
    <div class="mb-3">
        <label >Password</label>
        <p><?= htmlspecialchars($user->password) ?></p>
    </div>
    <div class="mb-3">
        <label for="role_id" class="form-label">Role</label>
        <p><?= htmlspecialchars($user->role) ?></p>
    </div>
     <div class="d-flex gap-2">
        <a href="/cms/users/editSelf" class="btn btn-primary">edit profile</a>
        </div>
</div>
