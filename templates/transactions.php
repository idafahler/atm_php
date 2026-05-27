<?php
$filters = [
    'user_id' => current_user_id(),
    'type' => $_GET['type'] ?? '',
    'from_date' => $_GET['from_date'] ?? '',
    'to_date' => $_GET['to_date'] ?? '',
];

$dateError = null;
if ($filters['from_date'] !== '' && $filters['to_date'] !== ''
    && $filters['from_date'] > $filters['to_date']) {
    $dateError = 'From-date cannot be after to-date.';
    $filters['from_date'] = '';
    $filters['to_date'] = '';
}

$perPage = 20;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$transactions = $transactionRepo->filter($filters, $perPage, $offset);
$total = $transactionRepo->countFiltered($filters);
$totalPages = (int)ceil($total / $perPage);

if ($page > $totalPages && $totalPages > 0) {
    redirect('/transactions?page=' . $totalPages);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My transactions</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h1>My transactions</h1>

    <?php require __DIR__ . '/nav/nav_user.php'; ?>

    <?php if ($dateError): ?>
        <p class="error"><?= e($dateError) ?></p>
    <?php endif; ?>

    <h2>Filter transactions</h2>

    <form method="GET" action="/transactions">
        <label>
            Type
            <select name="type">
                <option value="">All</option>
                <option value="deposit" <?= $filters['type'] === 'deposit' ? 'selected' : '' ?>>Deposit</option>
                <option value="withdrawal" <?= $filters['type'] === 'withdrawal' ? 'selected' : '' ?>>Withdrawal</option>
                <option value="transfer" <?= $filters['type'] === 'transfer' ? 'selected' : '' ?>>All transfers</option>
                <option value="transfer_internal" <?= $filters['type'] === 'transfer_internal' ? 'selected' : '' ?>>Internal transfers</option>
                <option value="transfer_external" <?= $filters['type'] === 'transfer_external' ? 'selected' : '' ?>>External transfers</option>
            </select>
        </label>

        <label>
            From
            <input type="date" name="from_date" value="<?= e($filters['from_date']) ?>">
        </label>

        <label>
            To
            <input type="date" name="to_date" value="<?= e($filters['to_date']) ?>">
        </label>

        <button type="submit">Filter</button>
        <a href="/transactions" class="btn btn-secondary">Clear</a>
    </form>

    <h2>Transactions</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Amount</th>
                <th>From account</th>
                <th>To account</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $t): ?>
                <tr>
                    <td><?= e((string)$t['id']) ?></td>
                    <td><?= e($t['type']) ?></td>
                    <td><?= e(number_format((float)$t['amount'], 2, ',', ' ')) ?> kr</td>
                    <td><?= e($t['from_account_id'] !== null ? (string)$t['from_account_id'] : '-') ?></td>
                    <td><?= e($t['to_account_id'] !== null ? (string)$t['to_account_id'] : '-') ?></td>
                    <td><?= e($t['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
        <nav class="pagination">
            <?php
            $queryParams = $_GET;
            unset($queryParams['user_id']);

            $prevParams = $queryParams;
            $prevParams['page'] = $page - 1;

            $nextParams = $queryParams;
            $nextParams['page'] = $page + 1;
            ?>
            <?php if ($page > 1): ?>
                <a href="?<?= http_build_query($prevParams) ?>">&laquo; Previous</a>
            <?php endif; ?>

            <span>Page <?= $page ?> of <?= $totalPages ?></span>

            <?php if ($page < $totalPages): ?>
                <a href="?<?= http_build_query($nextParams) ?>">Next &raquo;</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
    <p>Showing <?= count($transactions) ?> of <?= $total ?> transactions</p>
</body>
</html>