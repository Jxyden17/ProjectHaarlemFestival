<div class="container-lg py-4 py-md-5">
    <div class="vstack gap-3">
        <section class="cms-page-hero">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <p class="cms-page-hero__eyebrow">Access control</p>
                    <h1 class="cms-page-hero__title">Edit User</h1>
                    <p class="cms-page-hero__description">Update account details, contact information, and role assignment for this CMS user.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms/users" class="btn btn-outline-secondary">Back to Users</a>
                </div>
            </div>
        </section>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="/cms/users/edit" method="POST" class="row g-3">
                    <input type="hidden" name="id" value="<?= (int)$user->id ?>">

                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($user->name) ?>" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="phoneNumber" class="form-label">Phone number</label>
                        <input type="text" id="phoneNumber" name="phoneNumber" class="form-control" value="<?= htmlspecialchars($user->phoneNumber) ?>" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user->email) ?>" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="country" class="form-label">Country</label>
                        <input type="text" id="country" name="country" class="form-control" value="<?= htmlspecialchars($user->country) ?>" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="city" class="form-label">City</label>
                        <input type="text" id="city" name="city" class="form-control" value="<?= htmlspecialchars($user->city) ?>" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="addres" class="form-label">Address</label>
                        <input type="text" id="addres" name="addres" class="form-control" value="<?= htmlspecialchars($user->addres) ?>" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="postcode" class="form-label">Postcode</label>
                        <input type="text" id="postcode" name="postcode" class="form-control" value="<?= htmlspecialchars($user->postcode) ?>" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="role_id" class="form-label">Role</label>
                        <select id="role_id" name="role_id" class="form-select">
                            <option value="1" <?= $user->userRole->value === 1 ? 'selected' : '' ?>>Administrator</option>
                            <option value="3" <?= $user->userRole->value === 3 ? 'selected' : '' ?>>Customer</option>
                            <option value="2" <?= $user->userRole->value === 2 ? 'selected' : '' ?>>Employee</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Leave empty to keep current password">
                    </div>

                    <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="/cms/users" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
