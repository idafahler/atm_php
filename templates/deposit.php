<?php
$accounts = $accountRepo->findByUserId(current_user_id());
$error    = flash_get('error');
$success  = flash_get('success');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h1>Deposit</h1>

    <?php require __DIR__ . '/nav/nav_user.php'; ?>

    <?php if ($error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php elseif ($success): ?>
        <p class="success"><?= e($success) ?></p>
    <?php endif; ?>

    <h2>Deposit money</h2>
    <form method="POST" action="/deposit">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <label>
            Account
            <select name="account_id" required>
                <?php foreach ($accounts as $a): ?>
                    <option value="<?= e((string)$a['id']) ?>"
                        <?= old('account_id') === (string)$a['id'] ? 'selected' : '' ?>>
                        <?= e($a['account_type']) ?>
                        (<?= e(number_format((float)$a['balance'], 2, ',', ' ')) ?> kr)
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Amount
            <input type="number" name="amount" step="0.01" min="0.01"
                   value="<?= e(old('amount')) ?>" required>
        </label>

        <button type="submit">Deposit</button>
    </form>
</body>
</html>