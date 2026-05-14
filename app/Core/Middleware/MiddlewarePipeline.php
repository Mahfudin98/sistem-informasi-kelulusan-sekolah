<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Request;

/**
 * Middleware Pipeline
 *
 * Resolves middleware class names from the App\Middleware namespace
 * and runs them in order around the final handler (like an onion).
 */
final class MiddlewarePipeline
{
    /** @param string[] $middleware */
    public function __construct(private readonly array $middleware) {}

    public function run(Request $request, callable $final): void
    {
        $handler = $final;

        foreach (array_reverse($this->middleware) as $middlewareClass) {
            $fqcn    = "App\\Middleware\\{$middlewareClass}";
            $mw      = new $fqcn();
            $next    = $handler;
            $handler = fn(Request $req) => $mw->handle($req, $next);
        }

        $handler($request);
    }
}
