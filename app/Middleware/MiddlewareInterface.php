<?php

declare(strict_types=1);
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface MiddlewareInterface {
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next) : ResponseInterface;
}
