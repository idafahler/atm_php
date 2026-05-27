<?php
declare(strict_types=1);

verify_csrf();

$transferType = $_POST['transfer_type'] ?? '';
$fromId = (int)($_POST['from_account_id'] ?? 0);
$toId   = (int)($_POST['to_account_id'] ?? 0);
$amount = (float)($_POST['amount'] ?? 0);

$_SESSION['old']['from_account_id'] = (string)$fromId;
$_SESSION['old']['to_account_id']   = (string)$toId;
$_SESSION['old']['amount']          = (string)$amount;

if (!in_array($transferType, ['own', 'external'], true)) {
    flash_set('error', 'Invalid transfer type.');
    redirect('/transfer');
}

if ($amount <= 0) {
    flash_set('error', 'Amount must be greater than 0.');
    redirect('/transfer');
}

if ($fromId === $toId) {
    flash_set('error', 'From and to account must be different.');
    redirect('/transfer');
}

$from = $accountRepo->findById($fromId);

if (!$from || (int)$from['user_id'] !== current_user_id()) {
    flash_set('error', 'Invalid source account.');
    redirect('/transfer');
}

if ($transferType === 'own') {
    $to = $accountRepo->findById($toId);
    if (!$to || (int)$to['user_id'] !== current_user_id()) {
        flash_set('error', 'Invalid destination account.');
        redirect('/transfer');
    }
    $successMessage = 'Transfer completed.';
} else {
    $to = $accountRepo->findByIdWithOwner($toId);
    if (!$to) {
        flash_set('error', 'Destination account does not exist.');
        redirect('/transfer');
    }
    $successMessage = 'Transferred ' . number_format($amount, 2, ',', ' ')
                    . ' kr to ' . $to['owner_name'] . '.';
}

if ($amount > (float)$from['balance']) {
    flash_set('error', 'Insufficient funds.');
    redirect('/transfer');
}

$pdo->beginTransaction();
try {
    $accountRepo->updateBalance($fromId, (float)$from['balance'] - $amount);
    $accountRepo->updateBalance($toId,   (float)$to['balance']   + $amount);
    $transactionRepo->create('transfer', $amount, $fromId, $toId);
    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    flash_set('error', 'Something went wrong. Please try again.');
    redirect('/transfer');
}

unset($_SESSION['old']);
flash_set('success', $successMessage);
redirect('/dashboard');