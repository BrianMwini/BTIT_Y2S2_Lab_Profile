<?php
/**
 * =====================================================================
 * MPVS — Dashboard controller (SRS 4.2.2 Dashboard Interface)
 * Overview of transaction statistics, actionable notification cards
 * (pending verifications / pending approvals / today's activity),
 * recent transactions and the activity feed.
 * =====================================================================
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Receipt;
use App\Models\Transaction;
use App\Models\User;

class DashboardController extends Controller
{
    /** GET / — the central navigation hub. */
    public function index(array $params = []): void
    {
        Auth::requireLogin();
        $user = Auth::user();

        // ---- Notification cards -------------------------------------------------
        $pendingVerifications = Transaction::countPending();
        $verifiedToday        = Transaction::countTodayVerified();
        $failedToday          = Transaction::countTodayFailed();
        $pendingApprovals     = $user['role'] === 'admin' ? User::countPending() : 0;

        // ---- Statistics cards ---------------------------------------------------
        $todayCount = Transaction::countToday();
        $totalVerified = Transaction::countByStatus('verified');
        $totalFailed = Transaction::countByStatus('failed');
        $totalRevenue = Transaction::revenueVerified();

        // ---- 7-day chart series ------------------------------------------------
        $dayLabels = [];
        $dayRevenue = [];
        $dayVerified = [];
        $daily = Transaction::dailySeries(date('Y-m-d', strtotime('-6 days')), date('Y-m-d'));
        $byDay = [];
        foreach ($daily as $row) {
            $byDay[$row['day']] = $row;
        }
        for ($i = 6; $i >= 0; $i--) {
            $key = date('Y-m-d', strtotime("-$i days"));
            $dayLabels[] = date('D', strtotime($key));
            $dayRevenue[] = (float) ($byDay[$key]['revenue'] ?? 0);
            $dayVerified[] = (int) ($byDay[$key]['verified'] ?? 0);
        }

        // ---- Lists -------------------------------------------------------------
        $recentTransactions = Transaction::recent(7);
        $recentActivity = AuditLog::recent(8);

        $this->render('dashboard/index', [
            'title'               => 'Dashboard',
            'user'                => $user,
            'pendingVerifications' => $pendingVerifications,
            'verifiedToday'       => $verifiedToday,
            'failedToday'         => $failedToday,
            'pendingApprovals'    => $pendingApprovals,
            'todayCount'          => $todayCount,
            'totalVerified'       => $totalVerified,
            'totalFailed'         => $totalFailed,
            'totalRevenue'        => $totalRevenue,
            'userCount'           => User::countApproved(),
            'customerCount'       => Customer::countAll(),
            'receiptCount'        => Receipt::countAll(),
            'dayLabels'           => $dayLabels,
            'dayRevenue'          => $dayRevenue,
            'dayVerified'         => $dayVerified,
            'recentTransactions'  => $recentTransactions,
            'recentActivity'      => $recentActivity,
        ]);
    }
}
