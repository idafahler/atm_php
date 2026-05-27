<?php
declare(strict_types=1);

ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict'); 
ini_set('session.use_strict_mode', '1');


session_start();

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/userrepository.php';
require_once __DIR__ . '/../src/accountrepository.php';
require_once __DIR__ . '/../src/transactionrepository.php';

enforce_session_timeout();

$pdo = connect();
$userRepo = new UserRepository($pdo);
$accountRepo = new AccountRepository($pdo);
$transactionRepo = new TransactionRepository($pdo);

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

switch($path){

    case '/':
        redirect(current_user_id() ? '/dashboard' : '/login');
        break;
    
    case '/login':
        if($method === 'POST'){
            require __DIR__ . '/../src/actions/login.php';
        } else {
            require __DIR__ . '/../templates/login.php';
        }
        break;

    case '/dashboard':
        require_auth();
        require __DIR__ . '/../templates/dashboard.php';
        break;

    case '/withdraw':
        require_auth();
        if ($method === 'POST') {
            require __DIR__ . '/../src/actions/withdraw.php';
        } else {
            require __DIR__ . '/../templates/withdraw.php';
        }
        break;

    case '/deposit':
        require_auth();
        if ($method === 'POST') {
            require __DIR__ . '/../src/actions/deposit.php';
        } else {
            require __DIR__ . '/../templates/deposit.php';
        }
        break;

    case '/transfer':
        require_auth();
        if ($method === 'POST') {
            require __DIR__ . '/../src/actions/transfer.php';
        } else {
            require __DIR__ . '/../templates/transfer.php';
        }
        break;

    case '/transactions':
        require_auth();
        require __DIR__ . '/../templates/transactions.php';
        break;

    case '/account':
        require_auth();
        require __DIR__ . '/../templates/account.php';
        break;

    case '/account/change-pin':
        require_auth();
        if ($method === 'POST') {
            require __DIR__ . '/../src/actions/account_change_pin.php';
        }
        break;

    case '/account/regenerate-card':
        require_auth();
        if ($method === 'POST') {
            require __DIR__ . '/../src/actions/account_regenerate_card.php';
        }
        break;

    case '/accounts/create':
        require_auth();
        if ($method === 'POST') {
            require __DIR__ . '/../src/actions/createaccount.php';
        } else {
            require __DIR__ . '/../templates/createaccount.php';
        }
        break;

    case '/accounts/edit':
        require_auth();
        if($method === 'POST'){
            require __DIR__ . '/../src/actions/account_edit.php';
        } else{
            require __DIR__ . '/../templates/account_edit.php';
        }
        break;
    
    case '/accounts/transactions':
        require_auth();
        require __DIR__ . '/../templates/account_transactions.php';
        break;

    case '/admin':
        require_role('admin');
        require __DIR__ . '/../templates/admin/admin_dashboard.php';
        break;

    case '/admin/users':
        require_role('admin');
        require __DIR__ . '/../templates/admin/admin_users.php';
        break;

    case '/admin/users/view':
        require_role('admin');
        require __DIR__ . '/../templates/admin/admin_user_view.php';
        break;

    case '/admin/users/create':
        require_role('admin');
        if ($method === 'POST') {
            require __DIR__ . '/../src/actions/admin_user_create.php';
        } else {
            require __DIR__ . '/../templates/admin/admin_user_create.php';
        }
        break;

    case '/admin/users/edit':
        require_role('admin');
        if ($method === 'POST') {
            require __DIR__ . '/../src/actions/admin_user_edit.php';
        } else {
            require __DIR__ . '/../templates/admin/admin_user_edit.php';
        }
        break;

    case '/admin/users/regenerate-card':
        require_role('admin');
        if($method === 'POST'){
            require __DIR__ . '/../src/actions/admin_regenerate_card.php';
        }
        break;

    case '/admin/users/reset-pin':
        require_role('admin');
        if($method === 'POST') {
            require __DIR__ . '/../src/actions/admin_user_reset_pin.php';
        }
        break;

    case '/admin/users/delete':
        require_role('admin');
        if($method === 'POST'){
            require __DIR__ . '/../src/actions/admin_user_delete.php';
        }
        break;

    case '/admin/accounts':
        require_role('admin');
        require __DIR__ . '/../templates/admin/admin_accounts.php';
        break;

    case '/admin/accounts/create':
        require_role('admin');
        if ($method === 'POST') {
            require __DIR__ . '/../src/actions/admin_account_create.php';
        } else {
            require __DIR__ . '/../templates/admin/admin_account_create.php';
        }
        break;

    case '/admin/accounts/delete':
        require_role('admin');
        if($method === 'POST'){
            require __DIR__ . '/../src/actions/admin_account_delete.php';
        }
        break;

    case '/admin/transactions':
        require_role('admin');
        require __DIR__ . '/../templates/admin/admin_transactions.php';
        break;

    case '/logout':
        require __DIR__ . '/../src/actions/logout.php';
        break;

    default:
        http_response_code(404);
        echo '404 Not found';
}