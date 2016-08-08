<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Jenssegers\Optimus\Optimus;

/**
 * OptimusDecode Middleware.
 *
 * Scope: App.
 * This middleware is responsible decode all ".*Id" attributes that are going through the router.
 *
 */
class OptimusDecode implements MiddlewareInterface {
    private $optimus;

    public function __construct(Optimus $optimus) {
        $this->optimus      = $optimus;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param callable                                 $next
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) : ResponseInterface {
        $routeParams = ($request->getAttributes())['routeInfo'][2];

        foreach ($routeParams as $key => $value) {
            if (preg_match('/.*?Id$/', $key)) {
                $request = $request->withAttribute(sprintf('decoded%s', ucfirst($key)) , $this->optimus->decode($value));
            }
        }
        
        return $next($request, $response);
    }
}
