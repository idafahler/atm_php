<?php
declare(strict_types=1);

verify_csrf();

$name = trim($_POST['name'] ?? '');
$pin  = $_POST['pin'] ?? '';
$role = $_POST['role'] ?? 'user';

$_SESSION['old']['name'] = $name;
$_SESSION['old']['role'] = $role;

if ($name === '' || $pin === '') {
    flash_set('error', 'All fields are required.');
    redirect('/admin/users/create');
}

if (!validate_name($name)) {
    flash_set('error', 'Invalid name.');
    redirect('/admin/users/create');
}

if (!validate_pin($pin)) {
    flash_set('error', 'Pin needs to be 4 digits.');
    redirect('/admin/users/create');
}

if (!in_array($role, ['user', 'admin'], true)) {
    flash_set('error', 'Invalid role.');
    redirect('/admin/users/create');
}

do {
    $cardNumber = generate_card_number();
} while ($userRepo->cardNumberExists($cardNumber));

$pdo->beginTransaction();
try {
    $userId = $userRepo->create($cardNumber, $pin, $name, $role);
    $accountRepo->create($userId, 'checking', 0.00);
    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollback();
    flash_set('error', 'Something went wrong. Try again.');
    redirect('/admin/users/create');
}

unset($_SESSION['old']);
flash_set('success', 'User created.');
redirect('/admin/users');