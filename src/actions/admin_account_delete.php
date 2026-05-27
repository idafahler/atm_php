<?php
declare(strict_types=1);

verify_csrf();

$accountId = (int)($_POST['id'] ?? 0);
$account = $accountRepo->findById($accountId);

if (!$account) {
    flash_set('error', 'Account not found.');
    redirect('/admin/accounts');
}

if ((float)$account['balance'] !== 0.0) {
    flash_set('error', 'Account must have 0 balance before deletion.');
    redirect('/admin/accounts');
}

$accountRepo->delete($accountId);

flash_set('success', 'Account deleted.');
redirect('/admin/accounts');