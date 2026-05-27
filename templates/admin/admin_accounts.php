<?php
$accounts = $accountRepo->all();
$error    = flash_get('error');
$success  = flash_get('success');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Accounts</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h1>All accounts</h1>

    <?php require __DIR__ . '/../nav/nav_admin.php'; ?>

    <?php if ($error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php elseif ($success): ?>
        <p class="success"><?= e($success) ?></p>
    <?php endif; ?>

    <p><a href="/admin/accounts/create">Create account for user</a></p>

    <h2>Accounts</h2>
    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>ID</th>
                <th>Owner</th>
                <th>Type</th>
                <th>Name</th>
                <th>Balance</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($accounts as $a): ?>
                <?php $isMine = (int)$a['user_id'] === current_user_id(); ?>
                <tr<?= $isMine ? ' class="is-me"' : '' ?>>
                    <td>
                        <a href="/accounts/transactions?id=<?= e((string)$a['id']) ?>">
                            <?= e((string)$a['id']) ?>
                        </a>
                    </td>
                    <td>
                        <a href="/admin/users/view?id=<?= e((string)$a['user_id']) ?>">
                            <?= e($a['owner_name']) ?>
                        </a>
                    </td>
                    <td><?= e($a['account_type']) ?></td>
                    <td><?= e($a['account_name'] ?? '-') ?></td>
                    <td><?= e(number_format((float)$a['balance'], 2, ',', ' ')) ?> kr</td>
                    <td>
                        <a href="/accounts/edit?id=<?= e((string)$a['id']) ?>" class="btn">Edit</a>
                        <?php if ((float)$a['balance'] === 0.0): ?>
                            <form method="POST" action="/admin/accounts/delete" class="inline-form"
                                onsubmit="return confirm('Delete this account?');">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="id" value="<?= e((string)$a['id']) ?>">
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