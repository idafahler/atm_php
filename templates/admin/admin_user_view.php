<?php
$userId = (int)($_GET['id'] ?? 0);
$user = $userRepo->findById($userId);

if (!$user) {
    flash_set('error', 'User not found.');
    redirect('/admin/users');
}

$accounts = $accountRepo->findByUserId($userId);

$filters = [
    'user_id' => $userId,
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
    redirect('/admin/users/view?id=' . $userId . '&page=' . $totalPages);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User: <?= e($user['name']) ?></title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h1><?= e($user['name']) ?></h1>

    <?php require __DIR__ . '/../nav/nav_admin.php'; ?>

    <h2>User information</h2>

    <p>
        <strong>Name:</strong> <?= e($user['name']) ?><br>
        <strong>Card number:</strong> <?= e($user['card_number']) ?><br>
        <strong>Role:</strong> <?= e($user['role']) ?><br>
        <strong>Created:</strong> <?= e(substr($user['created_at'], 0, 10)) ?>
    </p>

    <p>
        <a href="/admin/users/edit?id=<?= e((string)$userId) ?>" class="btn">Edit user</a>
    </p>

    <h2>Accounts</h2>

    <?php if (empty($accounts)): ?>
        <p>This user has no accounts.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Balance</th>
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
                        <td><?= e($a['account_type']) ?></td>
                        <td><?= e($a['account_name'] ?? '-') ?></td>
                        <td><?= e(number_format((float)$a['balance'], 2, ',', ' ')) ?> kr</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h2>Filter transactions</h2>

    <?php if ($dateError): ?>
        <p class="error"><?= e($dateError) ?></p>
    <?php endif; ?>

    <form method="GET" action="/admin/users/view">
        <input type="hidden" name="id" value="<?= e((string)$userId) ?>">

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
        <a href="/admin/users/view?id=<?= e((string)$userId) ?>" class="btn btn-secondary">Clear</a>
    </form>

    <h2>Transactions</h2>

    <?php if (empty($transactions)): ?>
        <p>No transactions to show.</p>
    <?php else: ?>
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
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
        <nav class="pagination">
            <?php
            $queryParams = $_GET;
            $prevParams = $queryParams;
            $prevParams['page'] = $page - 1;
            $nextParams = $queryParams;
            $nextParams['page'] = $page + 1;
            ?>
            <?php if ($page > 1): ?>
                <a href="?<?= http_build_query($prevParams) ?>">Previous</a>
            <?php endif; ?>

            <span>Page <?= $page ?> of <?= $totalPages ?></span>

            <?php if ($page < $totalPages): ?>
                <a href="?<?= http_build_query($nextParams) ?>">Next</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>

    <p>Showing <?= count($transactions) ?> of <?= $total ?> transactions</p>
</body>
</html>