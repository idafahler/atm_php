<div class="user-info">
    Logged in as <strong><?= e($_SESSION['user_name']) ?></strong>
    (<?= e($_SESSION['user_role']) ?>)
</div>

<nav>
    <a href="/dashboard">Back to dashboard</a> |
    <a href="/admin">Statistics</a> |
    <a href="/admin/users">Users</a> |
    <a href="/admin/accounts">Accounts</a> |
    <a href="/admin/transactions">Transactions</a> |
    <a href="/logout">Log out</a>
</nav>