<?php
$user = $userRepo->findById(current_user_id());

if (!$user) {
    flash_set('error', 'User not found.');
    redirect('/dashboard');
}

$error = flash_get('error');
$success = flash_get('success');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My account</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h1>My account</h1>

    <?php require __DIR__ . '/nav/nav_user.php'; ?>

    <?php if ($error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p class="success"><?= e($success) ?></p>
    <?php endif; ?>

    <p>
        <strong>Name:</strong> <?= e($user['name']) ?><br>
        <strong>Card number:</strong> <?= e($user['card_number']) ?><br>
        <strong>Role:</strong> <?= e($user['role']) ?>
    </p>

    <h2>Change PIN</h2>

    <form method="POST" action="/account/change-pin">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <label>
            Current PIN
            <input type="password" name="current_pin" pattern="\d{4}" maxlength="4" inputmode="numeric" required>
        </label>

        <label>
            New PIN
            <input type="password" name="new_pin" pattern="\d{4}" maxlength="4" inputmode="numeric" required>
        </label>

        <label>
            Confirm new PIN
            <input type="password" name="confirm_pin" pattern="\d{4}" maxlength="4" inputmode="numeric" required>
        </label>

        <button type="submit">Change PIN</button>
    </form>

    <h2>Request new card number</h2>

    <p>
        Generates a new card number. <br>
        You'll need to use the new number next time you log in.
    </p>

    <form method="POST" action="/account/regenerate-card"
          onsubmit="return confirm('Generate a new card number? You will need to use the new number to log in.');">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <label>
            Current PIN (to confirm)
            <input type="password" name="current_pin" pattern="\d{4}" maxlength="4" inputmode="numeric" required>
        </label>

        <button type="submit">Generate new card number</button>
    </form>
</body>
</html>