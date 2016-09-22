<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Middleware\Auth;

use Phinx\Console\PhinxApplication;
use Phinx\Wrapper\TextWrapper;
use Slim\App;
use Test\Functional\AbstractFunctional;

abstract class AbstractAuthFunctional extends AbstractFunctional {
    /**
     * Runs one time before any method of the Child classes.
     */
    public static function setUpBeforeClass() {
        if (! self::$app) {
            $app = new App(
                ['settings' => $GLOBALS['appSettings']]
            );

            require_once __ROOT__ . '/../config/dependencies.php';
            require_once __ROOT__ . '/../config/handlers.php';

            self::$app = $app;
        }

        if (! self::$phinxApp) {
            $phinxApp = new PhinxApplication();

            self::$phinxApp = $phinxApp;
        }

        if (! self::$phinxTextWrapper) {
            $phinxTextWrapper = new TextWrapper(self::$phinxApp);
            $phinxTextWrapper->setOption('configuration', 'phinx.yml');
            $phinxTextWrapper->setOption('parser', 'YAML');
            $phinxTextWrapper->setOption('environment', 'testing');
            $phinxTextWrapper->getRollback('testing', 0);
            $phinxTextWrapper->getMigrate();
            $phinxTextWrapper->getSeed();

            self::$phinxTextWrapper = $phinxTextWrapper;
        }

        if (! self::$sqlConnection) {
            self::$sqlConnection = self::$app->getContainer()->get('sql');
        }

        if (! self::$noSqlConnection) {
            self::$noSqlConnection = self::$app->getContainer()->get('nosql');
        }
    }
}
