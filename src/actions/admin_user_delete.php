<?php
declare(strict_types=1);

verify_csrf();

$id = (int)($_POST['id'] ?? 0);

if ($id === current_user_id()) {
    flash_set('error', 'You cannot delete yourself.');
    redirect('/admin/users');
}

$user = $userRepo->findById($id);
if (!$user) {
    flash_set('error', 'User not found.');
    redirect('/admin/users');
}

$accounts = $accountRepo->findByUserId($id);
if (!empty($accounts)) {
    flash_set('error', 'Cannot delete user with existing accounts.');
    redirect('/admin/users');
}

if($user['role'] === 'admin' && $userRepo->countByRole('admin') <= 1){
    flash_set('error', 'Cannot delete the last admin.');
    redirect('/admin/users');
}

$userRepo->delete($id);

flash_set('success', 'User deleted.');
redirect('/admin/users');