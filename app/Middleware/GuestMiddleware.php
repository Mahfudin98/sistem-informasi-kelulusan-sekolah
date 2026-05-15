<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Middleware\MiddlewareInterface;

/**
 * Guest Middleware
 *
 * Redirects authenticated users away from guest-only pages (e.g. login).
 */
final class GuestMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): void
    {
        if (Session::has('user')) {
            Response::redirect('/admin/dashboard');
        }

        $next($request);
    }
}
