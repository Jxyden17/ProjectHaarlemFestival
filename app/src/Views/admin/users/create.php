<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Home</title>
    <link href="/css/Admin/users/index.css" rel="stylesheet">
</head>
<div class="admin-container">
    <h1>Add New User</h1>

    <form action="/admin/users/create" method="POST">
        <div class="form-group">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" placeholder="example@mail.com" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="role_id">Role:</label>
            <select id="role_id" name="role_id">
                <option value="1">Administrator</option>
                <option value="2" selected>Customer</option>
                <option value="3">Employee</option>
            </select>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-add">Create User</button>
            <a href="/users" class="btn btn-edit">Cancel</a>
        </div>
    </form>
</div>