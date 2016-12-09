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
    protected $middlewareApp;
    protected $middlewarePhinxApp;
    protected $middlewarePhinxTextWrapper;

    public function getApp() : \Slim\App {
        if ((! $this->middlewareApp) || (empty($this->middlewareApp))) {
            $app = new App(
                ['settings' => $GLOBALS['appSettings']]
            );

            require __ROOT__ . '/../config/dependencies.php';
            require __ROOT__ . '/../config/handlers.php';

            $this->middlewareApp = $app;
        }

        if (! $this->middlewarePhinxApp) {
            $phinxApp = new PhinxApplication();

            $this->middlewarePhinxApp = $phinxApp;
        }

        if (! $this->middlewarePhinxTextWrapper) {
            $phinxTextWrapper = new TextWrapper($this->middlewarePhinxApp);
            $phinxTextWrapper->setOption('configuration', 'phinx.yml');
            $phinxTextWrapper->setOption('parser', 'YAML');
            $phinxTextWrapper->setOption('environment', 'testing');
            $phinxTextWrapper->getRollback('testing', 0);
            $phinxTextWrapper->getMigrate();
            $phinxTextWrapper->getSeed();
            
            $this->middlewarePhinxTextWrapper = $phinxTextWrapper;
        }

        if (! self::$sqlConnection) {
            self::$sqlConnection = $this->middlewareApp->getContainer()->get('sql');
        }

        if (! self::$noSqlConnection) {
            self::$noSqlConnection = $this->middlewareApp->getContainer()->get('nosql');
        }

        return $this->middlewareApp;
    }

    // public function tearDown() {
    //     $this->middlewareApp = [];
    // }
}
