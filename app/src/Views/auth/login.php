<h2>Login</h2>

<?php require __DIR__ . '/../partialsViews/error.php'; ?>
<?php require __DIR__ . '/../partialsViews/info.php'; ?>

<form method="post" action="/login" class="mb-3">
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Login</button>
</form>

<p><a href="/forgot-password">Forgot your password?</a></p>
<p><a href="/register">Register</a></p>
