<?php
$users = $userRepo->all();
$error = flash_get('error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Create account</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h1>Create account for user</h1>

    <?php require __DIR__ . '/../nav/nav_admin.php'; ?>

    <?php if ($error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endif; ?>

    <h2>New account</h2>
    <form method="POST" action="/admin/accounts/create">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <label>
            User
            <select name="user_id" required>
                <option value="">— Select user —</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= e((string)$u['id']) ?>"
                        <?= old('user_id') === (string)$u['id'] ? 'selected' : '' ?>>
                        <?= e($u['name']) ?> (<?= e($u['role']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Account type
            <select name="account_type" required>
                <option value="checking" <?= old('account_type') === 'checking' ? 'selected' : '' ?>>Checking</option>
                <option value="savings"  <?= old('account_type') === 'savings'  ? 'selected' : '' ?>>Savings</option>
            </select>
        </label>

        <label>
            Account name
            <input type="text" name="account_name" value="<?= e(old('account_name')) ?>" maxlength="100">
        </label>

        <button type="submit">Create</button>
    </form>
</body>
</html>