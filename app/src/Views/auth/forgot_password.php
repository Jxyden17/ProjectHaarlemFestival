<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h3 mb-2">Forgot password</h2>
                    <p class="text-muted mb-4">Enter your email and we will send you a reset link.</p>

                    <?php require __DIR__ . '/../partialsViews/error.php'; ?>
                    <?php require __DIR__ . '/../partialsViews/info.php'; ?>

                    <form method="post" action="/forgot-password">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" autocomplete="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send reset link</button>
                    </form>

                    <p class="mt-3 mb-0"><a href="/login">Back to login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
