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
            <form method="GET" action="/users" class="search-form">
                <div class="search-input-group">
                    <input type="text" name="search" placeholder="Search users..." value="<?= htmlspecialchars($searchQuery) ?>" class="search-input">
                    <button type="submit" class="btn btn-search">Search</button>
                    <a href="/users" class="btn btn-clear">Clear</a>
                </div>
            </form>
        </div>

        <table class="user-table">
            <thead>
                <tr>
                    <th>Nr.</th> 
                    <th>
                        <a href="/users?sort=email&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>">Email</a>
                    </th>
                    <th>
                        <a href="/users?sort=role_id&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>">Functions</a>
                    </th>
                    <th>
                        <a href="/users?sort=created_at&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>">Account Made</a>
                    </th>
                    <th>
                        Actions
                    </th>
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