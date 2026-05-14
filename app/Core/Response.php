<?php

declare(strict_types=1);

namespace App\Core;

/**
 * HTTP Response Helper
 *
 * Provides fluent helpers for sending common HTTP responses.
 */
final class Response
{
    /**
     * Send a plain-text or HTML response.
     */
    public static function send(string $body, int $status = 200, array $headers = []): never
    {
        http_response_code($status);

        foreach ($headers as $name => $value) {
            header("$name: $value");
        }

        echo $body;
        exit;
    }

    /**
     * Send a JSON response.
     */
    public static function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Redirect to a given URL.
     */
    public static function redirect(string $url, int $status = 302): never
    {
        http_response_code($status);
        header("Location: $url");
        exit;
    }

    /**
     * Redirect back to the previous page.
     */
    public static function back(): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        self::redirect($referer);
    }

    /**
     * Abort with an HTTP error code.
     */
    public static function abort(int $code, string $message = ''): never
    {
        http_response_code($code);

        // Try to render a dedicated error view
        $viewFile = VIEW_PATH . "/errors/{$code}.php";

        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "<h1>HTTP {$code}</h1><p>" . htmlspecialchars($message) . "</p>";
        }

        exit;
    }
}
