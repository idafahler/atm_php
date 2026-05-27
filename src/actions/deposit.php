<?php
declare(strict_types=1);

verify_csrf();

$accountId = (int)($_POST['account_id'] ?? 0);
$amount = (float)($_POST['amount'] ?? 0);

$_SESSION['old']['account_id'] = (string)$accountId;
$_SESSION['old']['amount'] = (string)$amount;

if($amount <= 0){
    flash_set('error', 'Amount must be greater than 0.');
    redirect('/deposit');
}

$account = $accountRepo->findById($accountId);

if(!$account || (int)$account['user_id'] !== current_user_id()){
    flash_set('error', 'Invalid account');
    redirect('/deposit');
}

$pdo->beginTransaction();
try{
    $newBalance = (float)$account['balance'] + $amount;
    $accountRepo->updateBalance($accountId, $newBalance);
    $transactionRepo->create('deposit', $amount, null, $accountId);
    $pdo->commit();
} catch(Throwable $e){
    $pdo->rollBack();
    flash_set('error', 'Something went wrong. Please try again.');
    redirect('/deposit');
}

unset($_SESSION['old']);
flash_set('success', 'Deposit completed');
redirect('/dashboard');