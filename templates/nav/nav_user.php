<div class="user-info">
    Logged in as <strong><?= e($_SESSION['user_name']) ?></strong>
    (<?= e($_SESSION['user_role']) ?>)
</div>

<nav>
    <a href="/dashboard">Overview</a> |
    <a href="/deposit">Deposit</a> |
    <a href="/withdraw">Withdraw</a> |
    <a href="/transfer">Transfer</a> |
    <a href="/transactions">My transactions</a> |
    <a href="/account">My account</a> |
    <a href="/logout">Log out</a>
</nav>

<?php if (current_user_role() === 'admin'): ?>
    <nav>
        <a href="/admin">Admin panel</a>
    </nav>
<?php endif; ?>