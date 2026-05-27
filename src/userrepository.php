<?php
declare(strict_types=1);

class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch();
        return $user?: null;
    }

    public function findByCardNumber(string $cardNumber): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE card_number = :card_number');
        $stmt->execute([':card_number' => $cardNumber]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function cardNumberExists(string $cardNumber, ?int $excludeId = null): bool
    {
        if($excludeId === null) {
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE card_number = :card_number');
            $stmt->execute([':card_number' => $cardNumber]);
        } else {
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE card_number = :card_number AND id != :id');
            $stmt->execute([':card_number' => $cardNumber, ':id' => $excludeId]);
        }
        return (bool)$stmt->fetchColumn();
    }

    public function updateCardNumber(int $userId, string $cardNumber): void 
    {
        $stmt = $this->pdo->prepare('UPDATE users SET card_number = ? WHERE id = ?');
        $stmt->execute([$cardNumber, $userId]);
    }

    public function updatePin(int $userId, string $pin): void
    {
        $hash = password_hash($pin, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare('UPDATE users SET pin_hash = ? WHERE id = ?');
        $stmt->execute([$hash, $userId]);
    }

    public function all(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, card_number, name, role, created_at
            FROM users
            ORDER BY id'
        );
        return $stmt->fetchAll();
    }

    public function create(string $cardNumber, string $pin, string $name, string $role = 'user'): int 
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (card_number, pin_hash, name, role)
            VALUES (:card_number, :pin_hash, :name, :role)'
        );
        
        $stmt->execute([
            ':card_number' => $cardNumber,
            ':pin_hash'    => password_hash($pin, PASSWORD_BCRYPT),
            ':name'        => $name,
            ':role'        => $role,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $name, string $role): bool 
    {
        $stmt = $this->pdo->prepare(
            'UPDATE users
            SET name = :name, role = :role
            WHERE id = :id'
        );
        return $stmt->execute([
            ':id'          => $id,
            ':name'        => $name,
            ':role'        => $role,
        ]);
    }

    public function delete(int $id): bool 
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function countByRole(): array 
    {
        $stmt = $this->pdo->query('SELECT role, COUNT(*) AS count FROM users GROUP BY role');
        return $stmt->fetchAll();
    }

    public function countAll(): int 
    {
        return (int)$this->pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }
    
}