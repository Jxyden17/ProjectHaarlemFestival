<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h3 mb-2">Reset password</h2>
                    <p class="text-muted mb-4">Choose a new password for your account.</p>

                    <?php require __DIR__ . '/../partialsViews/error.php'; ?>

                    <form method="post" action="/reset-password">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

                        <div class="mb-3">
                            <label for="password" class="form-label">New password</label>
                            <input type="password" name="password" id="password" class="form-control" autocomplete="new-password" required>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm new password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" autocomplete="new-password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Reset password</button>
                    </form>

                    <p class="mt-3 mb-0"><a href="/login">Back to login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
