<h2>Forgot password</h2>

<?php require __DIR__ . '/../partialsViews/error.php'; ?>
<?php require __DIR__ . '/../partialsViews/info.php'; ?>

<form method="post" action="/forgot-password" class="mb-3">
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Send reset link</button>
</form>

<p><a href="/login">Back to login</a></p>
