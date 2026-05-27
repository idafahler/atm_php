<?php
$id = (int)($_GET['id'] ?? 0);
$user = $userRepo->findById($id);

if (!$user) {
    flash_set('error', 'User not found.');
    redirect('/admin/users');
}

$error = flash_get('error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edit user</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h1>Edit user: <?= e((string)$user['name']) ?></h1>

    <?php require __DIR__ . '/../nav/nav_admin.php'; ?>

    <?php if ($error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endif; ?>

    <h2>Update name and role</h2>

    <form method="POST" action="/admin/users/edit">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= e((string)$user['id']) ?>">

        <label>
            Name
            <input type="text" name="name" value="<?= e(old('name', $user['name'])) ?>" required>
        </label>

        <label>
            Role
            <select name="role">
                <?php $currentRole = old('role', $user['role']); ?>
                <option value="user"  <?= $currentRole === 'user'  ? 'selected' : '' ?>>user</option>
                <option value="admin" <?= $currentRole === 'admin' ? 'selected' : '' ?>>admin</option>
            </select>
        </label>

        <button type="submit">Save</button>
    </form>

    <h2>Update card number</h2>

    <form method="POST" action="/admin/users/regenerate-card">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= e((string)$user['id']) ?>">
        <p>Card number: <?= e($user['card_number']) ?></p>
        <button type="submit" onclick="return confirm('Generate new card number?')">
            Generate new card number
        </button>
    </form>

    <h2>Update PIN</h2>

    <form method="POST" action="/admin/users/reset-pin">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= e((string)$user['id']) ?>">

    <label>
        New PIN
        <input type="text" name="pin" pattern="\d{4}" maxlength="4" inputmode="numeric" required>
    </label>

    <button type="submit" onclick="return confirm('Reset PIN for this user?')">
        Set new PIN
    </button>
</form>
</body>
</html>