<div class="py-4">
    <h1 class="mb-3">Welcome<?php if (isset($email) && $email !== ''): ?>, <?= htmlspecialchars($email) ?><?php endif; ?>.</h1>
    <p class="lead">This is your starter template. Use it as a clean base for new projects.</p>

    <?php if (!isset($email) || $email === ''): ?>
        <div class="mt-4">
            <a class="btn btn-primary me-2" href="/login">Login</a>
            <a class="btn btn-outline-secondary" href="/register">Register</a>
        </div>
    <?php else: ?>
        <div class="mt-4">
            <a class="btn btn-outline-secondary" href="/logout">Logout</a>
        </div>
    <?php endif; ?>
</div>
