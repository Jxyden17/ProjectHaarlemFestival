<div class="container py-4">
    <h1 class="h3 mb-3">Edit User</h1>
    <form action="/cms/users/editSelf" method="POST" class="card p-3">
        <input type="hidden" name="id" value="<?= (int)$user->id ?>">

        <div class="mb-3">
            <label for="name" class="form-label">name</label>
            <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($user->name) ?>" required>
        </div>
        <div class="mb-3">
            <label for="phoneNumber" class="form-label">Phone number</label>
            <input type="text" id="phoneNumber" name="phoneNumber" class="form-control" value="<?= htmlspecialchars($user->phoneNumber) ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user->email) ?>" required>
        </div>
        <div class="mb-3">
            <label for="country" class="form-label">country</label>
            <input type="text" id="country" name="country" class="form-control" value="<?= htmlspecialchars($user->country) ?>" required>
        </div>
        <div class="mb-3">
            <label for="city" class="form-label">city</label>
            <input type="text" id="city" name="city" class="form-control" value="<?= htmlspecialchars($user->city) ?>" required>
        </div>
         <div class="mb-3">
            <label for="addres" class="form-label">addres</label>
            <input type="text" id="addres" name="addres" class="form-control" value="<?= htmlspecialchars($user->addres) ?>" required>
        </div>
         <div class="mb-3">
            <label for="postcode" class="form-label">postcode</label>
            <input type="text" id="postcode" name="postcode" class="form-control" value="<?= htmlspecialchars($user->postcode) ?>" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Leave empty to keep current password">
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
