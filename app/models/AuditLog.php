<?php
/**
 * =====================================================================
 * MPVS — Audit log model
 * Supports the SRS constraint: "Unauthorized access attempts may occur
 * and require monitoring." Records logins, verifications, user
 * management and report generation.
 * =====================================================================
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class AuditLog
{
    /** Record an activity entry. */
    public static function log(?int $userId, string $action, string $details = ''): void
    {
        if (!AUDIT_LOG_ENABLED) {
            return;
        }
        Database::query(
            'INSERT INTO audit_logs (user_id, action, details, ip_address, user_agent)
             VALUES (?, ?, ?, ?, ?)',
            [
                $userId,
                $action,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? 'cli',
                mb_substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 250),
            ]
        );
    }

    /** Recent activity for the dashboard feed. */
    public static function recent(int $limit = 10): array
    {
        return Database::fetchAll(
            'SELECT a.*, u.full_name AS user_name
             FROM audit_logs a
             LEFT JOIN users u ON u.id = a.user_id
             ORDER BY a.created_at DESC, a.id DESC
             LIMIT ' . (int) $limit
        );
    }

    /** Count entries recorded today. */
    public static function countToday(): int
    {
        $row = Database::fetchOne('SELECT COUNT(*) AS c FROM audit_logs WHERE DATE(created_at) = CURDATE()');
        return (int) ($row['c'] ?? 0);
    }
}
