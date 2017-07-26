<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Middleware;

use Jenssegers\Optimus\Optimus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * OptimusDecode Middleware.
 *
 * Scope: App.
 * This middleware is responsible decode all ".*Id" attributes that are going through the router.
 */
class OptimusDecode implements MiddlewareInterface {
    private $optimus;

    /**
     * Gets the decoded name of a key.
     *
     * @param string $key The key
     *
     * @return string The decoded name
     */
    private function getDecodedName(string $key) : string {
        return sprintf('decoded%s', ucfirst($key));
    }

    /**
     * Gets the decoded name of a parsed body key.
     *
     * @param string $key The key
     *
     * @return string The decoded name
     */
    private function getDecodedBodyName(string $key) : string {
        return sprintf('decoded_%s', $key);
    }

    /**
     * Test if the key should be decoded.
     *
     * @param string $key The key
     *
     * @return bool
     */
    private function matchDecodableKey(string $key) : bool {
        return (bool) preg_match('/Id$/', $key);
    }

    /**
     * Test if the request parsed body key should be decoded.
     *
     * @param string $key The key
     *
     * @return bool
     */
    private function matchDecodableBodyKey(string $key) : bool {
        return (bool) preg_match('/_id$/', $key);
    }

    public function __construct(Optimus $optimus) {
        $this->optimus = $optimus;
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
        $routeParams = $request->getAttribute('routeInfo');

        // decode route params
        if ((isset($routeParams[2])) && (is_array($routeParams[2]))) {
            // decode route parameters
            foreach ($routeParams[2] as $key => $value) {
                if ($this->matchDecodableKey($key)) {
                    $request = $request->withAttribute(
                        $this->getDecodedName($key),
                        $this->optimus->decode($value)
                    );
                }
            }
        }

        // decode request body parameters
        $parsedBody = $request->getParsedBody();

        if (is_array($parsedBody)) {
            // adds decoded values to $parsedBody
            foreach ($parsedBody as $key => $value) {
                if ($value === null) {
                    continue;
                }

                if (is_string($key)) {
                    // object request
                    if ($this->matchDecodableBodyKey($key)) {
                        $parsedBody[$this->getDecodedBodyName($key)] = $this->optimus->decode($value);
                    }
                } else {
                    // array request
                    foreach ($value as $objKey => $objValue) {
                        if ($objValue === null) {
                            continue;
                        }

                        if ($this->matchDecodableBodyKey($objKey)) {
                            $parsedBody[$key][$this->getDecodedBodyName($objKey)] = $this->optimus->decode($objValue);
                        }
                    }
                }
            }

            // add decoded values to the request
            $request = $request->withParsedBody($parsedBody);
        }

        return $next($request, $response);
    }
}
