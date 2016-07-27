<?php

declare(strict_types=1);
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Middleware;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * App Middleware
 * This middleware is responsible to throw an Exception if any route is not being analysed by the "Permission Middleware".
 * Whether it is public or not the Route Middleware "Permission" must be run.
 */
class GateKeeper implements MiddlewareInterface {
    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next) : ResponseInterface {
        $response = $next($request, $response);

        if (! $response->hasHeader('Allowed')) {
            // Unauthorizes requests that doesn't have the 'Allowed' header
            throw new \Exception("'Allowed' header not found, add the Permission Middleware to this Route");
        }

        return $response;
    }
}
