<?php
declare(strict_types=1);

class AccountRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM accounts WHERE id = :id');
        $stmt->bindvalue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $account = $stmt->fetch();
        return $account ?: null;
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM accounts WHERE user_id = :user_id ORDER BY id');
        $stmt->bindvalue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function all(): array
    {
        $stmt = $this->pdo->query(
            'SELECT a.id, a.user_id, u.name AS owner_name, a.account_type, a.account_name, a.balance
            FROM accounts a
            JOIN users u ON u.id = a.user_id
            ORDER BY a.id'
        );
        return $stmt->fetchAll();
    }

    public function create(int $userId, string $accountType, float $balance = 0.0, ?string $accountName = null): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO accounts (user_id, account_type, account_name, balance)
             VALUES (:user_id, :account_type, :account_name, :balance)'
        );
        $stmt->execute([
            ':user_id'      => $userId,
            ':account_type' => $accountType,
            ':account_name' => $accountName,
            ':balance'      => $balance,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function updateBalance(int $id, float $newBalance): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE accounts SET balance = :balance WHERE id = :id'
        );
        $stmt->execute([
            ':balance' => $newBalance,
            ':id'      => $id,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function updateName(int $accountId, ?string $accountName): void 
    {
        $stmt = $this->pdo->prepare(
            'UPDATE accounts SET account_name = :name WHERE id = :id'
        );
        $stmt->execute([
            ':name' => $accountName, 
            ':id' => $accountId
        ]);
    }

    public function delete(int $accountId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM accounts WHERE id = ?');
        $stmt->execute([$accountId]);
    }

    public function findByIdWithOwner(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT a.id, a.user_id, a.account_type, a.account_name, a.balance, u.name AS owner_name
            FROM accounts a
            JOIN users u ON u.id = a.user_id
            WHERE a.id = :id'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $account = $stmt->fetch();
        return $account ?: null;
    }

    public function findAllExceptUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT a.id, a.account_type, a.account_name, a.balance, u.id AS owner_id, u.name as owner_name
            FROM accounts a
            JOIN users u ON u.id = a.user_id
            WHERE a.user_id != :user_id
            ORDER BY u.name, a.account_type'
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countByType(): array
    {
        $stmt = $this->pdo->query(
        'SELECT account_type, COUNT(*) AS count, SUM(balance) AS total
        FROM accounts
        GROUP BY account_type'
        );
        return $stmt->fetchAll();
    }

    public function totalBalance(): float
    {
        return (float)$this->pdo->query('SELECT SUM(balance) FROM accounts')->fetchColumn();
    }

    public function countAll(): int
    {
        return (int)$this->pdo->query('SELECT COUNT(*) FROM accounts')->fetchColumn();
    }
}