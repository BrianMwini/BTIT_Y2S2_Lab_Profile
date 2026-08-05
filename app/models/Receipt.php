<?php
/**
 * =====================================================================
 * MPVS — Receipt model (SRS: Receipt entity)
 * One digital receipt per verified transaction, printable & savable.
 * =====================================================================
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Receipt
{
    /** Generate the next sequential receipt number, e.g. RCP-2026-000042. */
    public static function nextReceiptNo(): string
    {
        $row = Database::fetchOne('SELECT COUNT(*) AS c FROM receipts');
        $seq = ((int) ($row['c'] ?? 0)) + 1;
        return 'RCP-' . date('Y') . '-' . str_pad((string) $seq, 6, '0', STR_PAD_LEFT);
    }

    /** Create a receipt for a verified transaction. Returns the new id. */
    public static function create(int $transactionId, ?int $generatedBy): int
    {
        Database::query(
            'INSERT INTO receipts (receipt_no, transaction_id, generated_by) VALUES (?, ?, ?)',
            [self::nextReceiptNo(), $transactionId, $generatedBy]
        );
        return (int) Database::connection()->lastInsertId();
    }

    /** Find the receipt for a transaction (joined with transaction + customer). */
    public static function findByTransactionId(int $transactionId): ?array
    {
        return Database::fetchOne(
            'SELECT r.*, t.mpesa_code, t.amount, t.phone, t.status, t.verified_at,
                    c.full_name AS customer_name, u.full_name AS generated_by_name
             FROM receipts r
             JOIN transactions t ON t.id = r.transaction_id
             LEFT JOIN customers c ON c.id = t.customer_id
             LEFT JOIN users u ON u.id = r.generated_by
             WHERE r.transaction_id = ?',
            [$transactionId]
        );
    }

    /** Find a receipt by primary key. */
    public static function find(int $id): ?array
    {
        return Database::fetchOne(
            'SELECT r.*, t.mpesa_code, t.amount, t.phone, t.status, t.verified_at,
                    c.full_name AS customer_name, u.full_name AS generated_by_name
             FROM receipts r
             JOIN transactions t ON t.id = r.transaction_id
             LEFT JOIN customers c ON c.id = t.customer_id
             LEFT JOIN users u ON u.id = r.generated_by
             WHERE r.id = ?',
            [$id]
        );
    }

    /** Total number of receipts generated. */
    public static function countAll(): int
    {
        $row = Database::fetchOne('SELECT COUNT(*) AS c FROM receipts');
        return (int) ($row['c'] ?? 0);
    }
}
