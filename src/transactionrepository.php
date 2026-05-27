<?php
declare(strict_types=1);

class TransactionRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(string $type, float $amount, ?int $fromAccountId, ?int $toAccountId): int 
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO transactions (type, amount, from_account_id, to_account_id)
            VALUES (:type, :amount, :from_account_id, :to_account_id)'
        );

        $stmt->execute([
            ':type' => $type,
            ':amount' => $amount, 
            ':from_account_id' => $fromAccountId,
            ':to_account_id' => $toAccountId
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function findByAccountId(int $accountId): array 
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
            FROM transactions
            WHERE from_account_id = :id OR to_account_id = :id
            ORDER BY created_at DESC, id DESC'
        );
        $stmt->bindValue(':id', $accountId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function all(): array 
    {
        $stmt = $this->pdo->query(
            'SELECT id, type, amount, from_account_id, to_account_id, created_at
            FROM transactions
            ORDER BY created_at DESC, id DESC'
        );
        return $stmt->fetchAll();
    }

    public function countByType(): array
    {
        $stmt = $this->pdo->query(
            'SELECT type, COUNT(*) AS count, SUM(amount) AS total
            FROM transactions
            GROUP BY type'
        );
        return $stmt->fetchAll();
    }

    public function countAll(): int
    {
        return (int)$this->pdo->query('SELECT COUNT(*) FROM transactions')->fetchColumn();
    }

    public function recent(int $limit = 5): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, type, amount, from_account_id, to_account_id, created_at
            FROM transactions
            ORDER BY created_at DESC, id DESC
            LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function filter(array $filters, int $limit, int $offset): array
    {
        [$where, $params] = $this->buildWhere($filters);

        $sql = 'SELECT * FROM transactions';
        if ($where !== '') {
            $sql .= ' WHERE ' . $where;
        }
        $sql .= ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset';

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countFiltered(array $filters): int
    {
        [$where, $params] = $this->buildWhere($filters);

        $sql = 'SELECT COUNT(*) FROM transactions';
        if ($where !== '') {
            $sql .= ' WHERE ' . $where;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn();
    }

    private function buildWhere(array $filters): array
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['type'])) {
            if ($filters['type'] === 'transfer_internal') {
                $conditions[] = 'type = :type
                    AND (SELECT user_id FROM accounts WHERE id = transactions.from_account_id)
                    = (SELECT user_id FROM accounts WHERE id = transactions.to_account_id)';
                $params[':type'] = 'transfer';
            } elseif ($filters['type'] === 'transfer_external') {
                $conditions[] = 'type = :type
                    AND (SELECT user_id FROM accounts WHERE id = transactions.from_account_id)
                    != (SELECT user_id FROM accounts WHERE id = transactions.to_account_id)';
                $params[':type'] = 'transfer';
            } else {
                $conditions[] = 'type = :type';
                $params[':type'] = $filters['type'];
            }
        }
        if (!empty($filters['from_date'])) {
            $conditions[] = 'created_at >= :from_date';
            $params[':from_date'] = $filters['from_date'] . ' 00:00:00';
        }
        if (!empty($filters['to_date'])) {
            $conditions[] = 'created_at <= :to_date';
            $params[':to_date'] = $filters['to_date'] . ' 23:59:59';
        }
        if (!empty($filters['user_id'])) {
            $conditions[] = '(from_account_id IN (SELECT id FROM accounts WHERE user_id = :user_id_from)
                        OR to_account_id IN (SELECT id FROM accounts WHERE user_id = :user_id_to))';
            $params[':user_id_from'] = $filters['user_id'];
            $params[':user_id_to'] = $filters['user_id'];
        }
        if (!empty($filters['account_id'])) {
            $conditions[] = '(from_account_id = :account_id_from OR to_account_id = :account_id_to)';
            $params[':account_id_from'] = $filters['account_id'];
            $params[':account_id_to'] = $filters['account_id'];
        }

        return [implode(' AND ', $conditions), $params];
    }
}