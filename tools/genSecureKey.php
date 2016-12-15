#!/usr/bin/env php
<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

// Set the key password (must be set in the settings too!)
define('PASSWORD', 'set-your-password-here');
// Set the output location of your key
define('OUTPUT', __DIR__ . '/../resources/secure.key');

/*
 * DO NOT CHANGE ANYTHING BELOW THIS LINE
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Defuse\Crypto\KeyProtectedByPassword;

echo 'Generating key..', PHP_EOL;

$key = KeyProtectedByPassword::createRandomPasswordProtectedKey(PASSWORD);
file_put_contents(OUTPUT, $key->saveToAsciiSafeString());

echo 'Key generated and saved!', PHP_EOL;
echo 'Key location: ', OUTPUT, PHP_EOL;
