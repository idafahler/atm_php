<?php
declare(strict_types=1);

verify_csrf();

$accountId = (int)($_POST['id'] ?? 0);
$accountName = trim($_POST['account_name'] ?? '');

$account = $accountRepo->findById($accountId);

if (!$account) {
    flash_set('error', 'Account not found.');
    redirect('/dashboard');
}

if (current_user_role() !== 'admin' && (int)$account['user_id'] !== current_user_id()) {
    flash_set('error', 'Account not found.');
    redirect('/dashboard');
}

if(!validate_name($accountName)){
    flash_set('error', 'Invalid account name.');
    redirect('/accounts/edit?id=' . $accountId);
}

$accountRepo->updateName($accountId, $accountName !== '' ? $accountName : null);

flash_set('success', 'Account updated.');
redirect('/dashboard');