<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Home</title>
    <link href="/css/Admin/users/index.css" rel="stylesheet">
</head>
<body>
<div class="admin-container">
        <h1>Welcome Admin</h1>

        <div class="action-bar">
            <a href="/admin/users/create" class="btn btn-add">Add New User</a>
        </div>

        <div class="search-bar">
            <form method="GET" action="/users">
                <input type="text" name="search" placeholder="Search users..." value="<?= htmlspecialchars($searchQuery) ?>">
                <button type="submit" class="btn btn-search">Search</button>
                <a href="/users" class="btn btn-clear">Clear</a>
            </form>
        </div>
        
        <table class="user-table">
            <thead>
                <tr>
                    <th>Nr.</th> 
                    <th>Email</th>
                    <th>Functions</th>
                    <th>Account Made</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $counter = 1;
                foreach ($users as $user): 
                ?>
                    <tr>
                        <td><?= $counter++ ?></td> 
                        <td><?= htmlspecialchars($user->email) ?></td>
                        <td><?= $user->userRole->label() ?></td>
                        <td><?= $user->createdAt ?></td>
                        <td class="actions">
                            <a href="/admin/users/edit?id=<?= $user->id ?>" class="btn btn-edit">Edit</a>
                            <a href="/admin/users/delete?id=<?= $user->id ?>" class="btn btn-delete">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>