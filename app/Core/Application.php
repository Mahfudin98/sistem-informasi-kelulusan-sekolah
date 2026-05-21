<?php

declare(strict_types=1);

namespace App\Core;

use Dotenv\Dotenv;
use App\Core\Router;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;
use App\Core\ExceptionHandler;

/**
 * Application Bootstrap
 *
 * Initialises all core services, loads the environment,
 * starts a session and dispatches the current request.
 */
final class Application
{
    private Router $router;

    public function __construct()
    {
        $this->loadEnvironment();
        $this->configurePhp();
        ExceptionHandler::register();
        Session::start();
        $this->router = new Router();
    }

    /**
     * Load .env variables using vlucas/phpdotenv.
     */
    private function loadEnvironment(): void
    {
        if (!file_exists(ROOT_PATH . '/.env')) {
            return; // Skip loading, will be handled by installer
        }

        $dotenv = Dotenv::createImmutable(ROOT_PATH);
        $dotenv->load();

        $dotenv->required([
            'APP_NAME', 'APP_ENV', 'APP_URL',
            'DB_HOST', 'DB_NAME', 'DB_USER',
        ]);
    }

    /**
     * Apply runtime PHP configuration derived from .env.
     */
    private function configurePhp(): void
    {
        date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');

        $isDebug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($isDebug) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(0);
            ini_set('display_errors', '0');
        }

        ini_set('log_errors', '1');
        ini_set('error_log', STORAGE_PATH . '/logs/php_errors.log');
    }

    /**
     * Load route definitions and dispatch the request.
     */
    public function run(): void
    {
        $request = new Request();
        
        // --- INSTALLER INTERCEPT ---
        if (!file_exists(ROOT_PATH . '/.env')) {
            require_once ROOT_PATH . '/routes/installer.php';
            exit;
        }
        // ---------------------------

        // --- LICENSE LOCK ---
        $exemptPaths = ['/license-error', '/setup', '/api/license/sync'];

        if (!in_array($request->path, $exemptPaths)) {
            $status = \App\Services\LicenseService::checkStatus();
            if ($status === 'missing') {
                header('Location: /setup');
                exit;
            } elseif ($status === 'invalid') {
                header('Location: /license-error');
                exit;
            }
        }
        // --------------------

        $router = $this->router;
        require_once ROOT_PATH . '/routes/web.php';
        $this->router->dispatch($request);
    }
}
