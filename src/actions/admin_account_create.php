<?php
declare(strict_types=1);

verify_csrf();

$userId = (int)($_POST['user_id'] ?? 0);
$accountType = $_POST['account_type'] ?? '';
$accountName = trim($_POST['account_name'] ?? '');

$_SESSION['old']['user_id']      = (string)$userId;
$_SESSION['old']['account_type'] = $accountType;
$_SESSION['old']['account_name'] = $accountName;

if (!in_array($accountType, ['checking', 'savings'], true)) {
    flash_set('error', 'Invalid account type.');
    redirect('/admin/accounts/create');
}

$user = $userRepo->findById($userId);
if (!$user) {
    flash_set('error', 'User not found.');
    redirect('/admin/accounts/create');
}

if ($user['role'] !== 'admin') {
    $existing = $accountRepo->findByUserId($userId);
    if (count($existing) >= 5) {
        flash_set('error', 'This user already has the maximum number of accounts (5).');
        redirect('/admin/accounts/create');
    }
}

$accountRepo->create(
    userId: $userId,
    accountType: $accountType,
    accountName: $accountName ?: null
);

unset($_SESSION['old']);
flash_set('success', 'Account created for ' . $user['name'] . '.');
redirect('/admin/accounts');