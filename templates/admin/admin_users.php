<?php
$users = $userRepo->all();

$error   = flash_get('error');
$success = flash_get('success');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Users</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h1>All users</h1>

    <?php require __DIR__ . '/../nav/nav_admin.php'; ?>

    <?php if ($error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php elseif ($success): ?>
        <p class="success"><?= e($success) ?></p>
    <?php endif; ?>

    <p><a href="/admin/users/create">Create new user</a></p>

    <h2>Users</h2>

    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Card number</th>
                <th>Role</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <?php $isMe = (int)$u['id'] === current_user_id(); ?>
                    <tr<?= $isMe ? ' class="is-me"' : '' ?>>
                    <td>
                        <a href="/admin/users/view?id=<?= e((string)$u['id']) ?>">
                            <?= e((string)$u['id']) ?>
                        </a>
                    </td>
                    <td>
                        <a href="/admin/users/view?id=<?= e((string)$u['id']) ?>">
                            <?= e($u['name']) ?>
                        </a>
                    </td>
                    <td><?= e($u['card_number']) ?></td>
                    <td><?= e($u['role']) ?></td>
                    <td><?= e(substr($u['created_at'], 0, 10)) ?></td>
                    <td>
                        <a href="/admin/users/edit?id=<?= e((string)$u['id']) ?>" class="btn">Edit</a>
                        <?php if ((int)$u['id'] !== current_user_id()): ?>
                            
                            <form method="POST" action="/admin/users/delete" class="inline-form"
                                  onsubmit="return confirm('Delete this user?');">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="id" value="<?= e((string)$u['id']) ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>