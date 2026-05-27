<?php
$totalUsers        = $userRepo->countAll();
$usersByRole       = $userRepo->countByRole();

$totalAccounts     = $accountRepo->countAll();
$accountsByType    = $accountRepo->countByType();
$totalBalance      = $accountRepo->totalBalance();

$totalTransactions = $transactionRepo->countAll();
$transactionsByType = $transactionRepo->countByType();
$recentTransactions = $transactionRepo->recent(5);
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
    <h1>Statistics</h1>

    <?php require __DIR__ . '/../nav/nav_admin.php'; ?>

    <h2>Users</h2>
    <p>Total: <strong><?= $totalUsers ?></strong></p>
    <ul>
        <?php foreach ($usersByRole as $row): ?>
            <li><?= e($row['role']) ?>: <?= e((string)$row['count']) ?></li>
        <?php endforeach; ?>
    </ul>

    <h2>Accounts</h2>
    <p>Total accounts: <strong><?= $totalAccounts ?></strong></p>
    <p>Total balance in bank: <strong><?= e(number_format($totalBalance, 2, ',', ' ')) ?> kr</strong></p>
    <table border="1" cellpadding="6">
        <thead>
            <tr><th>Type</th><th>Count</th><th>Total balance</th></tr>
        </thead>
        <tbody>
            <?php foreach ($accountsByType as $row): ?>
                <tr>
                    <td><?= e($row['account_type']) ?></td>
                    <td><?= e((string)$row['count']) ?></td>
                    <td><?= e(number_format((float)$row['total'], 2, ',', ' ')) ?> kr</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Transactions</h2>
    <p>Total: <strong><?= $totalTransactions ?></strong></p>
    <table border="1" cellpadding="6">
        <thead>
            <tr><th>Type</th><th>Count</th><th>Total amount</th></tr>
        </thead>
        <tbody>
            <?php foreach ($transactionsByType as $row): ?>
                <tr>
                    <td><?= e($row['type']) ?></td>
                    <td><?= e((string)$row['count']) ?></td>
                    <td><?= e(number_format((float)$row['total'], 2, ',', ' ')) ?> kr</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Recent transactions</h3>
    <?php if (empty($recentTransactions)): ?>
        <p><em>No transactions yet.</em></p>
    <?php else: ?>
        <table border="1" cellpadding="6">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentTransactions as $t): ?>
                    <tr>
                        <td><?= e((string)$t['id']) ?></td>
                        <td><?= e($t['type']) ?></td>
                        <td><?= e(number_format((float)$t['amount'], 2, ',', ' ')) ?> kr</td>
                        <td><?= $t['from_account_id'] !== null ? e((string)$t['from_account_id']) : '—' ?></td>
                        <td><?= $t['to_account_id']   !== null ? e((string)$t['to_account_id'])   : '—' ?></td>
                        <td><?= e($t['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>