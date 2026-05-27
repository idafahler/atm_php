<?php
$myAccounts    = $accountRepo->findByUserId(current_user_id());
$otherAccounts = $accountRepo->findAllExceptUser(current_user_id());
$error    = flash_get('error');
$success  = flash_get('success');

$defaultFromId = isset($myAccounts[0]) ? (string)$myAccounts[0]['id'] : '';
$defaultToId   = isset($myAccounts[1]) ? (string)$myAccounts[1]['id'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer money</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h1>Transfer money</h1>

    <?php require __DIR__ . '/nav/nav_user.php'; ?>

    <?php if ($error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php elseif ($success): ?>
        <p class="success"><?= e($success) ?></p>
    <?php endif; ?>

    <h2>Between your own accounts</h2>

    <?php if (count($myAccounts) < 2): ?>
        <p>You need at least two accounts to transfer between them.</p>
    <?php else: ?>
        <form method="POST" action="/transfer">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="transfer_type" value="own">

            <label>
                From
                <select name="from_account_id" required>
                    <?php foreach ($myAccounts as $a): ?>
                        <option value="<?= e((string)$a['id']) ?>"
                            <?= (old('from_account_id') ?: $defaultFromId) === (string)$a['id'] ? 'selected' : '' ?>>
                            <?= e($a['account_type']) ?>
                            (<?= e(number_format((float)$a['balance'], 2, ',', ' ')) ?> kr)
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                To
                <select name="to_account_id" required>
                    <?php foreach ($myAccounts as $a): ?>
                        <option value="<?= e((string)$a['id']) ?>"
                            <?= (old('to_account_id') ?: $defaultToId) === (string)$a['id'] ? 'selected' : '' ?>>
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

            <button type="submit">Transfer</button>
        </form>
    <?php endif; ?>

    <h2>To another user</h2>

    <?php if (empty($otherAccounts)): ?>
        <p>There are no other accounts to transfer to.</p>
    <?php else: ?>
        <form method="POST" action="/transfer">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="transfer_type" value="external">

            <label>
                From (your account)
                <select name="from_account_id" required>
                    <?php foreach ($myAccounts as $a): ?>
                        <option value="<?= e((string)$a['id']) ?>">
                            <?= e($a['account_type']) ?>
                            (<?= e(number_format((float)$a['balance'], 2, ',', ' ')) ?> kr)
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Recipient
                <select name="to_account_id" required>
                    <option value="">— Select recipient —</option>
                    <?php foreach ($otherAccounts as $a): ?>
                        <option value="<?= e((string)$a['id']) ?>">
                            <?= e($a['owner_name']) ?> — <?= e($a['account_type']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Amount
                <input type="number" name="amount" step="0.01" min="0.01" required>
            </label>

            <button type="submit">Transfer</button>
        </form>
    <?php endif; ?>
</body>
</html>