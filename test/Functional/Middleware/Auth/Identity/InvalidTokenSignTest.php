<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Middleware\Auth\Identity;

use App\Helper\Token;
use App\Middleware\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Test\Functional\Middleware\Auth\AbstractAuthFunctional;

class InvalidTokenSignTest extends AbstractAuthFunctional {
    protected function setUp() {
        $this->middlewareApp = parent::getApp();
        parent::setUp();
        $this->httpMethod = 'GET';
        $this->uri        = '/testInvalidTokenSign';
    }

    public function testInvalidTokenSign() {
        $token = Token::generateIdentityToken(
            md5('5d41402abc4b2a76b9719d911017c592'),
            md5('invalid-private')
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
            ->add($authMiddleware(Auth::IDENTITY));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'identityToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame(
            'Invalid Identity',
            $body['error']['message']
        );
    }
}
