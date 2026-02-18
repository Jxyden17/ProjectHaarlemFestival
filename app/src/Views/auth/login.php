<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h3 mb-2">Login</h2>
                    <p class="text-muted mb-4">Sign in to your account.</p>

                    <?php require __DIR__ . '/../partialsViews/error.php'; ?>
                    <?php require __DIR__ . '/../partialsViews/info.php'; ?>

                    <form method="post" action="/login">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" autocomplete="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" autocomplete="current-password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>

                    <div class="mt-3 d-flex flex-column gap-2">
                        <a href="/forgot-password">Forgot your password?</a>
                        <p class="mb-0">New here? <a href="/register">Create an account</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
