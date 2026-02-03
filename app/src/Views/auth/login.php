<h2>Login</h2>

<?php require __DIR__ . '/../partialsViews/error.php'; ?>

<form method="post" action="/login" class="mb-3">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" name="username" id="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Login</button>
</form>
<a href="/register">Register</a>
