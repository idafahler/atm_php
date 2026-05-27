<?php
$error = flash_get('error');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h1>ATM</h1>

    <?php if ($error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="/login">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label>
            Card number
            <input type="text" name="card_number" value="<?= e(old('card_number')) ?>" required>
        </label>

        <label>
            PIN code
            <input type="password" name="pin" maxlength="4" inputmode="numeric" required>
        </label>

        <button type="submit">Log in</button>
    </form>
</body>
</html>