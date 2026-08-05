<?php
/**
 * =====================================================================
 * MPVS — Report model (SRS: Report entity)
 * Stores a log of every report generated for auditability.
 * =====================================================================
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/** Create a report record. Returns the new id. */
class Report
{
    public static function create(array $data): int
    {
        Database::query(
            'INSERT INTO reports (report_type, title, date_from, date_to, status_filter, summary, generated_by)
             VALUES (:report_type, :title, :date_from, :date_to, :status_filter, :summary, :generated_by)',
            [
                'report_type'   => $data['report_type'] ?? 'transactions',
                'title'         => $data['title'],
                'date_from'     => $data['date_from'] ?? null,
                'date_to'       => $data['date_to'] ?? null,
                'status_filter' => $data['status_filter'] ?? null,
                'summary'       => json_encode($data['summary'] ?? []),
                'generated_by'  => $data['generated_by'] ?? null,
            ]
        );
        return (int) Database::connection()->lastInsertId();
    }

    /** Recent report history. */
    public static function recent(int $limit = 10): array
    {
        return Database::fetchAll(
            'SELECT r.*, u.full_name AS generated_by_name
             FROM reports r
             LEFT JOIN users u ON u.id = r.generated_by
             ORDER BY r.generated_at DESC
             LIMIT ' . (int) $limit
        );
    }

    /** Total reports generated. */
    public static function countAll(): int
    {
        $row = Database::fetchOne('SELECT COUNT(*) AS c FROM reports');
        return (int) ($row['c'] ?? 0);
    }
}
