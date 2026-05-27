<?php
declare(strict_types=1);

verify_csrf();

$currentPin = $_POST['current_pin'] ?? '';
$newPin = $_POST['new_pin'] ?? '';
$confirmPin = $_POST['confirm_pin'] ?? '';

$user = $userRepo->findById(current_user_id());

if (!$user || !password_verify($currentPin, $user['pin_hash'])) {
    flash_set('error', 'Current PIN is incorrect.');
    redirect('/account');
}

if (!validate_pin($newPin)) {
    flash_set('error', 'New PIN must be exactly 4 digits.');
    redirect('/account');
}

if ($newPin !== $confirmPin) {
    flash_set('error', 'New PIN and confirmation do not match.');
    redirect('/account');
}

if ($newPin === $currentPin) {
    flash_set('error', 'New PIN must be different from current PIN.');
    redirect('/account');
}

$userRepo->updatePin(current_user_id(), $newPin);

flash_set('success', 'PIN changed.');
redirect('/account');