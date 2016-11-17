<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

define('__VERSION__', '1.0');
define('__ROOT__', __DIR__);

date_default_timezone_set('UTC');
setlocale(LC_ALL, 'en_US.UTF8');
mb_http_output('UTF-8');
mb_internal_encoding('UTF-8');

/**
 * Composer's autoload.
 */
require_once __DIR__ . '/../vendor/autoload.php';

// Loads .env if available
$dotEnv = new Dotenv\Dotenv(__DIR__ . '/../', '.env.testing');
$dotEnv->overload();

// Load application settings
require_once __DIR__ . '/../config/settings.php';
