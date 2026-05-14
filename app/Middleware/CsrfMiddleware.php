<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Middleware\MiddlewareInterface;

/**
 * CSRF Middleware
 *
 * Validates the CSRF token on all mutating HTTP methods.
 */
final class CsrfMiddleware implements MiddlewareInterface
{
    private const SAFE_METHODS = ['GET', 'HEAD', 'OPTIONS'];

    public function handle(Request $request, callable $next): void
    {
        if (in_array($request->method, self::SAFE_METHODS, true)) {
            $next($request);
            return;
        }

        $token = $request->body['_token'] ?? $request->headers['X-Csrf-Token'] ?? '';

        if (!Session::validateCsrf($token)) {
            if ($request->isAjax()) {
                Response::json(['error' => true, 'message' => 'CSRF token mismatch.'], 419);
            }

            Response::abort(419, 'CSRF token mismatch.');
        }

        $next($request);
    }
}
