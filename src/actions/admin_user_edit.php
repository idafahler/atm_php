<?php
declare(strict_types=1);

verify_csrf();

$id = (int)($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$role = $_POST['role'] ?? 'user';

$_SESSION['old']['name'] = $name;
$_SESSION['old']['role'] = $role;

$user = $userRepo->findById($id);
if (!$user) {
    flash_set('error', 'User not found.');
    redirect('/admin/users');
}

if(!validate_name($name)){
    flash_set('error', 'Name is not valid.');
    redirect('/admin/users/edit?id=' . $id);
}

if (!in_array($role, ['user', 'admin'], true)) {
    flash_set('error', 'Invalid role.');
    redirect('/admin/users/edit?id=' . $id);
}

if ($id === current_user_id() && $role !== 'admin') {
    flash_set('error', 'You cannot demote yourself.');
    redirect('/admin/users/edit?id=' . $id);
}

if($user['role'] === 'admin' && $role !== 'admin' && $userRepo->countByRole('admin') <= 1){
    flash_set('error', 'Cannot demote the last admin.');
    redirect('/admin/users/edit?id=' . $id);
}

$userRepo->update($id, $name, $role);

unset($_SESSION['old']);
flash_set('success', 'User updated.');
redirect('/admin/users');