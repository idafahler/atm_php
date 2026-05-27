<?php
$error = flash_get('error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Create user</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h1>Create new user</h1>

    <?php require __DIR__ . '/../nav/nav_admin.php'; ?>

    <?php if ($error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endif; ?>

    <h2>New user</h2>

    <form method="POST" action="/admin/users/create">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <label>
            Name
            <input type="text" name="name" minlength="2" maxlength="100" value="<?= e(old('name')) ?>" required>
        </label>

        <label>
            PIN
            <input type="text" name="pin" pattern="\d{4}" maxlength="4" inputmode="numeric" required>
        </label>

        <label>
            Role
            <select name="role">
                <option value="user"  <?= old('role') === 'user'  ? 'selected' : '' ?>>user</option>
                <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>admin</option>
            </select>
        </label>

        <button type="submit">Create</button>
    </form>
</body>
</html>