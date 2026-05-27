<?php
$accounts = $accountRepo->findByUserId(current_user_id());
$success  = flash_get('success');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h1>Welcome, <?= e($_SESSION['user_name']) ?></h1>

    <?php require __DIR__ . '/nav/nav_user.php'; ?>

    <?php if ($success): ?>
        <p class="success"><?= e($success) ?></p>
    <?php endif; ?>

    <h2>Your accounts</h2>
    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Balance</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($accounts as $a): ?>
                <tr>
                    <td>
                        <a href="/accounts/transactions?id=<?= e((string)$a['id']) ?>">
                            <?= e((string)$a['id']) ?>
                        </a>
                    </td>
                    <td><?= e($a['account_name'] ?? '-') ?></td>
                    <td><?= e($a['account_type']) ?></td>
                    <td><?= e(number_format((float)$a['balance'], 2, ',', ' ')) ?> kr</td>
                    <td>
                        <a href="/accounts/edit?id=<?= e((string)$a['id']) ?>" class="btn">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (current_user_role() === 'admin' || count($accounts) < 5): ?>
        <p><a href="/accounts/create">Open new account</a></p>
    <?php endif; ?>
</body>
</html>