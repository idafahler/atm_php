<?php
$accountId = (int)($_GET['id'] ?? 0);
$account = $accountRepo->findById($accountId);

if (!$account) {
    flash_set('error', 'Account not found.');
    redirect('/dashboard');
}

if (current_user_role() !== 'admin' && (int)$account['user_id'] !== current_user_id()) {
    flash_set('error', 'Account not found.');
    redirect('/dashboard');
}

$error = flash_get('error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit account</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h1>Edit account</h1>

    <nav>
        <a href="/dashboard">Back to dashboard</a>
    </nav>

    <?php if ($error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endif; ?>

    <p>
        <strong>Type:</strong> <?= e($account['account_type']) ?><br>
        <strong>Balance:</strong> <?= e(number_format((float)$account['balance'], 2, ',', ' ')) ?> kr
    </p>

    <h2>Edit</h2>
    <form method="POST" action="/accounts/edit">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= e((string)$accountId) ?>">

        <label>
            Account name (optional)
            <input type="text" name="account_name" 
                   value="<?= e($account['account_name'] ?? '') ?>"
                   maxlength="100">
        </label>

        <button type="submit">Save</button>
    </form>
</body>
</html>