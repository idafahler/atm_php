<?php
declare(strict_types=1);

verify_csrf();

$accountType = $_POST['account_type'] ?? '';
$accountName = trim($_POST['account_name'] ?? '');

$_SESSION['old']['account_type'] = $accountType;
$_SESSION['old']['account_name'] = $accountName;

if (!in_array($accountType, ['checking', 'savings'], true)) {
    flash_set('error', 'Invalid account type.');
    redirect('/accounts/create');
}
if(current_user_role() !== 'admin'){
    $existing = $accountRepo->findByUserId(current_user_id());
    if (count($existing) >= 5) {
        flash_set('error', 'You already have the maximum number of accounts.');
        redirect('/accounts/create');
    }
}

$accountRepo->create(
    userId: current_user_id(),
    accountType: $accountType,
    accountName: $accountName ?: null
);

unset($_SESSION['old']);
flash_set('success', 'New ' . $accountType . ' account opened.');
redirect('/dashboard');