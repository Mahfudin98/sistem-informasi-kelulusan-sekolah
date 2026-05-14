<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

/**
 * Global Exception & Error Handler
 *
 * Registers PHP error/exception handlers and renders
 * friendly error pages (or JSON for AJAX requests).
 */
final class ExceptionHandler
{
    public static function register(): void
    {
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handleException(Throwable $e): void
    {
        self::log($e);
        self::render($e);
    }

    public static function handleError(
        int    $severity,
        string $message,
        string $file,
        int    $line,
    ): bool {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

    public static function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $e = new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
            self::handleException($e);
        }
    }

    // ── Internal ──────────────────────────────────────────────────────────────

    private static function log(Throwable $e): void
    {
        $logDir = STORAGE_PATH . '/logs';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $entry = sprintf(
            "[%s] %s: %s in %s:%d\nTrace:\n%s\n%s\n",
            date('Y-m-d H:i:s'),
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString(),
            str_repeat('-', 80),
        );

        file_put_contents($logDir . '/app.log', $entry, FILE_APPEND | LOCK_EX);
    }

    private static function render(Throwable $e): void
    {
        $isDebug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $isAjax  = (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        http_response_code(500);

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'error'   => true,
                'message' => $isDebug ? $e->getMessage() : 'Server error.',
            ]);
            return;
        }

        $errorView = VIEW_PATH . '/errors/500.php';

        if (file_exists($errorView)) {
            require $errorView;
            return;
        }

        if ($isDebug) {
            echo '<pre style="background:#1e1e2e;color:#cdd6f4;padding:2rem;font-size:.875rem;">';
            echo '<strong style="color:#f38ba8">' . get_class($e) . '</strong>: ';
            echo htmlspecialchars($e->getMessage()) . "\n\n";
            echo htmlspecialchars($e->getTraceAsString());
            echo '</pre>';
        } else {
            echo '<h1>500 — Internal Server Error</h1>';
            echo '<p>Something went wrong. Please try again later.</p>';
        }
    }
}
