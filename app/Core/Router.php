<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Request;
use App\Core\Response;
use App\Core\Middleware\MiddlewarePipeline;

/**
 * Router
 *
 * Supports named routes, parameter segments (:id), optional middleware
 * stacks per route group, and controller@method dispatch.
 */
final class Router
{
    /** @var array<string, array<string, array{handler: mixed, middleware: string[]}>> */
    private array $routes = [];

    /** @var array<string, string> */
    private array $namedRoutes = [];

    /** Active group prefix */
    private string $groupPrefix = '';

    /** Active group middleware */
    private array $groupMiddleware = [];

    // ── Route Registration ───────────────────────────────────────────────────

    public function get(string $path, mixed $handler, array $middleware = [], ?string $name = null): self
    {
        return $this->addRoute('GET', $path, $handler, $middleware, $name);
    }

    public function post(string $path, mixed $handler, array $middleware = [], ?string $name = null): self
    {
        return $this->addRoute('POST', $path, $handler, $middleware, $name);
    }

    public function put(string $path, mixed $handler, array $middleware = [], ?string $name = null): self
    {
        return $this->addRoute('PUT', $path, $handler, $middleware, $name);
    }

    public function patch(string $path, mixed $handler, array $middleware = [], ?string $name = null): self
    {
        return $this->addRoute('PATCH', $path, $handler, $middleware, $name);
    }

    public function delete(string $path, mixed $handler, array $middleware = [], ?string $name = null): self
    {
        return $this->addRoute('DELETE', $path, $handler, $middleware, $name);
    }

    /**
     * Group routes under a shared prefix and/or middleware.
     */
    public function group(string $prefix, callable $callback, array $middleware = []): void
    {
        $previousPrefix     = $this->groupPrefix;
        $previousMiddleware = $this->groupMiddleware;

        $this->groupPrefix     = $previousPrefix . $prefix;
        $this->groupMiddleware = array_merge($previousMiddleware, $middleware);

        $callback($this);

        $this->groupPrefix     = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    // ── Route Resolution ─────────────────────────────────────────────────────

    public function dispatch(Request $request): void
    {
        $method = $request->method;
        $path   = rtrim($request->path, '/') ?: '/';

        foreach ($this->routes[$method] ?? [] as $pattern => $route) {
            $params = $this->match($pattern, $path);

            if ($params !== null) {
                $this->handle($route, $request, $params);
                return;
            }
        }

        Response::abort(404, "Route not found: {$path}");
    }

    /**
     * Generate a URL for a named route.
     *
     * @param array<string, string> $params
     */
    public function route(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \InvalidArgumentException("Route [{$name}] not defined.");
        }

        $url = $this->namedRoutes[$name];

        foreach ($params as $key => $value) {
            $url = str_replace(":{$key}", (string) $value, $url);
        }

        return $url;
    }

    // ── Internals ────────────────────────────────────────────────────────────

    private function addRoute(
        string  $method,
        string  $path,
        mixed   $handler,
        array   $middleware,
        ?string $name,
    ): self {
        $full = $this->groupPrefix . $path;
        $full = rtrim($full, '/') ?: '/';
        $mid  = array_merge($this->groupMiddleware, $middleware);

        $this->routes[$method][$full] = [
            'handler'    => $handler,
            'middleware' => $mid,
        ];

        if ($name !== null) {
            $this->namedRoutes[$name] = $full;
        }

        return $this;
    }

    /**
     * Match a route pattern against the incoming path.
     *
     * Returns an associative array of captured parameters on match,
     * or null on mismatch.
     *
     * @return array<string, string>|null
     */
    private function match(string $pattern, string $path): ?array
    {
        // Convert :param segments to named capture groups
        $regex = preg_replace('/\/:([^\/]+)/', '/(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $path, $matches)) {
            return null;
        }

        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }

    /**
     * Run middleware pipeline and invoke the handler.
     */
    private function handle(array $route, Request $request, array $params): void
    {
        $pipeline = new MiddlewarePipeline($route['middleware']);

        $pipeline->run($request, function (Request $req) use ($route, $params): void {
            $this->invokeHandler($route['handler'], $req, $params);
        });
    }

    /**
     * Invoke controller@method or a callable handler.
     */
    private function invokeHandler(mixed $handler, Request $request, array $params): void
    {
        if (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler, 2);

            $fqcn       = "App\\Controllers\\{$class}";
            $controller = new $fqcn();

            $controller->$method($request, $params);
            return;
        }

        if (is_callable($handler)) {
            $handler($request, $params);
            return;
        }

        throw new \RuntimeException("Invalid route handler.");
    }
}
