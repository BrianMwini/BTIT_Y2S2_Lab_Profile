<?php
/**
 * =====================================================================
 * MPVS — PDO database connection (singleton)
 * All queries across the application use PDO prepared statements.
 * =====================================================================
 */

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    /** Return the shared PDO connection, creating it on first use. */
    public static function connection(): PDO
    {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::ATTR_STRINGIFY_FETCHES  => false,
                ]);
            } catch (PDOException $e) {
                // Clean, non-technical error if the database is missing.
                http_response_code(500);
                if (DEBUG_MODE) {
                    die('Database connection failed: ' . htmlspecialchars($e->getMessage())
                        . '<br>Have you run <a href="setup.php">setup.php</a> yet?');
                }
                die('Database connection failed. Please run setup.php and try again.');
            }
        }
        return self::$instance;
    }

    /** Convenience: execute a prepared statement and return it. */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /** Fetch a single row, or null when nothing matches. */
    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $row = self::query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /** Fetch all rows. */
    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }
}
