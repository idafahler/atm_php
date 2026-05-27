<?php
declare(strict_types=1);

require_once __DIR__ . '/src/db.php';
require_once __DIR__ . '/src/helpers.php';
require_once __DIR__ . '/src/userrepository.php';
require_once __DIR__ . '/src/accountrepository.php';
require_once __DIR__ . '/src/transactionrepository.php';

try {
    $initPdo = new PDO(
        'mysql:host=localhost;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $initPdo->exec('CREATE DATABASE IF NOT EXISTS atmdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $initPdo->exec('USE atmdb');

    $schema = file_get_contents(__DIR__ . '/schema.sql');
    if ($schema === false) {
        throw new RuntimeException('Could not read schema.sql');
    }

    $initPdo->exec($schema);
} catch (Throwable $e) {
    echo "Error initializing database: " . $e->getMessage() . "\n";
    exit(1);
}

$pdo = connect();
$userRepo = new UserRepository($pdo);
$accountRepo = new AccountRepository($pdo);

$admins = [
    ['card' => '657534096562', 'pin' => '0000', 'name' => 'Admin Adminsson'],
    ['card' => '891823024734', 'pin' => '9999', 'name' => 'Admina Adminsson'],
];

foreach ($admins as $a) {
    $userRepo->create($a['card'], $a['pin'], $a['name'], 'admin');
}

$users = [
    ['card' => '888519486094', 'pin' => '1234', 'name' => 'Anna Andersson',     'checking' => 5230.50,  'savings' => 18750.00],
    ['card' => '404296523412', 'pin' => '4321', 'name' => 'Björn Berg',         'checking' => 1200.00,  'savings' => 42000.75],
    ['card' => '812889656060', 'pin' => '1111', 'name' => 'Cecilia Carlsson',   'checking' => 850.25,   'savings' => 9300.00],
    ['card' => '575499033323', 'pin' => '2222', 'name' => 'David Dahl',         'checking' => 15400.00, 'savings' => 3200.50],
    ['card' => '883287214091', 'pin' => '3333', 'name' => 'Eva Eriksson',       'checking' => 320.00,   'savings' => 67500.00],
];

$userIds = [];
foreach ($users as $u) {
    $userId = $userRepo->create($u['card'], $u['pin'], $u['name'], 'user');
    $checkingId = $accountRepo->create($userId, 'checking', $u['checking']);
    $savingsId = $accountRepo->create($userId, 'savings', $u['savings']);

    $userIds[] = [
        'user_id' => $userId,
        'name' => $u['name'],
        'checking_id' => $checkingId,
        'savings_id' => $savingsId,
    ];
}

$now = time();

foreach ($userIds as $u) {
    $depositCount = random_int(5, 10);
    for ($i = 0; $i < $depositCount; $i++) {
        $amount = round(random_int(100, 5000) + random_int(0, 99) / 100, 2);
        $accountId = random_int(0, 1) ? $u['checking_id'] : $u['savings_id'];
        $daysAgo = random_int(0, 60);
        $createdAt = date('Y-m-d H:i:s', $now - $daysAgo * 86400 - random_int(0, 86400));

        seed_transaction($pdo, 'deposit', $amount, null, $accountId, $createdAt);
    }

    $withdrawCount = random_int(3, 7);
    for ($i = 0; $i < $withdrawCount; $i++) {
        $amount = round(random_int(50, 1500) + random_int(0, 99) / 100, 2);
        $accountId = random_int(0, 1) ? $u['checking_id'] : $u['savings_id'];
        $daysAgo = random_int(0, 60);
        $createdAt = date('Y-m-d H:i:s', $now - $daysAgo * 86400 - random_int(0, 86400));

        seed_transaction($pdo, 'withdrawal', $amount, $accountId, null, $createdAt);
    }

    $internalCount = random_int(2, 5);
    for ($i = 0; $i < $internalCount; $i++) {
        $amount = round(random_int(100, 2000) + random_int(0, 99) / 100, 2);
        $fromId = random_int(0, 1) ? $u['checking_id'] : $u['savings_id'];
        $toId = $fromId === $u['checking_id'] ? $u['savings_id'] : $u['checking_id'];
        $daysAgo = random_int(0, 60);
        $createdAt = date('Y-m-d H:i:s', $now - $daysAgo * 86400 - random_int(0, 86400));

        seed_transaction($pdo, 'transfer', $amount, $fromId, $toId, $createdAt);
    }
}

$externalCount = 15;
for ($i = 0; $i < $externalCount; $i++) {
    $fromUser = $userIds[array_rand($userIds)];
    do {
        $toUser = $userIds[array_rand($userIds)];
    } while ($toUser['user_id'] === $fromUser['user_id']);

    $amount = round(random_int(100, 3000) + random_int(0, 99) / 100, 2);
    $fromId = random_int(0, 1) ? $fromUser['checking_id'] : $fromUser['savings_id'];
    $toId = random_int(0, 1) ? $toUser['checking_id'] : $toUser['savings_id'];
    $daysAgo = random_int(0, 60);
    $createdAt = date('Y-m-d H:i:s', $now - $daysAgo * 86400 - random_int(0, 86400));

    seed_transaction($pdo, 'transfer', $amount, $fromId, $toId, $createdAt);
}

echo "Seed complete.\n";

function seed_transaction(PDO $pdo, string $type, float $amount, ?int $fromId, ?int $toId, string $createdAt): void
{
    $stmt = $pdo->prepare(
        'INSERT INTO transactions (type, amount, from_account_id, to_account_id, created_at)
         VALUES (:type, :amount, :from, :to, :created_at)'
    );
    $stmt->execute([
        ':type' => $type,
        ':amount' => $amount,
        ':from' => $fromId,
        ':to' => $toId,
        ':created_at' => $createdAt,
    ]);
}