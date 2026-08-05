<?php
/**
 * =====================================================================
 * MPVS — Report controller (SRS 4.2.6 Reports Interface)
 * Administrator-only. Statistics, charts, filters, CSV export and a log
 * of every generated report.
 * =====================================================================
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\AuditLog;
use App\Models\Report;
use App\Models\Transaction;

class ReportController extends Controller
{
    /** GET /reports — report dashboard. */
    public function index(array $params = []): void
    {
        Auth::requireRole('admin');

        // Default window: last 30 days.
        $dateFrom = $this->input('from', date('Y-m-d', strtotime('-29 days')));
        $dateTo   = $this->input('to', date('Y-m-d'));
        $status   = $this->input('status', 'all');
        $verifier = $this->input('verifier', '');

        if ($status !== 'all' && !in_array($status, ['verified', 'failed', 'pending'], true)) {
            $status = 'all';
        }
        if ($dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $stats  = Transaction::stats($dateFrom, $dateTo);
        $series = Transaction::dailySeries($dateFrom, $dateTo);
        $distribution = Transaction::statusDistribution($dateFrom, $dateTo);

        // Recent records matching the window (top 25) for the table view.
        $recentRows = Transaction::search([
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
            'status'    => $status !== 'all' ? $status : '',
            'verifier'  => $verifier,
        ], 1, 25)['rows'];

        $this->render('reports/index', [
            'title'        => 'Reports',
            'user'         => Auth::user(),
            'dateFrom'     => $dateFrom,
            'dateTo'       => $dateTo,
            'status'       => $status,
            'verifier'     => $verifier,
            'verifiers'    => Transaction::verifiers(),
            'stats'        => $stats,
            'series'       => $series,
            'distribution' => $distribution,
            'recentRows'   => $recentRows,
            'reportHistory' => Report::recent(8),
        ]);
    }

    /** POST /reports/generate — persist a report record (auditability). */
    public function generate(array $params = []): void
    {
        Auth::requireRole('admin');
        $this->verifyCsrf();

        $dateFrom = $this->post('from', date('Y-m-d', strtotime('-29 days')));
        $dateTo   = $this->post('to', date('Y-m-d'));
        $status   = $this->post('status', 'all');
        if ($status !== 'all' && !in_array($status, ['verified', 'failed', 'pending'], true)) {
            $status = 'all';
        }
        if ($dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $stats = Transaction::stats($dateFrom, $dateTo);

        $title = 'Transaction report';
        if ($status !== 'all') {
            $title = ucfirst($status) . ' transactions report';
        }
        $title .= ' (' . date('d M Y', strtotime($dateFrom)) . ' — ' . date('d M Y', strtotime($dateTo)) . ')';

        Report::create([
            'report_type'   => 'transactions',
            'title'         => $title,
            'date_from'     => $dateFrom,
            'date_to'       => $dateTo,
            'status_filter' => $status !== 'all' ? $status : null,
            'summary'       => $stats,
            'generated_by'  => Auth::id(),
        ]);
        AuditLog::log(Auth::id(), 'generate_report', 'Generated: ' . $title);

        Flash::set('success', 'Report generated and saved to report history.');
        redirect('reports?from=' . urlencode($dateFrom) . '&to=' . urlencode($dateTo) . '&status=' . urlencode($status));
    }

    /** GET /reports/export — download the filtered dataset as CSV. */
    public function export(array $params = []): void
    {
        Auth::requireRole('admin');

        $dateFrom = $this->input('from', date('Y-m-d', strtotime('-29 days')));
        $dateTo   = $this->input('to', date('Y-m-d'));
        $status   = $this->input('status', 'all');
        if ($status !== 'all' && !in_array($status, ['verified', 'failed', 'pending'], true)) {
            $status = 'all';
        }
        if ($dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $verifier = $this->input('verifier', '');
        $rows = Transaction::search([
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
            'status'    => $status !== 'all' ? $status : '',
            'verifier'  => $verifier,
        ], 1, 10000)['rows'];

        AuditLog::log(Auth::id(), 'export_csv', 'CSV export ' . count($rows) . ' rows (' . $dateFrom . ' → ' . $dateTo . ')');

        // Build CSV (cells starting with = + - @ are quoted with a leading
        // apostrophe to prevent spreadsheet formula injection).
        $sanitize = fn(string $value): string =>
            preg_match('/^[=+\-@]/', $value) ? "'" . $value : $value;

        $out = fopen('php://temp', 'r+');
        fputcsv($out, ['Receipt Code', 'Customer', 'Phone', 'Amount (KES)', 'Status', 'Verified By', 'Verification Date']);
        foreach ($rows as $row) {
            fputcsv($out, [
                $sanitize($row['mpesa_code']),
                $sanitize($row['customer_name'] ?? '—'),
                $sanitize($row['phone']),
                number_format((float) $row['amount'], 2),
                $row['status'],
                $sanitize($row['verifier_name'] ?? ''),
                $row['verified_at'],
            ]);
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="mpvs-report-' . $dateFrom . '_to_' . $dateTo . '.csv"');
        echo "\xEF\xBB\xBF" . $csv; // UTF-8 BOM so Excel renders correctly
        exit;
    }
}
