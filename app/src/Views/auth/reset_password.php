<h2>Reset password</h2>

<?php require __DIR__ . '/../partialsViews/error.php'; ?>

<form method="post" action="/reset-password" class="mb-3">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

    <div class="mb-3">
        <label for="password" class="form-label">New password</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="confirm_password" class="form-label">Confirm new password</label>
        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Reset password</button>
</form>

<p><a href="/login">Back to login</a></p>
