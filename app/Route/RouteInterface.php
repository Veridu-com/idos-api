<?php

declare(strict_types=1);
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Route;

use Slim\App;

/**
 * Route Interface.
 */
interface RouteInterface {
    /**
     * Registers the Routes on the Application Route Manager.
     *
     * @param \Slim\App $app
     *
     * @return void
     */
    public static function register(App $app);
    /**
     * Returns all public route names.
     *
     * @return array
     */
    public static function getPublicNames();
}
