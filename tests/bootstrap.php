<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('VIEW_PATH', APP_PATH . '/Views');

require_once ROOT_PATH . '/vendor/autoload.php';

// Load .env for testing
if (file_exists(ROOT_PATH . '/.env')) {
    $dotenv = \Dotenv\Dotenv::createImmutable(ROOT_PATH);
    $dotenv->load(); // Use load() to throw exception if it fails
}

// Set testing environment
$_ENV['APP_ENV'] = 'testing';
// $_ENV['DB_NAME'] = env('DB_NAME') . '_test'; // Create this DB first if you want isolation
