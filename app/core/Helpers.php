<?php
/**
 * =====================================================================
 * MPVS — Global helper functions
 * =====================================================================
 */

declare(strict_types=1);

use App\Core\Csrf;

/** Escape output for HTML (prevents XSS). Use for ALL dynamic output. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** Build an application URL from a relative path. */
function url(string $path = ''): string
{
    $base = defined('BASE_URL') ? BASE_URL : '';
    return $base . '/' . ltrim($path, '/');
}

/** Redirect to an application path and terminate. */
function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

/** Retrieve a previous form input value (after a failed POST). */
function old(string $key, string $default = ''): string
{
    return isset($_SESSION['old_input'][$key]) ? (string) $_SESSION['old_input'][$key] : $default;
}

/** Remember current form input so it survives a redirect on error. */
function remember_inputs(array $inputs): void
{
    $_SESSION['old_input'] = $inputs;
}

/** Clear remembered form inputs. */
function clear_remembered_inputs(): void
{
    unset($_SESSION['old_input']);
}

/** Format an amount as currency, e.g. money(1500) -> "KES 1,500.00". */
function money($amount): string
{
    return DEFAULT_CURRENCY . ' ' . number_format((float) $amount, 2);
}

/** Format a MySQL datetime for display. */
function format_datetime(?string $datetime, string $format = 'd M Y, g:i A'): string
{
    if (empty($datetime)) {
        return '—';
    }
    $ts = strtotime($datetime);
    return $ts ? date($format, $ts) : $datetime;
}

/** Bootstrap badge HTML for a transaction status. */
function status_badge(string $status): string
{
    $map = [
        'verified' => ['success', 'fa-circle-check'],
        'failed'   => ['danger', 'fa-circle-xmark'],
        'pending'  => ['warning', 'fa-clock'],
    ];
    [$color, $icon] = $map[$status] ?? ['secondary', 'fa-circle-question'];
    return '<span class="badge rounded-pill bg-' . $color . '-subtle text-' . $color
        . ' border border-' . $color . '-subtle px-3 py-2"><i class="fa-solid ' . $icon . ' me-1"></i>'
        . ucfirst(e($status)) . '</span>';
}

/** Bootstrap badge HTML for a user role. */
function role_badge(string $role): string
{
    if ($role === 'admin') {
        return '<span class="badge rounded-pill bg-dark px-3 py-2"><i class="fa-solid fa-user-shield me-1"></i>Administrator</span>';
    }
    return '<span class="badge rounded-pill bg-info-subtle text-info-emphasis border border-info-subtle px-3 py-2"><i class="fa-solid fa-user me-1"></i>Staff</span>';
}

/** Bootstrap badge HTML for a user status (approval workflow). */
function user_status_badge(string $status): string
{
    $map = [
        'approved' => ['success', 'fa-circle-check', 'Approved'],
        'pending'  => ['warning', 'fa-clock', 'Pending'],
        'rejected' => ['danger', 'fa-ban', 'Rejected'],
        'inactive' => ['secondary', 'fa-user-slash', 'Inactive'],
    ];
    [$color, $icon, $label] = $map[$status] ?? ['secondary', 'fa-circle-question', ucfirst($status)];
    return '<span class="badge rounded-pill bg-' . $color . '-subtle text-' . $color
        . ' border border-' . $color . '-subtle px-3 py-2"><i class="fa-solid ' . $icon . ' me-1"></i>'
        . e($label) . '</span>';
}

/** CSRF hidden field for forms. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(Csrf::token()) . '">';
}

/** Human-readable audit action label. */
function audit_action_label(string $action): string
{
    $labels = [
        'login'               => 'User logged in',
        'login_failed'        => 'Failed login attempt',
        'logout'              => 'User logged out',
        'register'            => 'Staff registration (pending approval)',
        'transaction_created' => 'Transaction recorded',
        'verify_transaction'  => 'Transaction verified',
        'transaction_failed'  => 'Transaction marked as failed',
        'generate_receipt'    => 'Receipt generated',
        'generate_report'     => 'Report generated',
        'export_csv'          => 'Report exported (CSV)',
        'user_created'        => 'User account created',
        'user_updated'        => 'User account updated',
        'user_approved'       => 'User account approved',
        'user_rejected'       => 'User account rejected',
        'user_suspended'      => 'User account suspended',
        'user_activated'      => 'User account activated',
        'forbidden'           => 'Blocked unauthorized access',
    ];
    return $labels[$action] ?? ucfirst(str_replace('_', ' ', $action));
}
