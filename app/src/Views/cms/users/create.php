<div class="container-lg py-4 py-md-5">
    <div class="vstack gap-3">
        <section class="cms-page-hero">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <p class="cms-page-hero__eyebrow">Access control</p>
                    <h1 class="cms-page-hero__title">Create User</h1>
                    <p class="cms-page-hero__description">Add a new CMS user account and assign the correct role before granting access.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms/users" class="btn btn-outline-secondary">Back to Users</a>
                </div>
            </div>
        </section>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars((string)$error) ?></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="/cms/users/create" method="POST" class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="phoneNumber" class="form-label">Phone number</label>
                        <input type="text" id="phoneNumber" name="phoneNumber" class="form-control" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="country" class="form-label">Country</label>
                        <input type="text" id="country" name="country" class="form-control" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="city" class="form-label">City</label>
                        <input type="text" id="city" name="city" class="form-control" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="addres" class="form-label">Address</label>
                        <input type="text" id="addres" name="addres" class="form-control" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="postcode" class="form-label">Postcode</label>
                        <input type="text" id="postcode" name="postcode" class="form-control" required>
                    </div>

                    <div class="col-12">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="role_id" class="form-label">Role</label>
                        <select id="role_id" name="role_id" class="form-select">
                            <option value="1">Administrator</option>
                            <option value="3" selected>Customer</option>
                            <option value="2">Employee</option>
                        </select>
                    </div>

                    <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                        <button type="submit" class="btn btn-primary">Create User</button>
                        <a href="/cms/users" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
