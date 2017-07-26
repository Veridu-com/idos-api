<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

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
    public static function register(App $app) : void;
    /**
     * Returns all public route names.
     *
     * @return array
     */
    public static function getPublicNames() : array;
}
