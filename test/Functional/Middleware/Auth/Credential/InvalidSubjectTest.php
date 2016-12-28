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

class InvalidSubjectTest extends AbstractAuthFunctional {
    protected function setUp() {
        $this->middlewareApp = parent::getApp();
        $this->uri           = '/testInvalidSubject';
        $this->httpMethod    = 'GET';
    }

    public function testInvalidSubject() {
        $token = Token::generateCredentialToken(
            'invalid-subject-public',
            'b16c931c061e14af275bd2c86d3cf48d',
            '81197557e9117dfd6f16cb72a2710830'
        );
        $app            = $this->middlewareApp;
        $authMiddleware = $this->middlewareApp
            ->getContainer()['authMiddleware'];
        $this->middlewareApp
            ->get(
                '/testInvalidSubject', function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {
                    return $response;
                }
            )
            ->add($authMiddleware(Auth::CREDENTIAL));

        // var_dump($token);
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
        $this->assertSame('Invalid Credential', $body['error']['message']);
    }
}
