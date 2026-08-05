<?php
/**
 * =====================================================================
 * MPVS — Customer model (SRS: Customer entity)
 * Customers do not log in; their details are captured from verified
 * transactions and upserted by phone number.
 * =====================================================================
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Customer
{
    /** Find a customer by M-Pesa phone number. */
    public static function findByPhone(string $phone): ?array
    {
        return Database::fetchOne('SELECT * FROM customers WHERE phone = ?', [$phone]);
    }

    /** Find or create a customer by phone + name. Returns the customer id. */
    public static function findOrCreate(string $phone, string $fullName, ?string $email = null): int
    {
        $existing = self::findByPhone($phone);
        if ($existing !== null) {
            // Refresh the name if the payer has provided it before.
            if ($fullName !== '' && $existing['full_name'] !== $fullName) {
                Database::query('UPDATE customers SET full_name = ? WHERE id = ?', [$fullName, $existing['id']]);
            }
            return (int) $existing['id'];
        }

        $name = $fullName !== '' ? $fullName : 'Unknown Customer';
        Database::query(
            'INSERT INTO customers (full_name, phone, email) VALUES (?, ?, ?)',
            [$name, $phone, $email]
        );
        return (int) Database::connection()->lastInsertId();
    }

    /** Total number of distinct customers. */
    public static function countAll(): int
    {
        $row = Database::fetchOne('SELECT COUNT(*) AS c FROM customers');
        return (int) ($row['c'] ?? 0);
    }
}
