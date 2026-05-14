<?php

declare(strict_types=1);

/**
 * Application Entry Point
 *
 * All HTTP requests are routed through this file.
 * Apache/Nginx rewrites ensure this is always hit.
 */

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('VIEW_PATH', APP_PATH . '/Views');
define('START_TIME', microtime(true));

// ── Composer Autoloader ──────────────────────────────────────────────────────
require_once ROOT_PATH . '/vendor/autoload.php';

// ── Bootstrap Application ─────────────────────────────────────────────────────
$app = new \App\Core\Application();
$app->run();
