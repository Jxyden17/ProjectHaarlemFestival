<h2>Register</h2>

<?php require __DIR__ . '/../partialsViews/error.php'; ?>

<form method="post" action="/register" class="mt-3">
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control" placeholder="Enter email" required>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
    </div>

    <button type="submit" class="btn btn-primary">Register</button>
</form>

<p class="mt-3">
    Already have an account? <a href="/login">Login here</a>.
</p>
