<?php
declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string 
{
    if(empty($_SESSION['csrf_token'])){
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void 
{
    $token = $_POST['csrf_token'] ?? '';
    $stored = $_SESSION['csrf_token'] ?? '';

    if($stored === '' || !is_string($token) || !hash_equals($stored, $token)) {
        http_response_code(403);
        exit('Invalid CSRF token');
    }
}

function current_user_id(): ?int 
{
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

function current_user_role(): ?string 
{
    return $_SESSION['user_role'] ?? null;
}

function require_auth(): void 
{
    if(current_user_id() === null){
        header('Location: /login');
        exit;
    }
}

function require_role(string $role): void 
{
    require_auth();
    if(current_user_role() !== $role){
        http_response_code(403);
        exit('403 Forbidden');
    }
}

function redirect(string $path): void 
{
    header('Location: ' . $path);
    exit;
}

function enforce_session_timeout(int $maxIdleSeconds = 1800): void 
{
    if (current_user_id() === null) {
        return;
    }

    $now = time();
    $lastActivity = $_SESSION['last_activity'] ?? null;

    if ($lastActivity !== null && ($now - $lastActivity) > $maxIdleSeconds) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
        session_start();
        redirect('/login');
    }

    $_SESSION['last_activity'] = $now;
}

function generate_card_number(): string 
{
    $digits = '';
    $cardNumberLen = 12;

    for($i = 0; $i < $cardNumberLen -1; $i++){
        $digits .= random_int(0,9);
    }

    $sum = 0;
    $double = true;

    for($i = strlen($digits) - 1; $i >= 0; $i--){
        $digit = (int)$digits[$i];

        if($double){
            $digit *= 2;
            if($digit > 9){
                $digit -= 9;
            }
        }
        $sum += $digit;
        $double = !$double;
    }

    $remainder = $sum % 10;

    if ($remainder === 0) {
        $checkDigit = 0;
    } else { 
        $checkDigit = 10 - $remainder;
    }

    $cardNumber = $digits . $checkDigit;

    if (!luhn_check($cardNumber)) {
        throw new RuntimeException('Produced invalid Luhn.');
    }

    return $cardNumber;
}

function luhn_check(string $cardNumber): bool 
{
    if(strlen($cardNumber) !== 12){
        return false;
    }

    $sum = 0;
    $double = false;
    
    for($i = strlen($cardNumber) - 1; $i >= 0; $i--){
        $digit = (int)$cardNumber[$i];

        if($double){ 
            $digit *= 2;
            if($digit > 9){
                $digit -= 9;
            }
        }

        $sum += $digit;
        $double = !$double;
    }
    return $sum % 10 === 0;
}

function validate_pin(string $pin): bool 
{
    if(preg_match('/^\d{4}$/', $pin)){
        return true;
    }
    return false;
}

function validate_name(string $name): bool 
{
    $length = mb_strlen($name); 
    if($length < 2 || $length > 100){
        return false;
    }
    
    return preg_match('/^[\p{L} \'-]+$/u', $name) === 1; 
}

function verify_login(?array $user, string $pin): bool 
{
    return $user !== null && password_verify($pin, $user['pin_hash']);
}

function old(string $key, string $default = ''): string 
{
    $value = $_SESSION['old'][$key] ?? $default;
    return is_string($value) ? $value : $default;
}

function flash_set(string $key, string $message): void 
{
    $_SESSION['flash'][$key] = $message;
}

function flash_get(string $key): ?string 
{
    $message = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $message;
}