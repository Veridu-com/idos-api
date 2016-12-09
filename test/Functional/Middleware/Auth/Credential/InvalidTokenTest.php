<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Middleware\Auth\Credential;

use App\Helper\Token;
use App\Middleware\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Test\Functional\Middleware\Auth\AbstractAuthFunctional;

class InvalidTokenTest extends AbstractAuthFunctional {
    protected function setUp() {
        $this->middlewareApp = parent::getApp();
        $this->uri           = '/testInvalidToken';
        $this->httpMethod    = 'GET';
    }

    public function testInvalidToken() {
        $token = 'invalid.token';

        $authMiddleware = $this->middlewareApp
            ->getContainer()
            ->get('authMiddleware');
        $this->middlewareApp
            ->get(
                '/testInvalidToken', function (ServerRequestInterface $request, ResponseInterface $response) {
                    return $response;
                }
            )
            ->add($authMiddleware(Auth::CREDENTIAL));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                'QUERY_STRING' => 'credentialToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame('Invalid Token', $body['error']['message']);
    }
}
