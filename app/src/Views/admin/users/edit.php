<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Home</title>
    <link href="/css/Admin/users/index.css" rel="stylesheet">
</head>
<div class="admin-container">
    <h1>Edit User</h1>

    <form action="/admin/users/edit" method="POST">
        
        <input type="hidden" name="id" value="<?= $user->id ?>">

        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" 
                   value="<?= htmlspecialchars($user->email) ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Leave empty to keep current password">
        </div>

        <div class="form-group">
            <label for="role_id">Function:</label>
            <select id="role_id" name="role_id">
                <option value="1" <?= $user->userRole->value === 1 ? 'selected' : '' ?>>Administrator</option>
                <option value="2" <?= $user->userRole->value === 2 ? 'selected' : '' ?>>Customer</option>
                <option value="3" <?= $user->userRole->value === 3 ? 'selected' : '' ?>>Employee</option>
            </select>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-add">Save Changes</button>
            <a href="/users" class="btn btn-edit">Cancel</a>
        </div>
    </form>
</div>