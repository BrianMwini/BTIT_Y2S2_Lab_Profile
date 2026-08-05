<?php
/**
 * =====================================================================
 * MPVS — Base controller
 * Provides view rendering with shared layouts, plus small request helpers.
 * =====================================================================
 */

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    /**
     * Render a view inside a layout.
     *
     * @param string $view   View path relative to /views (no .php), e.g. 'transactions/verify'
     * @param array  $data   Variables extracted into the view scope
     * @param string $layout Layout name: 'app' (dashboard) or 'auth' (centered card)
     */
    protected function render(string $view, array $data = [], string $layout = 'app'): void
    {
        // Layout defaults: the sidebar needs the current user and the
        // topbar needs recent activity for the notification bell. Admins
        // also receive the pending-approval / pending-verification counts
        // so the sidebar and dashboard can surface them.
        if ($layout === 'app') {
            if (Auth::check()) {
                $user = Auth::user();
                $data['user'] ??= $user;
                $data['notifications'] ??= \App\Models\AuditLog::recent(6);
                if ($user['role'] === 'admin') {
                    $data['pendingApprovals'] ??= \App\Models\User::countPending();
                    $data['pendingVerifications'] ??= \App\Models\Transaction::countPending();
                }
            }
        }

        extract($data, EXTR_SKIP);

        // Render the inner view to a buffer so layouts stay reusable.
        ob_start();
        require VIEWS_PATH . '/' . $view . '.php';
        $content = ob_get_clean();

        // A view may request a different layout via $this->layoutOverride.
        $requestedLayout = $this->layoutOverride ?? $layout;

        if ($requestedLayout === 'none') {
            echo $content;
            return;
        }

        require VIEWS_PATH . '/layouts/' . $requestedLayout . '.php';
    }

    /** HTTP GET input. */
    protected function input(string $key, string $default = ''): string
    {
        $value = $_GET[$key] ?? $default;
        return is_string($value) ? trim($value) : $default;
    }

    /** HTTP POST input. */
    protected function post(string $key, string $default = ''): string
    {
        $value = $_POST[$key] ?? $default;
        return is_string($value) ? trim($value) : $default;
    }

    /** CSRF verify helper for POST actions. */
    protected function verifyCsrf(): void
    {
        Csrf::verify();
    }
}
