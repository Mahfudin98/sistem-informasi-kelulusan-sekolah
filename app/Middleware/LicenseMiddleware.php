<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Services\LicenseService;

class LicenseMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        // Skip license check for the license-error page to avoid infinite loop
        if ($request->getPath() === '/license-error') {
            return $next($request);
        }

        if (!LicenseService::check()) {
            header('Location: /license-error');
            exit;
        }

        return $next($request);
    }
}
