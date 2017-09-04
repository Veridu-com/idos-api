<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Middleware\Auth\Credential;

use App\Helper\Token;
use App\Middleware\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Test\Functional\Middleware\Auth\AbstractAuthFunctional;

class InvalidTokenSign extends AbstractAuthFunctional {
    protected function setUp() {
        parent::setUp();
        $this->uri           = '/testInvalidTokenSign';
        $this->httpMethod    = 'GET';
        $this->middlewareApp = $this->getApp();
    }

    public function testInvalidTokenSign() {
        $token = Token::generateCredentialToken(
            'public',
            'b16c931c061e14af275bd2c86d3cf48d',
            'invalid-service-private'
        );

        $authMiddleware = $this->middlewareApp
            ->getContainer()
            ->get('authMiddleware');
        $this->middlewareApp
            ->get(
                '/testInvalidTokenSign', function (ServerRequestInterface $request, ResponseInterface $response) {
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
        $this->assertSame('Token Verification Failed', $body['error']['message']);
    }
}
