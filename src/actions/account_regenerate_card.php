<?php
declare(strict_types=1);

verify_csrf();

$currentPin = $_POST['current_pin'] ?? '';

$user = $userRepo->findById(current_user_id());

if (!$user || !password_verify($currentPin, $user['pin_hash'])) {
    flash_set('error', 'Current PIN is incorrect.');
    redirect('/account');
}

do {
    $newCard = generate_card_number();
} while ($userRepo->cardNumberExists($newCard));

$userRepo->updateCardNumber(current_user_id(), $newCard);

flash_set('success', 'New card number: ' . $newCard);
redirect('/account');