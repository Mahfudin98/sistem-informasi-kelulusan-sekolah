<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Request;

/**
 * Contract every middleware must implement.
 */
interface MiddlewareInterface
{
    public function handle(Request $request, callable $next): void;
}
