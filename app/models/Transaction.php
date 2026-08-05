<?php
/**
 * =====================================================================
 * MPVS — Transaction model (SRS: Transaction entity)
 * The core record of the system: administrators record transactions as
 * Pending, then mark them Verified or Failed on the Verify page.
 * All queries use prepared statements.
 * =====================================================================
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Transaction
{
    /** Find a transaction by primary key (with customer + verifier names). */
    public static function find(int $id): ?array
    {
        $sql = 'SELECT t.*, c.full_name AS customer_name, u.full_name AS verifier_name
                FROM transactions t
                LEFT JOIN customers c ON c.id = t.customer_id
                LEFT JOIN users u ON u.id = t.verified_by
                WHERE t.id = ?';
        return Database::fetchOne($sql, [$id]);
    }

    /** Find a transaction by its M-Pesa-style code. */
    public static function findByCode(string $code): ?array
    {
        return Database::fetchOne('SELECT * FROM transactions WHERE mpesa_code = ?', [$code]);
    }

    /**
     * Create a pending (or any) transaction record.
     *
     * @param array $data mpesa_code, phone, amount, status, customer_id,
     *                    verified_by, verified_at
     * @return int new transaction id
     */
    public static function create(array $data): int
    {
        Database::query(
            'INSERT INTO transactions
                (mpesa_code, customer_id, phone, amount, status, verified_by, verified_at)
             VALUES
                (:mpesa_code, :customer_id, :phone, :amount, :status, :verified_by, :verified_at)',
            [
                'mpesa_code'  => $data['mpesa_code'],
                'customer_id' => $data['customer_id'] ?? null,
                'phone'       => $data['phone'],
                'amount'      => $data['amount'],
                'status'      => $data['status'] ?? 'pending',
                'verified_by' => $data['verified_by'] ?? null,
                'verified_at' => $data['verified_at'] ?? null,
            ]
        );
        return (int) Database::connection()->lastInsertId();
    }

    /**
     * Generate a unique 10-character M-Pesa-style code (e.g. QHJ7K8L9MN).
     * Ambiguous characters (0/O, 1/I) are excluded for clarity.
     */
    public static function generateCode(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        do {
            $code = '';
            for ($i = 0; $i < 10; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } while (self::findByCode($code) !== null); // prevent duplicates (UNIQUE key)
        return $code;
    }

    /** Mark a pending transaction as verified. Sets verifier + timestamp. */
    public static function markVerified(int $id, int $verifiedBy): void
    {
        Database::query(
            "UPDATE transactions SET status = 'verified', verified_by = ?, verified_at = NOW() WHERE id = ?",
            [$verifiedBy, $id]
        );
    }

    /** Mark a pending transaction as failed. Sets verifier + timestamp. */
    public static function markFailed(int $id, int $verifiedBy): void
    {
        Database::query(
            "UPDATE transactions SET status = 'failed', verified_by = ?, verified_at = NOW() WHERE id = ?",
            [$verifiedBy, $id]
        );
    }

    /**
     * List transactions with search, filters and pagination.
     *
     * @param array $filters code, phone, customer, status, verifier, date_from, date_to
     * @param int   $page    current page (1-based)
     * @param int   $perPage rows per page
     * @return array{rows:array,total:int,pages:int}
     */
    public static function search(array $filters, int $page = 1, int $perPage = 15): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['code'])) {
            $where[] = 't.mpesa_code LIKE :code';
            $params['code'] = '%' . $filters['code'] . '%';
        }
        if (!empty($filters['phone'])) {
            $where[] = 't.phone LIKE :phone';
            $params['phone'] = '%' . $filters['phone'] . '%';
        }
        if (!empty($filters['customer'])) {
            $where[] = 'c.full_name LIKE :customer';
            $params['customer'] = '%' . $filters['customer'] . '%';
        }
        if (!empty($filters['status']) && in_array($filters['status'], ['verified', 'failed', 'pending'], true)) {
            $where[] = 't.status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['verifier']) && (int) $filters['verifier'] > 0) {
            $where[] = 't.verified_by = :verifier';
            $params['verifier'] = (int) $filters['verifier'];
        }
        if (!empty($filters['date_from'])) {
            $where[] = 'DATE(t.created_at) >= :date_from';
            $params['date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = 'DATE(t.created_at) <= :date_to';
            $params['date_to'] = $filters['date_to'];
        }

        $whereSql = $where ? (' WHERE ' . implode(' AND ', $where)) : '';

        $totalRow = Database::fetchOne(
            'SELECT COUNT(*) AS c FROM transactions t
             LEFT JOIN customers c ON c.id = t.customer_id' . $whereSql,
            $params
        );
        $total = (int) ($totalRow['c'] ?? 0);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, $page), $pages);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT t.*, c.full_name AS customer_name, u.full_name AS verifier_name
                FROM transactions t
                LEFT JOIN customers c ON c.id = t.customer_id
                LEFT JOIN users u ON u.id = t.verified_by' . $whereSql . '
                ORDER BY t.created_at DESC, t.id DESC
                LIMIT :limit OFFSET :offset';

        $stmt = Database::connection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return ['rows' => $stmt->fetchAll(), 'total' => $total, 'pages' => $pages, 'page' => $page];
    }

    /** Recent transactions (dashboard). */
    public static function recent(int $limit = 8): array
    {
        return Database::fetchAll(
            'SELECT t.*, c.full_name AS customer_name
             FROM transactions t
             LEFT JOIN customers c ON c.id = t.customer_id
             ORDER BY t.created_at DESC, t.id DESC
             LIMIT ' . (int) $limit
        );
    }

    /** Distinct users who have verified transactions (reports verifier filter). */
    public static function verifiers(): array
    {
        return Database::fetchAll(
            'SELECT DISTINCT u.id, u.full_name
             FROM users u
             INNER JOIN transactions t ON t.verified_by = u.id
             ORDER BY u.full_name ASC'
        );
    }

    /* -----------------------------------------------------------------
     * Dashboard & report statistics
     * ----------------------------------------------------------------- */

    /** Count transactions created today. */
    public static function countToday(): int
    {
        $row = Database::fetchOne('SELECT COUNT(*) AS c FROM transactions WHERE DATE(created_at) = CURDATE()');
        return (int) ($row['c'] ?? 0);
    }

    /** Count pending (awaiting verification) transactions. */
    public static function countPending(): int
    {
        $row = Database::fetchOne("SELECT COUNT(*) AS c FROM transactions WHERE status = 'pending'");
        return (int) ($row['c'] ?? 0);
    }

    /** Count transactions verified today. */
    public static function countTodayVerified(): int
    {
        $row = Database::fetchOne("SELECT COUNT(*) AS c FROM transactions WHERE status = 'verified' AND DATE(verified_at) = CURDATE()");
        return (int) ($row['c'] ?? 0);
    }

    /** Count transactions marked failed today. */
    public static function countTodayFailed(): int
    {
        $row = Database::fetchOne("SELECT COUNT(*) AS c FROM transactions WHERE status = 'failed' AND DATE(verified_at) = CURDATE()");
        return (int) ($row['c'] ?? 0);
    }

    /** Count transactions by status (optionally since a date). */
    public static function countByStatus(string $status, ?string $since = null): int
    {
        $sql = 'SELECT COUNT(*) AS c FROM transactions WHERE status = ?';
        $params = [$status];
        if ($since !== null) {
            $sql .= ' AND DATE(created_at) >= ?';
            $params[] = $since;
        }
        $row = Database::fetchOne($sql, $params);
        return (int) ($row['c'] ?? 0);
    }

    /** Total verified revenue (optionally since a date). */
    public static function revenueVerified(?string $since = null): float
    {
        $sql = "SELECT COALESCE(SUM(amount), 0) AS s FROM transactions WHERE status = 'verified'";
        $params = [];
        if ($since !== null) {
            $sql .= ' AND DATE(created_at) >= ?';
            $params[] = $since;
        }
        $row = Database::fetchOne($sql, $params);
        return (float) ($row['s'] ?? 0);
    }

    /** Average verified transaction amount. */
    public static function averageAmount(): float
    {
        $row = Database::fetchOne("SELECT COALESCE(AVG(amount), 0) AS a FROM transactions WHERE status = 'verified'");
        return (float) ($row['a'] ?? 0);
    }

    /** Verified revenue + count per day between two dates (chart data). */
    public static function dailySeries(string $dateFrom, string $dateTo): array
    {
        return Database::fetchAll(
            "SELECT DATE(created_at) AS day,
                    COUNT(*) AS total,
                    SUM(CASE WHEN status = 'verified' THEN 1 ELSE 0 END) AS verified,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) AS failed,
                    COALESCE(SUM(CASE WHEN status = 'verified' THEN amount ELSE 0 END), 0) AS revenue
             FROM transactions
             WHERE DATE(created_at) BETWEEN ? AND ?
             GROUP BY DATE(created_at)
             ORDER BY day ASC",
            [$dateFrom, $dateTo]
        );
    }

    /** Status distribution counts (chart data). */
    public static function statusDistribution(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $sql = 'SELECT status, COUNT(*) AS c FROM transactions';
        $params = [];
        if ($dateFrom && $dateTo) {
            $sql .= ' WHERE DATE(created_at) BETWEEN ? AND ?';
            $params = [$dateFrom, $dateTo];
        }
        $sql .= ' GROUP BY status';
        $rows = Database::fetchAll($sql, $params);
        $map = ['verified' => 0, 'failed' => 0, 'pending' => 0];
        foreach ($rows as $row) {
            if (isset($map[$row['status']])) {
                $map[$row['status']] = (int) $row['c'];
            }
        }
        return $map;
    }

    /** Aggregate statistics over an optional date window. */
    public static function stats(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $sql = 'SELECT COUNT(*) AS total,
                       SUM(CASE WHEN status = "verified" THEN 1 ELSE 0 END) AS verified,
                       SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) AS failed,
                       SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) AS pending,
                       COALESCE(SUM(CASE WHEN status = "verified" THEN amount ELSE 0 END), 0) AS revenue
                FROM transactions';
        $params = [];
        if ($dateFrom && $dateTo) {
            $sql .= ' WHERE DATE(created_at) BETWEEN ? AND ?';
            $params = [$dateFrom, $dateTo];
        }
        $row = Database::fetchOne($sql, $params);
        $stats = [
            'total'   => (int) ($row['total'] ?? 0),
            'verified' => (int) ($row['verified'] ?? 0),
            'failed'  => (int) ($row['failed'] ?? 0),
            'pending' => (int) ($row['pending'] ?? 0),
            'revenue' => (float) ($row['revenue'] ?? 0),
        ];
        $stats['average'] = $stats['verified'] > 0
            ? round($stats['revenue'] / $stats['verified'], 2)
            : 0.0;
        return $stats;
    }
}
