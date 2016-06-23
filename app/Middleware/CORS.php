<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * CORS Middleware.
 *
 * Cross Origin Resource Sharing header control.
 *
 * @link https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
 */
class CORS {
    private $methods;

    public function __construct(array $methods = []) {
        if (! in_array('OPTIONS', $methods))
            $methods[] = 'OPTIONS';
        $this->methods = $methods;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next) {
        if (! empty($request->getHeaderLine('Origin')))
            $response = $response
                ->withHeader(
                    'Access-Control-Allow-Origin',
                    $request->getHeaderLine('Origin')
                )
                ->withHeader(
                    'Access-Control-Max-Age',
                    '3628800'
                )
                ->withHeader(
                    'Access-Control-Allow-Credentials',
                    'true'
                )
                ->withHeader(
                    'Access-Control-Allow-Methods',
                    implode(',', $this->methods)
                )
                ->withHeader(
                    'Access-Control-Allow-Headers',
                    'Authorization, Content-Type, If-Modified-Since, If-None-Match, X-Requested-With'
                )
                ->withHeader(
                    'Access-Control-Expose-Headers',
                    'ETag, X-Rate-Limit-Limit, X-Rate-Limit-Remaining, X-Rate-Limit-Reset'
                );

        return $next($request, $response);
    }
}
