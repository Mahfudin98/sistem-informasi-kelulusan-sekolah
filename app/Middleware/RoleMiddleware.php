<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Middleware\MiddlewareInterface;

/**
 * Role Middleware
 *
 * Restricts route access to users with a specific role.
 * Usage in route: ['RoleMiddleware:admin'] — pass role as a constructor arg.
 *
 * Note: For simplicity this reads the role from Session::get('user')['role'].
 */
final class RoleMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly string $role = '') {}

    public function handle(Request $request, callable $next): void
    {
        $user = Session::get('user');

        if (!$user || ($user['role'] ?? '') !== $this->role) {
            Response::abort(403, 'Akses ditolak.');
        }

        $next($request);
    }
}
