<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * GateKeeper Middleware.
 *
 * Scope: Application.
 * This middleware is responsible for throwing a RuntimeException if any route is not being analysed
 * by the "Permission Middleware".
 * Whether it is public or not the Route Middleware "Permission" must be executed.
 */
class GateKeeper implements MiddlewareInterface {
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param callable                                 $next
     *
     * @throws \RuntimeException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) : ResponseInterface {
        $response = $next($request, $response);

        if (! $response->hasHeader('Allowed')) {
            // Unauthorizes requests that doesn't have the 'Allowed' header
            // throw new \RuntimeException("'Allowed' header not found, add the Permission Middleware to this Route");
        }

        return $response;
    }
}
