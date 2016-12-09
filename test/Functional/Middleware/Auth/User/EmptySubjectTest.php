<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Middleware\Auth\User;

use App\Helper\Token;
use App\Middleware\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Test\Functional\Middleware\Auth\AbstractAuthFunctional;

class EmptySubjectTest extends AbstractAuthFunctional {
    protected function setUp() {
        $this->middlewareApp = parent::getApp();
        $this->httpMethod    = 'GET';
        $this->uri           = '/testEmptySubject';
    }

    public function testEmptySubject() {
        $token = Token::generateUserToken(
            '',
            md5('public'),
            md5('private')
        );

        $authMiddleware = $this->middlewareApp
            ->getContainer()
            ->get('authMiddleware');
        $this->middlewareApp
            ->get(
                '/testEmptySubject', function (ServerRequestInterface $request, ResponseInterface $response) {
                    return $response;
                }
            )
            ->add($authMiddleware(Auth::USER));

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'QUERY_STRING' => 'userToken=' . $token
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);
        $this->assertSame('Missing Subject Claim', $body['error']['message']);
    }
}
