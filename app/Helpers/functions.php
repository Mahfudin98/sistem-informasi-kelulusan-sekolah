<?php

declare(strict_types=1);

if (!function_exists('env')) {
    /**
     * Retrieve an environment variable with an optional default.
     */
    function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
}

if (!function_exists('config')) {
    /**
     * Read a dot-notation key from a config file in /config/*.php.
     * Results are cached in a static registry.
     */
    function config(string $key, mixed $default = null): mixed
    {
        static $cache = [];

        [$file, $rest] = array_pad(explode('.', $key, 2), 2, null);
        $path = ROOT_PATH . "/config/{$file}.php";

        if (!isset($cache[$file])) {
            $cache[$file] = file_exists($path) ? require $path : [];
        }

        if ($rest === null) {
            return $cache[$file] ?? $default;
        }

        $segments = explode('.', $rest);
        $data     = $cache[$file];

        foreach ($segments as $segment) {
            if (!is_array($data) || !array_key_exists($segment, $data)) {
                return $default;
            }
            $data = $data[$segment];
        }

        return $data;
    }
}

if (!function_exists('url')) {
    /**
     * Generate an absolute URL from a path.
     */
    function url(string $path = ''): string
    {
        $base = rtrim(env('APP_URL', ''), '/');
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    /**
     * Generate a URL to a public asset.
     */
    function asset(string $path): string
    {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('redirect')) {
    /**
     * Helper to redirect and exit.
     */
    function redirect(string $url, int $status = 302): never
    {
        \App\Core\Response::redirect($url, $status);
    }
}

if (!function_exists('session')) {
    /**
     * Get/set session values.
     */
    function session(string $key, mixed $default = null): mixed
    {
        return \App\Core\Session::get($key, $default);
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Return the current CSRF token.
     */
    function csrf_token(): string
    {
        return \App\Core\Session::csrfToken();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Return a hidden HTML input for the CSRF token.
     */
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('old')) {
    /**
     * Retrieve old (flashed) input value.
     */
    function old(string $key, mixed $default = ''): mixed
    {
        $old = \App\Core\Session::get('old', []);
        return $old[$key] ?? $default;
    }
}

if (!function_exists('flash')) {
    /**
     * Get a flash message from the session.
     */
    function flash(string $key, mixed $default = null): mixed
    {
        return \App\Core\Session::getFlash($key, $default);
    }
}

if (!function_exists('e')) {
    /**
     * Escape HTML entities (alias for htmlspecialchars).
     */
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and die — for debugging only.
     */
    function dd(mixed ...$vars): never
    {
        echo '<pre style="background:#1e1e2e;color:#cdd6f4;padding:1.5rem;font-size:.875rem;">';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        exit;
    }
}

if (!function_exists('abort')) {
    /**
     * Abort request with HTTP status code.
     */
    function abort(int $code, string $message = ''): never
    {
        \App\Core\Response::abort($code, $message);
    }
}

if (!function_exists('str_slug')) {
    /**
     * Generate a URL-friendly slug from a string.
     */
    function str_slug(string $value, string $separator = '-'): string
    {
        $value = mb_strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9\s-]/', '', $value);
        $value = preg_replace('/[\s-]+/', $separator, $value);
        return trim($value, $separator);
    }
}

if (!function_exists('now')) {
    /**
     * Return the current datetime string.
     */
    function now(string $format = 'Y-m-d H:i:s'): string
    {
        return date($format);
    }
}

if (!function_exists('format_date')) {
    /**
     * Format a date string or timestamp.
     */
    function format_date(string|int $date, string $format = 'd M Y'): string
    {
        $ts = is_int($date) ? $date : strtotime($date);
        return date($format, $ts ?: time());
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format a number as Rupiah.
     */
    function format_currency(float|int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('pagination')) {
    /**
     * Generate a simple Bootstrap-compatible pagination HTML.
     *
     * @param array{page: int, lastPage: int, total: int} $meta
     */
    function pagination(array $meta, string $baseUrl = ''): string
    {
        if ($meta['lastPage'] <= 1) {
            return '';
        }

        $html  = '<nav aria-label="Navigasi Halaman"><ul class="pagination">';
        $cur   = $meta['page'];
        $last  = $meta['lastPage'];
        $sep   = str_contains($baseUrl, '?') ? '&' : '?';

        $html .= '<li class="page-item' . ($cur <= 1 ? ' disabled' : '') . '">';
        $html .= '<a class="page-link" href="' . e($baseUrl . $sep . 'page=' . ($cur - 1)) . '">&laquo;</a></li>';

        for ($p = max(1, $cur - 2); $p <= min($last, $cur + 2); $p++) {
            $active = $p === $cur ? ' active' : '';
            $html  .= '<li class="page-item' . $active . '">';
            $html  .= '<a class="page-link" href="' . e($baseUrl . $sep . 'page=' . $p) . '">' . $p . '</a></li>';
        }

        $html .= '<li class="page-item' . ($cur >= $last ? ' disabled' : '') . '">';
        $html .= '<a class="page-link" href="' . e($baseUrl . $sep . 'page=' . ($cur + 1)) . '">&raquo;</a></li>';
        $html .= '</ul></nav>';

        return $html;
    }
}

if (!function_exists('profil_sekolah')) {
    /**
     * Retrieve the current school profile (cached per request).
     */
    function profil_sekolah(string $key = null, mixed $default = null): mixed
    {
        static $profil = null;

        if ($profil === null) {
            try {
                $db = \App\Core\Database::getInstance();
                $profil = $db->fetchOne("SELECT * FROM profil_sekolah WHERE id = 1 LIMIT 1") ?: [];
            } catch (\Exception $e) {
                $profil = [];
            }
        }

        if ($key === null) {
            return $profil;
        }

        return $profil[$key] ?? $default;
    }
}
