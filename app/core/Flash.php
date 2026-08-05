<?php
/**
 * =====================================================================
 * MPVS — Flash messages (one-time success / error / info alerts)
 * =====================================================================
 */

declare(strict_types=1);

namespace App\Core;

class Flash
{
    /** Queue a flash message shown on the next page load. */
    public static function set(string $type, string $message): void
    {
        $_SESSION['flash_messages'][] = ['type' => $type, 'message' => $message];
    }

    /** Return all queued messages and clear the queue. */
    public static function pull(): array
    {
        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        return $messages;
    }
}
