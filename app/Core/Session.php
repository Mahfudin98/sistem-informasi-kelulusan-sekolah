<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Session Manager
 *
 * Thin wrapper around PHP's native session with helpers for flash
 * messages and CSRF tokens.
 */
final class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $name     = $_ENV['SESSION_NAME']      ?? 'app_session';
        $lifetime = (int) ($_ENV['SESSION_LIFETIME'] ?? 7200);
        $secure   = filter_var($_ENV['SESSION_SECURE']    ?? false, FILTER_VALIDATE_BOOLEAN);
        $httpOnly = filter_var($_ENV['SESSION_HTTP_ONLY'] ?? true,  FILTER_VALIDATE_BOOLEAN);

        session_name($name);

        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path'     => '/',
            'secure'   => $secure,
            'httponly' => $httpOnly,
            'samesite' => 'Lax',
        ]);

        session_start();
        self::$started = true;
    }

    // ── Basic Get / Set / Has / Remove ────────────────────────────────────────

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function all(): array
    {
        return $_SESSION;
    }

    public static function flush(): void
    {
        $_SESSION = [];
    }

    public static function destroy(): void
    {
        self::flush();
        session_destroy();
        self::$started = false;
    }

    // ── Flash Messages ────────────────────────────────────────────────────────

    /**
     * Store a flash message (auto-deleted after being read once).
     */
    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Retrieve and remove a flash message.
     */
    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public static function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }

    // ── CSRF ──────────────────────────────────────────────────────────────────

    /**
     * Generate (or retrieve) a CSRF token for the current session.
     */
    public static function csrfToken(): string
    {
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }

    /**
     * Validate a submitted CSRF token (constant-time comparison).
     */
    public static function validateCsrf(string $token): bool
    {
        $stored = $_SESSION['_csrf_token'] ?? '';
        return hash_equals($stored, $token);
    }

    /**
     * Regenerate the session ID (call after login/privilege change).
     */
    public static function regenerate(bool $deleteOld = true): void
    {
        session_regenerate_id($deleteOld);
    }
}
