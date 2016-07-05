<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Middleware;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Watcher Middleware.
 *
 * I'm yet to figure out what this is.
 */
class Watcher {
    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next) {
        $response = $next($request, $response);

        return $response->withHeader('X-Watcher', 'was-here');
    }
}
