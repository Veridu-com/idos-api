<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Middleware\Auth;

use Slim\App;
use Test\Functional\AbstractFunctional;

abstract class AbstractAuthFunctional extends AbstractFunctional {
    /**
     * Slim's Application Instance.
     *
     * @var \Slim\App
     */
    private $app;

    /**
     * Load all the dependencies for the aplication.
     *
     * @return Slim\App $app
     */
    protected function getApp() : App {
        if ($this->app) {
            return $this->app;
        }

        $app = new App(
            ['settings' => $GLOBALS['appSettings']]
        );

        require_once __ROOT__ . '/../config/dependencies.php';
        require_once __ROOT__ . '/../config/handlers.php';

        $this->app = $app;

        return $app;
    }
}
