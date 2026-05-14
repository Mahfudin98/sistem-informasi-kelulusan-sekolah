<?php

declare(strict_types=1);

namespace App\Core;

/**
 * HTTP Request Abstraction
 *
 * Wraps the PHP super-globals into a clean, immutable-ish object.
 */
final class Request
{
    public readonly string  $method;
    public readonly string  $uri;
    public readonly string  $path;
    public readonly array   $query;
    public readonly array   $body;
    public readonly array   $files;
    public readonly array   $headers;

    public function __construct()
    {
        $this->method  = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->uri     = $_SERVER['REQUEST_URI'] ?? '/';
        $this->path    = strtok($this->uri, '?') ?: '/';
        $this->query   = $_GET;
        $this->body    = $_POST;
        $this->files   = $_FILES;
        $this->headers = $this->parseHeaders();
    }

    // ── Convenience Getters ──────────────────────────────────────────────────

    public function isGet(): bool    { return $this->method === 'GET'; }
    public function isPost(): bool   { return $this->method === 'POST'; }
    public function isPut(): bool    { return $this->method === 'PUT'; }
    public function isPatch(): bool  { return $this->method === 'PATCH'; }
    public function isDelete(): bool { return $this->method === 'DELETE'; }
    public function isAjax(): bool
    {
        return ($this->headers['X-Requested-With'] ?? '') === 'XMLHttpRequest';
    }

    /**
     * Retrieve a sanitised query-string value.
     */
    public function query(string $key, mixed $default = null): mixed
    {
        return isset($this->query[$key])
            ? htmlspecialchars((string) $this->query[$key], ENT_QUOTES, 'UTF-8')
            : $default;
    }

    /**
     * Retrieve a sanitised POST body value.
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return isset($this->body[$key])
            ? htmlspecialchars((string) $this->body[$key], ENT_QUOTES, 'UTF-8')
            : $default;
    }

    /**
     * Return all POST inputs (sanitised).
     */
    public function all(): array
    {
        return array_map(
            fn($v) => is_string($v) ? htmlspecialchars($v, ENT_QUOTES, 'UTF-8') : $v,
            $this->body,
        );
    }

    /**
     * Parse HTTP request headers.
     */
    private function parseHeaders(): array
    {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace(
                    ' ', '-',
                    ucwords(strtolower(str_replace('_', ' ', substr($key, 5))))
                );
                $headers[$name] = $value;
            }
        }

        return $headers;
    }

    /**
     * Retrieve an uploaded file entry.
     */
    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Return the client IP address.
     */
    public function ip(): string
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR']
            ?? '0.0.0.0';
    }
}
