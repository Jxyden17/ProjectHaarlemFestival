<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Home</title>
    <link href="/css/Admin/users/index.css" rel="stylesheet">
</head>
<div class="admin-container">
    <h1 >Delete User</h1>
    
    <div class="warning-box">
        <p>Are you sure you want to delete the user <strong><?= htmlspecialchars($user->email) ?></strong>?</p>
        <p><small>This action cannot be undone later!</small></p>
    </div>

    <form action="/admin/users/delete" method="POST">
        <input type="hidden" name="id" value="<?= $user->id ?>">
        
        <button type="submit" class="btn btn-delete">Delete User</button>
        <a href="/users" class="btn btn-add">Cancel</a>
    </form>
</div>