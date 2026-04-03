<?php
$turnstileSiteKey = $_ENV['TURNSTILE_SITE_KEY'] ?? getenv('TURNSTILE_SITE_KEY');
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h3 mb-2">Register</h2>
                    <p class="text-muted mb-4">Create your account to continue.</p>

                    <?php require __DIR__ . '/../partialsViews/error.php'; ?>

                    <!-- Cloudflare Turnstile voor captcha -->
                    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

                    <form method="post" action="/register">
                       <div class="mb-3">
                            <label for="name" class="form-label">name</label>
                            <input type="text" id="name" name="name" class="form-control" value="" required>
                        </div>
                        <div class="mb-3">
                            <label for="phoneNumber" class="form-label">Phone number</label>
                            <input type="text" id="phoneNumber" name="phoneNumber" class="form-control" value="" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="" required>
                        </div>
                        <div class="mb-3">
                            <label for="country" class="form-label">country</label>
                            <input type="text" id="country" name="country" class="form-control" value="" required>
                        </div>
                        <div class="mb-3">
                            <label for="city" class="form-label">city</label>
                            <input type="text" id="city" name="city" class="form-control" value="" required>
                        </div>
                        <div class="mb-3">
                            <label for="addres" class="form-label">addres</label>
                            <input type="text" id="addres" name="addres" class="form-control" value="" required>
                        </div>
                        <div class="mb-3">
                            <label for="postcode" class="form-label">postcode</label>
                            <input type="text" id="postcode" name="postcode" class="form-control" value="" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Create a password" autocomplete="new-password" required>
                        </div>

                        <div class="mb-3">
                            <div class="cf-turnstile" data-sitekey="<?= htmlspecialchars($turnstileSiteKey) ?>"></div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>

                    <p class="mt-3 mb-0">
                        Already have an account? <a href="/login">Login here</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
