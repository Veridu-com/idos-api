<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

date_default_timezone_set('UTC');
setlocale(LC_ALL, 'en_US.UTF8');
mb_http_output('UTF-8');
mb_internal_encoding('UTF-8');

require_once __DIR__ . '/../vendor/autoload.php';

// Loads .env if available
$dotEnv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotEnv->load();

// Load application settings
require_once __DIR__ . '/../config/settings.php';

// Increase error reporting
if ($appSettings['debug']) {
    ini_set('display_errors', 'On');
    error_reporting(-1);
}

// Application Setup
$app = new Slim\App(
    ['settings' => $appSettings]
);

// Dependency Injection
require_once __DIR__ . '/../config/dependencies.php';

// Application Middleware
require_once __DIR__ . '/../config/middleware.php';

// Command Handlers
require_once __DIR__ . '/../config/handlers.php';

// Route definition
require_once __DIR__ . '/../config/routes.php';

// Event Listeners
require_once __DIR__ . '/../config/listeners.php';

// Application Execution
$app->run();
