<?php
$error = flash_get('error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h1>Open new account</h1>

    <?php require __DIR__ . '/nav/nav_user.php'; ?>

    <?php if ($error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endif; ?>

    <h2>New account</h2>

    <form method="POST" action="/accounts/create">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <label>
            Account type
            <select name="account_type" required>
                <option value="checking" <?= old('account_type') === 'checking' ? 'selected' : '' ?>>Checking</option>
                <option value="savings"  <?= old('account_type') === 'savings'  ? 'selected' : '' ?>>Savings</option>
            </select>
        </label>

        <label>
            Account name (optional)
            <input type="text" name="account_name" value="<?= e(old('account_name')) ?>">
        </label>

        <button type="submit">Open account</button>
    </form>
</body>
</html>