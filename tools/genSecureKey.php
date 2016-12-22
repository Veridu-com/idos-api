#!/usr/bin/env php
<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Loads .env if available
if (is_file(__DIR__ . '/../.env')) {
    $dotEnv = new Dotenv\Dotenv(__DIR__ . '/../');
    $dotEnv->load();
}

// Load application settings
require_once __DIR__ . '/../config/settings.php';

use Defuse\Crypto\KeyProtectedByPassword;

// key location
define('OUTPUT', __DIR__ . '/../resources/secure.key');

if (is_file(OUTPUT)) {
    echo 'A "secure.key" already exists, leaving.', PHP_EOL;
    exit;
}

if (empty($appSettings['secure'])) {
    echo 'Your secure passphrase is empty!', PHP_EOL;
    echo 'Set IDOS_SECURE_KEY in your environment.', PHP_EOL;
    exit;
}

echo 'Generating key..', PHP_EOL;

$key = KeyProtectedByPassword::createRandomPasswordProtectedKey($appSettings['secure']);
echo 'Key generated!', PHP_EOL;

file_put_contents(OUTPUT, $key->saveToAsciiSafeString());
echo 'Key saved!', PHP_EOL;
echo 'Location: ', OUTPUT, PHP_EOL;
