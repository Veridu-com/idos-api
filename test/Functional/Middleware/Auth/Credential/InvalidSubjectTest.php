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
use Slim\Http\Response;
use Test\Functional\Middleware\Auth\AbstractAuthFunctional;
use Phinx\Console\PhinxApplication;
use Phinx\Wrapper\TextWrapper;

class InvalidSubjectTest extends AbstractAuthFunctional {

    protected function setUp() {
        $this->middlewareApp = parent::getApp();
        $this->uri = '/testInvalidSubject';
        $this->httpMethod = 'GET';
    }

    public function testInvalidSubject() {
        $token = Token::generateCredentialToken(
            md5('invalid-credential-public'),
            md5('public-1'),
            md5('private-1')
        );
        $app = $this->middlewareApp;
        $authMiddleware = $this->middlewareApp
            ->getContainer()['authMiddleware'];
        $this->middlewareApp
            ->get(
                '/testInvalidSubject', function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {
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
        $this->assertSame('Invalid Credential', $body['error']['message']);
    }
}