<?php
declare(strict_types=1);

verify_csrf();

$userId = (int)($_POST['id'] ?? 0);
$user = $userRepo ->findById($userId);

if(!$user){
    flash_set('error', 'User not found');
    redirect('/admin/users');
}

do{
    $newCard = generate_card_number();
} while ($userRepo->cardNumberExists($newCard));

$userRepo->updateCardNumber($userId, $newCard);

flash_set('success', 'New card number for ' . $user['name']);
redirect('/admin/users');