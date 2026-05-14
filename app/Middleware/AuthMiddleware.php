<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Middleware\MiddlewareInterface;

/**
 * Auth Middleware
 *
 * Redirects unauthenticated users to the login page.
 */
final class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): void
    {
        if (!Session::has('user')) {
            Session::flash('error', 'Silakan login terlebih dahulu.');
            Response::redirect('/login');
        }

        $next($request);
    }
}
