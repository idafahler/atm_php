<?php
declare(strict_types=1);

verify_csrf();

$cardNumber = trim($_POST['card_number'] ?? '');
$pin = $_POST['pin'] ?? '';

$_SESSION['old']['card_number'] = $cardNumber;

if($cardNumber === '' || $pin === '') {
    flash_set('error', 'Enter both card number and PIN.');
    redirect('/login');
}

$user = $userRepo->findByCardNumber($cardNumber);

if(!verify_login($user, $pin)){
    flash_set('error', 'Invalid card number or PIN.');
    redirect('/login');
}

session_regenerate_id(true);

$_SESSION['last_activity'] = time();

$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['user_role'] = $user['role'];
$_SESSION['user_name'] = $user['name'];

unset($_SESSION['old']);

redirect('/dashboard');