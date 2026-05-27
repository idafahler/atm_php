<?php
declare(strict_types=1);

verify_csrf();

$userId = (int)($_POST['id'] ?? 0);
$pin = $_POST['pin'] ?? '';

$user = $userRepo->findById($userId);

if(!$user){
    flash_set('error', 'User not found.');
    redirect('/admin/users');
}

if(!validate_pin($pin)){
    flash_set('error', 'PIN must be exactly 4 digits.');
    redirect('/admin/users/edit?id=' . $userId);
}

$userRepo->updatePin($userId, $pin);

flash_set('success', 'New PIN for ' . $user['name'] . '.');
redirect('/admin/users');